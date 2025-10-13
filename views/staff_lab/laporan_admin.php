<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Hanya admin yang dapat mengakses laporan ini.");
}

require_once('C:/xampp/htdocs/praktikum_system/libraries/fpdf/fpdf.php');

$host = 'localhost';
$dbname = 'praktikum_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// FUNCTION: Untuk mendapatkan path gambar tanda tangan
function getSignaturePath($signature_data) {
    $base_dir = realpath(__DIR__ . '/../../uploads/absen_asisten/');
    if (!$base_dir) return null;

    $filename = basename($signature_data);
    $full_path = $base_dir . DIRECTORY_SEPARATOR . $filename;

    // JANGAN ADA ECHO DI SINI
    if (file_exists($full_path)) {
        return $full_path;
    }
    return null;
}



// ========== FUNGSI BARU UNTUK CETAK ROW ==========
function cetakRow($pdf, $no, $asisten, $absensi_per_pertemuan, $start, $end) {
    global $col_no, $col_nim, $col_nama, $col_praktikum, $col_pertemuan, $col_total;

    $pdf->Cell($col_no, 8, $no, 1, 0, 'C');
    $pdf->Cell($col_nim, 8, $asisten['nim'], 1, 0, 'C');
    $pdf->Cell($col_nama, 8, $asisten['nama'], 1, 0, 'L');
    $pdf->Cell($col_praktikum, 8, $asisten['nama_praktikum'] . " (" . ($asisten['kelas'] ?? '-') . ")", 1, 0, 'L');

    $total = 0;
    for ($p = $start; $p <= $end; $p++) {
        $absensi_p = $absensi_per_pertemuan[$p] ?? null;
        $hadir = $absensi_p && $absensi_p['status_hadir'] == 'hadir';

        if ($hadir && !empty($absensi_p['signature_data'])) {
            $signature_path = getSignaturePath($absensi_p['signature_data']);
            if ($signature_path) {
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                // TAMBAHKAN BORDER CELL DULU
                $pdf->Cell($col_pertemuan, 8, '', 1, 0, 'C');
                // KEMUDIAN GAMBAR TANDA TANGAN DI ATASNYA
                $pdf->Image($signature_path, $x + 1.5, $y + 1, $col_pertemuan - 3, 7);
            } else {
                $pdf->Cell($col_pertemuan, 8, 'TTD', 1, 0, 'C');
            }
            $total++;
        } else {
            $pdf->Cell($col_pertemuan, 8, '', 1, 0, 'C');
        }
    }

    // PASTIKAN TOTAL TIDAK TERPOTONG
    $pdf->Cell($col_total, 8, $total, 1, 1, 'C');
}


 class PDF extends FPDF {
    public $nama_praktikum = '';

    // Footer otomatis tiap halaman
    function Footer() {
        // posisi 15 mm dari bawah
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);

        // pakai tanda minus '-' agar tidak bermasalah encoding
        $footerText = 'Absensi Asisten Praktikum ' .
                      ' - Halaman ' . $this->PageNo() . ' dari {nb}';

        $this->Cell(0, 10, $footerText, 0, 0, 'C');
    }
}




// PROSES GENERATE PDF
if (isset($_GET['pdf'])) {
    // Ambil parameter filter
    $praktikum_id = isset($_GET['praktikum_id']) ? $_GET['praktikum_id'] : null;
    $kelas = isset($_GET['kelas']) ? $_GET['kelas'] : null;
    $tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '2025/2026';
    $semester = isset($_GET['semester']) ? $_GET['semester'] : 'Genap';
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '2025-01-01';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '2025-12-31';

    // VALIDASI: Harus pilih praktikum
    if (empty($praktikum_id)) {
        die("Silakan pilih praktikum terlebih dahulu!");
    }

    try {
        // QUERY 1: Ambil DAFTAR ASPRAK dengan filter kelas
        $query_asisten = "SELECT DISTINCT 
                            ap.nim, 
                            ap.nama,
                            ap.kelas,
                            p.nama_praktikum,
                            p.id as praktikum_id
                          FROM asisten_praktikum ap 
                          JOIN praktikum p ON ap.praktikum_id = p.id 
                          WHERE ap.praktikum_id = :praktikum_id 
                          AND ap.status = 'active'";

        $params_asisten = [':praktikum_id' => $praktikum_id];
        
        if (!empty($kelas) && $kelas != 'all') {
            $query_asisten .= " AND ap.kelas = :kelas";
            $params_asisten[':kelas'] = $kelas;
        }
        
        $query_asisten .= " ORDER BY ap.nama";

        $stmt_asisten = $pdo->prepare($query_asisten);
        $stmt_asisten->execute($params_asisten);
        $asisten_list = $stmt_asisten->fetchAll(PDO::FETCH_ASSOC);

        if (empty($asisten_list)) {
            die("Tidak ada asisten untuk praktikum ini!");
        }

        // QUERY 2: Ambil DATA ABSENSI dengan signature_data
        $nim_list = array_column($asisten_list, 'nim');
        $placeholders = str_repeat('?,', count($nim_list) - 1) . '?';
        
        $query_absensi = "SELECT 
                            aa.nim, 
                            aa.nama, 
                            aa.praktikum_name,
                            aa.kelas, 
                            aa.pertemuan, 
                            aa.tanggal,
                            aa.jam_mulai,
                            aa.jam_akhir,
                            aa.status_hadir,
                            aa.signature_data
                          FROM absen_asisten aa 
                          WHERE aa.nim IN ($placeholders)
                          AND aa.tanggal BETWEEN ? AND ?
                          AND aa.pertemuan IN ('Briefing', '1', '2', '3', '4', '5', '6', '7', '8', '9', '8', '11', '12', '13', '14', 'Presentasi Tugas Akhir', 'Pengisian Nilai Akhir')
                          ORDER BY 
                            CASE 
                              WHEN aa.pertemuan = 'Briefing' THEN 0
                              WHEN aa.pertemuan = 'Presentasi Tugas Akhir' THEN 15
                              WHEN aa.pertemuan = 'Pengisian Nilai Akhir' THEN 16
                              ELSE CAST(aa.pertemuan AS UNSIGNED)
                            END,
                            aa.tanggal";
        
        $absensi_params = $nim_list;
        $absensi_params[] = $start_date;
        $absensi_params[] = $end_date;
        
        if (!empty($kelas) && $kelas != 'all') {
            $query_absensi .= " AND aa.kelas = ?";
            $absensi_params[] = $kelas;
        }
        
        $stmt_absensi = $pdo->prepare($query_absensi);
        $stmt_absensi->execute($absensi_params);
        $data_absensi = $stmt_absensi->fetchAll(PDO::FETCH_ASSOC);

        // Organisasi data
        $data_organized = [];
        foreach ($asisten_list as $asisten) {
            $data_organized[$asisten['nim']] = [
                'info' => $asisten,
                'absensi' => []
            ];
        }
        
        foreach ($data_absensi as $absensi) {
            $nim = $absensi['nim'];
            if (isset($data_organized[$nim])) {
                $data_organized[$nim]['absensi'][] = $absensi;
            }
        }

        // Ambil info tanggal & jam untuk setiap pertemuan
        $info_pertemuan = [];
        foreach ($data_absensi as $absensi) {
            $pertemuan = $absensi['pertemuan'];
            if (!isset($info_pertemuan[$pertemuan])) {
                $info_pertemuan[$pertemuan] = [
                    'tanggal' => $absensi['tanggal'],
                    'jam_mulai' => $absensi['jam_mulai'],
                    'jam_akhir' => $absensi['jam_akhir']
                ];
            }
        }

        // Include libraries FPDF
        $fpdf_path = $_SERVER['DOCUMENT_ROOT'] . '/praktikum_system/libraries/fpdf/fpdf.php';
        
        if (!file_exists($fpdf_path)) {
            // Coba path alternatif
            $fpdf_path = __DIR__ . '/../../libraries/fpdf/fpdf.php';
        }
        
        if (!file_exists($fpdf_path)) {
            die("File FPDF tidak ditemukan. Pastikan FPDF sudah diinstall di: libraries/fpdf/");
        }
        
        require_once($fpdf_path);

        // Create new PDF document - Landscape
        $pdf = new PDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetMargins(10, 10, 10);     // margin kiri, atas, kanan
        $pdf->AliasNbPages(); // penting supaya {nb} bisa tampil
        

 

        



        
        // Header
        // Header Template FTUP
$pdf->SetFont('Arial', 'B', 12);

// Logo kiri
$pdf->Image('C:/xampp/htdocs/praktikum_system/assets/images/univpancasila.png', 15, 10, 20);

// Logo kanan
$pdf->Image('C:/xampp/htdocs/praktikum_system/assets/images/qan.png', 265, 10, 20);

// Kode FM di atas logo kanan
$pdf->SetXY(240, 4);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(0, 5, 'FM 1-8.5.1-4.420-44.v4', 0, 1, 'R');

// Judul tengah
$pdf->Cell(0, 8, '', 0, 1); // spasi atas
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'FAKULTAS TEKNIK UNIVERSITAS PANCASILA', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'KEHADIRAN ASISTEN PRAKTIKUM / PRAKTEK', 0, 1, 'C');

// Garis pemisah bawah judul
$pdf->Ln(3);
$pdf->Cell(0, 0.5, '', 'T', 1, 'C');
$pdf->Ln(5);

// Informasi dasar
$pdf->SetFont('Arial', '', 9);
$nama_praktikum = $asisten_list[0]['nama_praktikum'] ?? 'Praktikum';
$kelas_display = (!empty($kelas) && $kelas != 'all') ? $kelas : 'A';
$jurusan = 'TEKNIK INFORMATIKA';
$semester_display = strtoupper($semester);
$tahun_ajaran_display = $tahun_ajaran;

// Rata kiri info header
$pdf->SetFont('Arial', '', 9);

$label_width = 30;   // lebar kolom label kiri
$colon_width = 5;    // lebar kolom titik dua
$value_width = 120;  // lebar kolom nilai kanan

$pdf->Cell($label_width, 6, 'JURUSAN', 0, 0);
$pdf->Cell($colon_width, 6, ':', 0, 0);
$pdf->Cell($value_width, 6, 'TEKNIK INFORMATIKA', 0, 1);

$pdf->Cell($label_width, 6, 'SEMESTER', 0, 0);
$pdf->Cell($colon_width, 6, ':', 0, 0);
$pdf->Cell($value_width, 6, strtoupper($semester_display), 0, 1);

$pdf->Cell($label_width, 6, 'TAHUN AKADEMIK', 0, 0);
$pdf->Cell($colon_width, 6, ':', 0, 0);
$pdf->Cell($value_width, 6, $tahun_ajaran_display, 0, 1);

$pdf->Cell($label_width, 6, 'PRAKTIKUM', 0, 0);
$pdf->Cell($colon_width, 6, ':', 0, 0);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($value_width, 6, strtoupper('PRAK. ' . $nama_praktikum . ' (KELAS ' . $kelas_display . ')'), 0, 1);

$pdf->Ln(4);


        // BAGIAN 1: BRIEFING + PRAK 1-5
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 8, 'BAGIAN 1: BRIEFING + PRAKTIKUM 1-5', 0, 1, 'L');
        
        // Table header
        $pdf->SetFont('Arial', 'B', 7);
        
        // Header row 1 - Kolom utama
        $pdf->Cell(8, 25, 'NO', 1, 0, 'C');
        $pdf->Cell(20, 25, 'NIM', 1, 0, 'C');
        $pdf->Cell(40, 25, 'NAMA', 1, 0, 'C');
        $pdf->Cell(50, 25, 'PRAKTIKUM (KELAS)', 1, 0, 'C');
        
        // Briefing header dengan format rapi
        $briefing_date = isset($info_pertemuan['Briefing']) ? date('d/m/Y', strtotime($info_pertemuan['Briefing']['tanggal'])) : '-';
        $briefing_time = isset($info_pertemuan['Briefing']) ? 
            substr($info_pertemuan['Briefing']['jam_mulai'], 0, 5) . ' - ' . substr($info_pertemuan['Briefing']['jam_akhir'], 0, 5) : '-';

        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Rect($x, $y, 25, 25);
        $pdf->SetXY($x, $y + 2);
        $pdf->Cell(25, 5, 'BRIEFING', 0, 0, 'C');
        $pdf->SetXY($x, $y + 9);
        $pdf->Cell(25, 5, $briefing_date, 0, 0, 'C');
        $pdf->SetXY($x, $y + 16);
        $pdf->Cell(25, 5, $briefing_time, 0, 0, 'C');
        $pdf->SetXY($x + 25, $y);

        
        
        // Prak 1-5 headers dengan format rapi
        for ($p = 1; $p <= 5; $p++) {
            $date = isset($info_pertemuan[$p]) ? date('d/m/Y', strtotime($info_pertemuan[$p]['tanggal'])) : '-';
            $time = isset($info_pertemuan[$p]) ? 
                substr($info_pertemuan[$p]['jam_mulai'], 0, 5) . ' - ' . substr($info_pertemuan[$p]['jam_akhir'], 0, 5) : '-';
            
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Rect($x, $y, 25, 25);
            $pdf->SetXY($x, $y + 2);
            $pdf->Cell(25, 5, 'PRAKTIKUM ' . $p, 0, 0, 'C');
            $pdf->SetXY($x, $y + 9);
            $pdf->Cell(25, 5, $date, 0, 0, 'C');
            $pdf->SetXY($x, $y + 16);
            $pdf->Cell(25, 5, $time, 0, 0, 'C');
            $pdf->SetXY($x + 25, $y);
        }
        
        $pdf->Cell(15, 25, 'TOTAL', 1, 1, 'C');

        // Data rows
        $pdf->SetFont('Arial', '', 8);
        $no = 1;
        foreach ($data_organized as $nim => $data) {
            $asisten = $data['info'];
            $absensi_asisten = $data['absensi'];
            
            $absensi_per_pertemuan = [];
            foreach ($absensi_asisten as $absensi) {
                $pertemuan = $absensi['pertemuan'];
                $absensi_per_pertemuan[$pertemuan] = $absensi;
            }
            
            $total_hadir = 0;
            foreach ($absensi_per_pertemuan as $absensi) {
                if ($absensi['status_hadir'] == 'hadir') {
                    $total_hadir++;
                }
            }
            
            $praktikum_display = $asisten['nama_praktikum'] . " (" . ($asisten['kelas'] ?? '-') . ")";
            
            $pdf->Cell(8, 12, $no++, 1, 0, 'C');
            $pdf->Cell(20, 12, $asisten['nim'], 1, 0, 'C');
            $pdf->Cell(40, 12, $asisten['nama'], 1, 0, 'L');
            $pdf->Cell(50, 12, $praktikum_display, 1, 0, 'L');
            
            // Briefing - Gambar Tanda Tangan
            $briefing = isset($absensi_per_pertemuan['Briefing']) ? $absensi_per_pertemuan['Briefing'] : null;
            $hadir_briefing = $briefing && $briefing['status_hadir'] == 'hadir';
            
            if ($hadir_briefing && !empty($briefing['signature_data'])) {
    $signature_path = getSignaturePath($briefing['signature_data']);
    if ($signature_path) {
        // Buat cell dengan border terlebih dahulu
        $pdf->Cell(25, 12, '', 1, 0, 'C');
        
        // Kembali ke posisi cell dan tambahkan gambar
        $x = $pdf->GetX() - 25; // Kembali ke awal cell
        $y = $pdf->GetY();
        $pdf->Image($signature_path, $x + 5, $y + 1, 15, 8);
        
        // Tetap di posisi setelah cell
        $pdf->SetXY($x + 25, $y);
    } else {
        $pdf->Cell(25, 12, 'TTD', 1, 0, 'C');
    }
} else {
    $pdf->Cell(25, 12, '', 1, 0, 'C');
}
            
            // Prak 1-5 - Gambar Tanda Tangan
            for ($p = 1; $p <= 5; $p++) {
    $absensi_p = isset($absensi_per_pertemuan[$p]) ? $absensi_per_pertemuan[$p] : null;
    $hadir_p = $absensi_p && $absensi_p['status_hadir'] == 'hadir';
    
    if ($hadir_p && !empty($absensi_p['signature_data'])) {
        $signature_path = getSignaturePath($absensi_p['signature_data']);
        if ($signature_path) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            
            // Buat cell dengan border terlebih dahulu
            $pdf->Cell(25, 12, '', 1, 0);
            
            // Kembali ke posisi awal cell untuk menambahkan gambar
            $pdf->SetXY($x, $y);
            $pdf->Image($signature_path, $x + 5, $y + 1, 15, 8);
            
            // Pindah ke posisi setelah cell
            $pdf->SetXY($x + 25, $y);
        } else {
            $pdf->Cell(25, 12, 'TTD', 1, 0, 'C');
        }
    } else {
        $pdf->Cell(25, 12, '', 1, 0, 'C');
    }
}

$pdf->Cell(15, 12, $total_hadir, 1, 1, 'C');
        }

        // Add new page untuk bagian 2
        $pdf->AddPage();
        
        // BAGIAN 2: PRAK 6-11
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 8, 'BAGIAN 2: PRAKTIKUM 6-11', 0, 1, 'L');
        
        // Table header bagian 2
        $pdf->SetFont('Arial', 'B', 7);
        
        // Header row 1
        $pdf->Cell(8, 25, 'NO', 1, 0, 'C');
        $pdf->Cell(20, 25, 'NIM', 1, 0, 'C');
        $pdf->Cell(40, 25, 'NAMA', 1, 0, 'C');
        $pdf->Cell(50, 25, 'PRAKTIKUM (KELAS)', 1, 0, 'C');
        
        // Prak 6-11 dengan format rapi
        for ($p = 6; $p <= 11; $p++) {
            $date = isset($info_pertemuan[$p]) ? date('d/m/Y', strtotime($info_pertemuan[$p]['tanggal'])) : '-';
            $time = isset($info_pertemuan[$p]) ? 
                substr($info_pertemuan[$p]['jam_mulai'], 0, 5) . ' - ' . substr($info_pertemuan[$p]['jam_akhir'], 0, 5) : '-';
            
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Rect($x, $y, 25, 25);
            $pdf->SetXY($x, $y + 2);
            $pdf->Cell(25, 5, 'PRAKTIKUM ' . $p, 0, 0, 'C');
            $pdf->SetXY($x, $y + 9);
            $pdf->Cell(25, 5, $date, 0, 0, 'C');
            $pdf->SetXY($x, $y + 16);
            $pdf->Cell(25, 5, $time, 0, 0, 'C');
            $pdf->SetXY($x + 25, $y);
        }
        
        $pdf->Cell(15, 25, 'TOTAL', 1, 1, 'C');

        // Data rows bagian 2
        $pdf->SetFont('Arial', '', 8);
        $no = 1;
        foreach ($data_organized as $nim => $data) {
            $asisten = $data['info'];
            $absensi_asisten = $data['absensi'];
            
            $absensi_per_pertemuan = [];
            foreach ($absensi_asisten as $absensi) {
                $pertemuan = $absensi['pertemuan'];
                $absensi_per_pertemuan[$pertemuan] = $absensi;
            }
            
            $total_hadir_bagian2 = 0;
            for ($p = 6; $p <= 11; $p++) {
                if (isset($absensi_per_pertemuan[$p]) && $absensi_per_pertemuan[$p]['status_hadir'] == 'hadir') {
                    $total_hadir_bagian2++;
                }
            }
            
            $praktikum_display = $asisten['nama_praktikum'] . " (" . ($asisten['kelas'] ?? '-') . ")";
            
            $pdf->Cell(8, 12, $no++, 1, 0, 'C');
            $pdf->Cell(20, 12, $asisten['nim'], 1, 0, 'C');
            $pdf->Cell(40, 12, $asisten['nama'], 1, 0, 'L');
            $pdf->Cell(50, 12, $praktikum_display, 1, 0, 'L');
            
            // Prak 6-11 - Gambar Tanda Tangan
           for ($p = 6; $p <= 11; $p++) {
    $absensi_p = isset($absensi_per_pertemuan[$p]) ? $absensi_per_pertemuan[$p] : null;
    $hadir_p = $absensi_p && $absensi_p['status_hadir'] == 'hadir';
    
    if ($hadir_p && !empty($absensi_p['signature_data'])) {
        $signature_path = getSignaturePath($absensi_p['signature_data']);
        if ($signature_path) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            
            // Buat cell dengan border terlebih dahulu
            $pdf->Cell(25, 12, '', 1, 0);
            
            // Kembali ke posisi awal cell untuk menambahkan gambar
            $pdf->SetXY($x, $y);
            $pdf->Image($signature_path, $x + 5, $y + 1, 15, 8);
            
            // Pindah ke posisi setelah cell
            $pdf->SetXY($x + 25, $y);
        } else {
            $pdf->Cell(25, 12, 'TTD', 1, 0, 'C');
        }
    } else {
        $pdf->Cell(25, 12, '', 1, 0, 'C');
    }
}

$pdf->Cell(15, 12, $total_hadir_bagian2, 1, 1, 'C');
        }

        // Add new page untuk bagian 3
        $pdf->AddPage();
        
        // BAGIAN 3: PRAK 12-14 + TUGAS AKHIR + NILAI AKHIR
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(0, 8, 'BAGIAN 3: PRAKTIKUM 12-14 + TUGAS AKHIR + NILAI AKHIR', 0, 1, 'L');
            
            // Table header bagian 3
            $pdf->SetFont('Arial', 'B', 7);
            
            // Header row 1
            $pdf->Cell(8, 25, 'NO', 1, 0, 'C');
            $pdf->Cell(25, 25, 'NIM', 1, 0, 'C');
            $pdf->Cell(45, 25, 'NAMA', 1, 0, 'C');
            $pdf->Cell(50, 25, 'PRAKTIKUM (KELAS)', 1, 0, 'C');
            
            // Prak 12-14 dengan format rapi
            for ($p = 12; $p <= 14; $p++) {
                $date = isset($info_pertemuan[$p]) ? date('d/m/Y', strtotime($info_pertemuan[$p]['tanggal'])) : '-';
                $time = isset($info_pertemuan[$p]) ? 
                    substr($info_pertemuan[$p]['jam_mulai'], 0, 5) . ' - ' . substr($info_pertemuan[$p]['jam_akhir'], 0, 5) : '-';
                
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->Rect($x, $y, 25, 25);
                $pdf->SetXY($x, $y + 2);
                $pdf->Cell(25, 5, 'PRAKTIKUM ' . $p, 0, 0, 'C');
                $pdf->SetXY($x, $y + 9);
                $pdf->Cell(25, 5, $date, 0, 0, 'C');
                $pdf->SetXY($x, $y + 16);
                $pdf->Cell(25, 5, $time, 0, 0, 'C');
                $pdf->SetXY($x + 25, $y);
            }
            
            // Tugas Akhir dengan format rapi
            $tugas_date = isset($info_pertemuan['Presentasi Tugas Akhir']) ? date('d/m/Y', strtotime($info_pertemuan['Presentasi Tugas Akhir']['tanggal'])) : '-';
            $tugas_time = isset($info_pertemuan['Presentasi Tugas Akhir']) ? 
                substr($info_pertemuan['Presentasi Tugas Akhir']['jam_mulai'], 0, 5) . ' - ' . substr($info_pertemuan['Presentasi Tugas Akhir']['jam_akhir'], 0, 5) : '-';
            
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Rect($x, $y, 25, 25);
            $pdf->SetXY($x, $y + 2);
           $pdf->MultiCell(25, 3, "TUBES/\nPRESENTASI", 0, 'C');
            $pdf->SetXY($x, $y + 9);
            $pdf->Cell(25, 5, $tugas_date, 0, 0, 'C');
            $pdf->SetXY($x, $y + 16);
            $pdf->Cell(25, 5, $tugas_time, 0, 0, 'C');
            $pdf->SetXY($x + 25, $y);
            
            // Nilai Akhir dengan format rapi
            $nilai_date = isset($info_pertemuan['Pengisian Nilai Akhir']) ? date('d/m/Y', strtotime($info_pertemuan['Pengisian Nilai Akhir']['tanggal'])) : '-';
            $nilai_time = isset($info_pertemuan['Pengisian Nilai Akhir']) ? 
                substr($info_pertemuan['Pengisian Nilai Akhir']['jam_mulai'], 0, 5) . ' - ' . substr($info_pertemuan['Pengisian Nilai Akhir']['jam_akhir'], 0, 5) : '-';
            
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->Rect($x, $y, 25, 25);
            $pdf->SetXY($x, $y + 2);
            $pdf->MultiCell(25, 3, "PENGISIAN FORM\nPENILAIAN AKHIR", 0, 'C');
            $pdf->SetXY($x, $y + 9);
            $pdf->Cell(25, 5, $nilai_date, 0, 0, 'C');
            $pdf->SetXY($x, $y + 16);
            $pdf->Cell(25, 5, $nilai_time, 0, 0, 'C');
            $pdf->SetXY($x + 25, $y);

            $pdf->Cell(15, 25, 'TOTAL JML', 1, 1, 'C');

            // Data rows bagian 3
            $pdf->SetFont('Arial', '', 8);
            $no = 1;
            foreach ($data_organized as $nim => $data) {
                $asisten = $data['info'];
                $absensi_asisten = $data['absensi'];
                
                $absensi_per_pertemuan = [];
                foreach ($absensi_asisten as $absensi) {
                    $pertemuan = $absensi['pertemuan'];
                    $absensi_per_pertemuan[$pertemuan] = $absensi;
                }
                
                $total_keseluruhan = 0;
                foreach ($absensi_per_pertemuan as $absensi) {
                    if ($absensi['status_hadir'] == 'hadir') {
                        $total_keseluruhan++;
                    }
                }
                
                $praktikum_display = $asisten['nama_praktikum'] . " (" . ($asisten['kelas'] ?? '-') . ")";
                
                $pdf->Cell(8, 12, $no++, 1, 0, 'C');
                $pdf->Cell(25, 12, $asisten['nim'], 1, 0, 'C');
                $pdf->Cell(45, 12, $asisten['nama'], 1, 0, 'L');
                $pdf->Cell(50, 12, $praktikum_display, 1, 0, 'L');
                
                // Prak 12-14 - Gambar Tanda Tangan
              for ($p = 12; $p <= 14; $p++) {
    $absensi_p = isset($absensi_per_pertemuan[$p]) ? $absensi_per_pertemuan[$p] : null;
    $hadir_p = $absensi_p && $absensi_p['status_hadir'] == 'hadir';
    
    if ($hadir_p && !empty($absensi_p['signature_data'])) {
        $signature_path = getSignaturePath($absensi_p['signature_data']);
        if ($signature_path) {
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            
            // Buat cell dengan border terlebih dahulu
            $pdf->Cell(25, 12, '', 1, 0);
            
            // Kembali ke posisi awal cell untuk menambahkan gambar
            $pdf->SetXY($x, $y);
            $pdf->Image($signature_path, $x + 5, $y + 1, 15, 8);
            
            // Pindah ke posisi setelah cell
            $pdf->SetXY($x + 25, $y);
        } else {
            $pdf->Cell(25, 12, 'TTD', 1, 0, 'C');
        }
    } else {
        $pdf->Cell(25, 12, '', 1, 0, 'C');
    }
}
                
                // Tugas Akhir - Gambar Tanda Tangan
                $tugas_akhir = isset($absensi_per_pertemuan['Presentasi Tugas Akhir']) ? $absensi_per_pertemuan['Presentasi Tugas Akhir'] : null;
$hadir_tugas_akhir = $tugas_akhir && $tugas_akhir['status_hadir'] == 'hadir';

if ($hadir_tugas_akhir && !empty($tugas_akhir['signature_data'])) {
    $signature_path = getSignaturePath($tugas_akhir['signature_data']);
    if ($signature_path) {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        
        // Buat cell dengan border terlebih dahulu
        $pdf->Cell(25, 12, '', 1, 0);
        
        // Kembali ke posisi awal cell untuk menambahkan gambar
        $pdf->SetXY($x, $y);
        $pdf->Image($signature_path, $x + 5, $y + 1, 15, 8);
        
        // Pindah ke posisi setelah cell
        $pdf->SetXY($x + 25, $y);
    } else {
        $pdf->Cell(25, 12, 'TTD', 1, 0, 'C');
    }
} else {
    $pdf->Cell(25, 12, '', 1, 0, 'C');
}
            
            // Nilai Akhir - Gambar Tanda Tangan
           $nilai_akhir = isset($absensi_per_pertemuan['Pengisian Nilai Akhir']) ? $absensi_per_pertemuan['Pengisian Nilai Akhir'] : null;
$hadir_nilai_akhir = $nilai_akhir && $nilai_akhir['status_hadir'] == 'hadir';

if ($hadir_nilai_akhir && !empty($nilai_akhir['signature_data'])) {
    $signature_path = getSignaturePath($nilai_akhir['signature_data']);
    if ($signature_path) {
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        
        // Buat cell dengan border terlebih dahulu
        $pdf->Cell(25, 12, '', 1, 0);
        
        // Kembali ke posisi awal cell untuk menambahkan gambar
        $pdf->SetXY($x, $y);
        $pdf->Image($signature_path, $x + 5, $y + 1, 15, 8);
        
        // Pindah ke posisi setelah cell
        $pdf->SetXY($x + 25, $y);
    } else {
        $pdf->Cell(25, 12, 'TTD', 1, 0, 'C');
    }
} else {
    $pdf->Cell(25, 12, '', 1, 0, 'C');
}

$pdf->Cell(15, 12, $total_keseluruhan, 1, 1, 'C');
            }
        // Output PDF ke browser
        $clean_praktikum_name = preg_replace('/[^a-zA-Z0-9]/', '_', $nama_praktikum);
        $pdf->Output('I', 'rekap_kehadiran_' . $clean_praktikum_name . '.pdf');
        
        exit();
        
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}



// JIKA BUKAN PDF, TAMPILKAN FORM FILTER
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran Asisten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card { margin-top: 20px; }
        .required { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Rekap Kehadiran Asisten Praktikum</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="laporan_admin.php" target="_blank">
                            <input type="hidden" name="pdf" value="1">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Praktikum <span class="required">*</span></label>
                                    <select name="praktikum_id" class="form-select" required>
                                        <option value="">- Pilih Praktikum -</option>
                                        <?php
                                        $query_praktikum = "SELECT DISTINCT p.id, p.nama_praktikum 
                                                           FROM praktikum p 
                                                           JOIN asisten_praktikum ap ON p.id = ap.praktikum_id 
                                                           WHERE ap.status = 'active'
                                                           ORDER BY p.nama_praktikum";
                                        $stmt_praktikum = $pdo->query($query_praktikum);
                                        $praktikum_list = $stmt_praktikum->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach ($praktikum_list as $praktikum) {
                                            echo "<option value='{$praktikum['id']}'>{$praktikum['nama_praktikum']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kelas</label>
                                    <select name="kelas" class="form-select">
                                        <option value="all">- Semua Kelas -</option>
                                        <?php
                                        $query_kelas = "SELECT DISTINCT kelas FROM asisten_praktikum WHERE kelas IS NOT NULL ORDER BY kelas";
                                        $stmt_kelas = $pdo->query($query_kelas);
                                        $kelas_list = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach ($kelas_list as $kelas) {
                                            echo "<option value='{$kelas['kelas']}'>Kelas {$kelas['kelas']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control" value="2025-01-01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control" value="2025-12-31">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select name="tahun_ajaran" class="form-select">
                                        <option value="2024/2025">2024/2025</option>
                                        <option value="2025/2026" selected>2025/2026</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Semester</label>
                                    <select name="semester" class="form-select">
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap" selected>Genap</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    📄 Generate PDF
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 