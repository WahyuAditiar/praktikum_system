<?php
// export_rekap.php
session_start();

// Cek role - hanya admin dan asisten_praktikum yang boleh akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'asisten_praktikum', 'staff_lab'])) {
    die("Akses ditolak. Hanya admin, staff lab, dan asisten praktikum yang dapat mengakses laporan ini.");
}

require_once __DIR__ . '/../../config/config.php';

// Buat koneksi database
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Set headers untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Rekap_Kehadiran_Asisten_" . date('Y-m-d') . ".xls\"");
header("Cache-Control: max-age=0");

// Ambil parameter filter
$praktikum_id = isset($_GET['praktikum_id']) ? $_GET['praktikum_id'] : null;
$tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : date('Y') . '/' . (date('Y') + 1);
$semester = isset($_GET['semester']) ? $_GET['semester'] : 'Genap';
$nim = isset($_GET['nim']) ? $_GET['nim'] : null;

try {
    // Query untuk mengambil data asisten praktikum dengan filter
    $query_asisten = "SELECT ap.*, p.nama_praktikum, m.kode_mk, m.nama_mk 
                      FROM asisten_praktikum ap 
                      JOIN praktikum p ON ap.praktikum_id = p.id 
                      JOIN mata_kuliah m ON p.mata_kuliah_id = m.id 
                      WHERE ap.status = 'active'";
    
    $params = [];
    
    if ($praktikum_id) {
        $query_asisten .= " AND ap.praktikum_id = :praktikum_id";
        $params[':praktikum_id'] = $praktikum_id;
    }
    
    if ($tahun_ajaran) {
        $query_asisten .= " AND ap.tahun_ajaran = :tahun_ajaran";
        $params[':tahun_ajaran'] = $tahun_ajaran;
    }
    
    if ($semester) {
        $query_asisten .= " AND ap.semester = :semester";
        $params[':semester'] = $semester;
    }
    
    // Jika asisten_praktikum, hanya bisa lihat data sendiri
    if ($_SESSION['role'] === 'asisten_praktikum' && isset($_SESSION['nim'])) {
        $query_asisten .= " AND ap.nim = :nim";
        $params[':nim'] = $_SESSION['nim'];
    }
    
    // Jika ada filter NIM (untuk admin/staff)
    if ($nim && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff_lab')) {
        $query_asisten .= " AND ap.nim = :nim_filter";
        $params[':nim_filter'] = $nim;
    }
    
    $query_asisten .= " ORDER BY ap.nama ASC";
    
    $stmt_asisten = $pdo->prepare($query_asisten);
    $stmt_asisten->execute($params);
    $asisten_list = $stmt_asisten->fetchAll(PDO::FETCH_ASSOC);

    // Jika tidak ada data asisten
    if (empty($asisten_list)) {
        die("Tidak ada data asisten praktikum untuk filter yang dipilih.");
    }

    // Ambil NIM dari asisten yang akan diquery
    $nim_list = array_column($asisten_list, 'nim');
    $placeholders = str_repeat('?,', count($nim_list) - 1) . '?';
    
    // **QUERY YANG DIPERBAIKI - SESUAI STRUKTUR TABEL absen_asisten**
    $query_absensi = "SELECT 
                        aa.id,
                        aa.nim,
                        aa.nama,
                        aa.praktikum_id,
                        aa.praktikum_name,
                        aa.kelas,
                        aa.pertemuan,
                        aa.tanggal,
                        aa.jam_mulai,
                        aa.jam_akhir,
                        aa.status_hadir,
                        aa.signature_path,
                        aa.foto_path,
                        aa.laporan_path,
                        aa.gps_lat,
                        aa.gps_lng,
                        aa.created_by,
                        aa.created_at,
                        aa.updated_at
                      FROM absen_asisten aa 
                      WHERE aa.nim IN ($placeholders)
                      ORDER BY aa.nama, aa.tanggal, aa.pertemuan";
    
    $stmt_absensi = $pdo->prepare($query_absensi);
    $stmt_absensi->execute($nim_list);
    $absensi_data = $stmt_absensi->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}

// **DEBUG: Cek data yang diambil**
error_log("Jumlah asisten: " . count($asisten_list));
error_log("Jumlah absensi: " . count($absensi_data));

// Organisasi data absensi per asisten
$absensi_per_asisten = [];
foreach ($absensi_data as $absensi) {
    $nim = $absensi['nim'];
    if (!isset($absensi_per_asisten[$nim])) {
        $absensi_per_asisten[$nim] = [
            'nama' => $absensi['nama'],
            'praktikum_name' => $absensi['praktikum_name'] ?? '',
            'absensi' => []
        ];
    }
    $absensi_per_asisten[$nim]['absensi'][] = $absensi;
}

// **CEK APAKAH ADA DATA ABSENSI**
if (empty($absensi_data)) {
    error_log("TIDAK ADA DATA ABSENSI DITEMUKAN!");
    // Tetapi lanjutkan untuk menampilkan template kosong
}
?>

<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <style>
        td {
            mso-number-format:"\@";
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .header {
            font-size: 14pt;
            font-weight: bold;
        }
        .subheader {
            font-size: 12pt;
            font-weight: bold;
        }
        .table-header {
            font-weight: bold;
            text-align: center;
            background-color: #E6E6E6;
            border: 1px solid #000000;
        }
        .cell-border {
            border: 1px solid #000000;
            padding: 2px;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <table width="100%">
        <tr>
            <td colspan="23" class="header text-center">FAKULTAS TEKNIK UNIVERSITAS PANCASILA</td>
        </tr>
        <tr>
            <td colspan="23" class="subheader text-center">KEHADIRAN ASISTEN PRAKTIKUM / PRAKTEK</td>
        </tr>
        <tr>
            <td colspan="23" class="text-center">
                Praktikum: <?= htmlspecialchars($asisten_list[0]['nama_praktikum'] ?? '') ?><br>
                Tahun Ajaran: <?= htmlspecialchars($tahun_ajaran) ?> - Semester: <?= htmlspecialchars($semester) ?>
            </td>
        </tr>
        <tr><td colspan="23">&nbsp;</td></tr>
    </table>

    <!-- Bagian 1: Briefing + Praktikum 1-5 -->
    <table width="100%" cellpadding="2" cellspacing="0" style="border-collapse: collapse;">
        <!-- Header Table -->
        <tr>
            <td class="table-header cell-border" rowspan="3">NO</td>
            <td class="table-header cell-border" rowspan="3">NAMA</td>
            <td class="table-header cell-border" rowspan="3">NIM</td>
            <td class="table-header cell-border" rowspan="3">PRAKTIKUM</td>
            <td class="table-header cell-border" colspan="3">BRIEFING</td>
            <td class="table-header cell-border" colspan="3">PRAK 1</td>
            <td class="table-header cell-border" colspan="3">PRAK 2</td>
            <td class="table-header cell-border" colspan="3">PRAK 3</td>
            <td class="table-header cell-border" colspan="3">PRAK 4</td>
            <td class="table-header cell-border" colspan="3">PRAK 5</td>
            <td class="table-header cell-border" rowspan="3">TOTAL HADIR</td>
        </tr>
        <tr>
            <!-- Briefing & Prak 1-5 -->
            <?php for ($i = 0; $i <= 5; $i++): ?>
            <td class="table-header cell-border">Hadir</td>
            <td class="table-header cell-border">TTD</td>
            <td class="table-header cell-border">Foto</td>
            <?php endfor; ?>
        </tr>
        <tr>
            <!-- Keterangan untuk setiap pertemuan -->
            <?php for ($i = 0; $i <= 5; $i++): ?>
            <td class="table-header cell-border">✓</td>
            <td class="table-header cell-border">✓</td>
            <td class="table-header cell-border">✓</td>
            <?php endfor; ?>
        </tr>

        <?php
        $no = 1;
        foreach ($asisten_list as $asisten):
            $nim = $asisten['nim'];
            $asisten_absensi = isset($absensi_per_asisten[$nim]) ? $absensi_per_asisten[$nim]['absensi'] : [];
            
            // Organisasi absensi per pertemuan
            $absensi_organized = [];
            foreach ($asisten_absensi as $absensi) {
                $key = $absensi['pertemuan'];
                $absensi_organized[$key] = $absensi;
            }
            
            // Hitung total hadir
            $total_hadir = 0;
            foreach ($absensi_organized as $absensi) {
                if ($absensi['status_hadir'] == 'hadir') {
                    $total_hadir++;
                }
            }
        ?>
        <tr>
            <td class="cell-border text-center"><?= $no++ ?></td>
            <td class="cell-border"><?= htmlspecialchars($asisten['nama']) ?></td>
            <td class="cell-border text-center"><?= htmlspecialchars($asisten['nim']) ?></td>
            <td class="cell-border"><?= htmlspecialchars($asisten['nama_praktikum']) ?></td>
            
            <!-- Briefing -->
            <?php
            $briefing = isset($absensi_organized['Briefing']) ? $absensi_organized['Briefing'] : null;
            ?>
            <td class="cell-border text-center"><?= $briefing && $briefing['status_hadir'] == 'hadir' ? '✓' : '' ?></td>
            <td class="cell-border text-center"><?= $briefing && $briefing['signature_path'] ? '✓' : '' ?></td>
            <td class="cell-border text-center"><?= $briefing && $briefing['foto_path'] ? '✓' : '' ?></td>
            
            <!-- Prak 1-5 -->
            <?php for ($pertemuan = 1; $pertemuan <= 5; $pertemuan++): 
                $absensi_pertemuan = isset($absensi_organized[$pertemuan]) ? $absensi_organized[$pertemuan] : null;
            ?>
                <td class="cell-border text-center"><?= $absensi_pertemuan && $absensi_pertemuan['status_hadir'] == 'hadir' ? '✓' : '' ?></td>
                <td class="cell-border text-center"><?= $absensi_pertemuan && $absensi_pertemuan['signature_path'] ? '✓' : '' ?></td>
                <td class="cell-border text-center"><?= $absensi_pertemuan && $absensi_pertemuan['foto_path'] ? '✓' : '' ?></td>
            <?php endfor; ?>
            
            <!-- Total Hadir -->
            <td class="cell-border text-center"><?= $total_hadir ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Tambahkan informasi debug -->
    <table width="100%" style="margin-top: 20px;">
        <tr>
            <td colspan="23" class="text-center" style="color: red; font-size: 8pt;">
                Debug Info: <?= count($asisten_list) ?> asisten ditemukan, <?= count($absensi_data) ?> data absensi
            </td>
        </tr>
    </table>

</body>
</html>