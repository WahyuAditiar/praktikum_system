<?php
// laporan_admin.php - VERSI LEBIH RAPI
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Hanya admin yang dapat mengakses laporan ini.");
}

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

// FUNGSI UNTUK MENDAPATKAN PATH SIGNATURE
function getSignaturePath($nim, $tanggal, $base_path) {
    $date_part = date('Ymd', strtotime($tanggal));
    $filename = "sig_{$nim}_{$date_part}.png";
    $full_path = $base_path . $filename;
    
    if (file_exists($full_path)) {
        return "uploads/absen_asisten/" . $filename;
    }
    
    $files = scandir($base_path);
    $matching_files = [];
    
    foreach ($files as $file) {
        if (strpos($file, 'sig_') === 0 && strpos($file, $nim) !== false) {
            $matching_files[] = $file;
        }
    }
    
    if (!empty($matching_files)) {
        sort($matching_files);
        $latest_file = end($matching_files);
        return "uploads/absen_asisten/" . $latest_file;
    }
    
    return null;
}

// Base path untuk uploads
$base_upload_path = "C:/xampp/htdocs/praktikum_system/uploads/absen_asisten/";

// PROSES DOWNLOAD EXCEL
if (isset($_GET['download'])) {
    // Ambil parameter filter
    $praktikum_id = isset($_GET['praktikum_id']) ? $_GET['praktikum_id'] : null;
    $kelas = isset($_GET['kelas']) ? $_GET['kelas'] : null;
    $tahun_ajaran = isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '2025/2026';
    $semester = isset($_GET['semester']) ? $_GET['semester'] : 'Genap';
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '2025-01-01';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '2025-12-31';

    if (empty($praktikum_id)) {
        die("Silakan pilih praktikum terlebih dahulu!");
    }

    try {
        // QUERY ASISTEN
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

        // QUERY ABSENSI
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
                            aa.signature_path
                          FROM absen_asisten aa 
                          WHERE aa.nim IN ($placeholders)
                          AND aa.tanggal BETWEEN ? AND ?
                          ORDER BY aa.nim, aa.tanggal, aa.pertemuan";
        
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

        // Set headers untuk file Excel
        $nama_praktikum = $asisten_list[0]['nama_praktikum'] ?? 'Praktikum';
        $clean_praktikum_name = preg_replace('/[^a-zA-Z0-9]/', '_', $nama_praktikum);
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"rekap_kehadiran_{$clean_praktikum_name}.xls\"");
        header("Cache-Control: max-age=0");
        
?>        
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 9pt;
        }
        td, th { 
            border: 1px solid #000000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }
        th { 
            font-weight: bold; 
            background-color: #E6E6E6; 
        }
        .header { 
            font-size: 12pt; 
            font-weight: bold; 
            text-align: center;
        }
        .subheader { 
            font-size: 10pt; 
            font-weight: bold; 
            text-align: center;
        }
        .table-header { 
            background-color: #CCCCCC; 
            font-weight: bold;
        }
        .text-left { text-align: left; }
        
        /* COLUMN WIDTH YANG LEBIH RAPI */
        .col-no { width: 25px; }
        .col-nim { width: 80px; }
        .col-nama { width: 100px; }
        .col-praktikum { width: 120px; }
        .col-pertemuan { width: 35px; }
        .col-jam { width: 20px; }
        .col-total { width: 35px; }
        .col-tugas-akhir { width: 20px; }
        .col-nilai { width: 20px; }
        
        .tanggal-jam { 
            font-size: 6pt; 
            line-height: 1.0;
        }
        .signature-img {
            max-width: 30px;
            max-height: 12px;
            vertical-align: middle;
        }
        .spacer-row { height: 5px; }
    </style>
</head>
<body>
    <!-- Header Universitas -->
    <table>
        <tr>
            <td colspan="40" class="header">UNIVERSITAS PANCASILA</td>
        </tr>
        <tr>
            <td colspan="40" class="subheader">REKAP KEHADIRAN ASISTEN PRAKTIKUM</td>
        </tr>
        <tr>
            <td colspan="40" style="text-align: center; padding: 4px; font-size: 8pt;">
                Praktikum: <?= htmlspecialchars($nama_praktikum) ?> | 
                Tahun: <?= htmlspecialchars($tahun_ajaran) ?> - <?= htmlspecialchars($semester) ?>
                <?php if (!empty($kelas) && $kelas != 'all'): ?>
                    | Kelas: <?= htmlspecialchars($kelas) ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr><td colspan="40" class="spacer-row">&nbsp;</td></tr>
    </table>

    <!-- BAGIAN 1: BRIEFING + PRAK 1-5 -->
    <table cellpadding="1" cellspacing="0">
        <tr>
            <td class="table-header col-no" rowspan="3">NO</td>
            <td class="table-header col-nim" rowspan="3">NIM</td>
            <td class="table-header col-nama" rowspan="3">NAMA</td>
            <td class="table-header col-praktikum" rowspan="3">PRAKTIKUM (KELAS)</td>
            <td class="table-header" colspan="4">BRIEF</td>
            <td class="table-header" colspan="4">P1</td>
            <td class="table-header" colspan="4">P2</td>
            <td class="table-header" colspan="4">P3</td>
            <td class="table-header" colspan="4">P4</td>
            <td class="table-header" colspan="4">P5</td>
            <td class="table-header col-total" rowspan="3">TOTAL</td>
        </tr>
        <tr>
            <!-- Tanggal & Jam -->
            <?php for ($i = 0; $i <= 5; $i++): 
                $pertemuan = ($i == 0) ? 'Briefing' : $i;
                $info = isset($info_pertemuan[$pertemuan]) ? $info_pertemuan[$pertemuan] : null;
            ?>
            <td class="table-header tanggal-jam" colspan="4">
                <?php if ($info): ?>
                    <?= date('d/m', strtotime($info['tanggal'])) ?><br>
                    <?= substr($info['jam_mulai'], 0, 5) ?>
                <?php else: ?>
                    -<br>-
                <?php endif; ?>
            </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <?php for ($i = 0; $i <= 5; $i++): ?>
            <td class="table-header col-jam">1</td>
            <td class="table-header col-jam">2</td>
            <td class="table-header col-jam">3</td>
            <td class="table-header col-pertemuan">TTD</td>
            <?php endfor; ?>
        </tr>

        <?php
        $no = 1;
        foreach ($data_organized as $nim => $data):
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
        ?>
        <tr>
            <td class="col-no"><?= $no++ ?></td>
            <td class="col-nim"><?= htmlspecialchars($asisten['nim']) ?></td>
            <td class="col-nama text-left"><?= htmlspecialchars($asisten['nama']) ?></td>
            <td class="col-praktikum text-left"><?= htmlspecialchars($praktikum_display) ?></td>
            
            <!-- Briefing -->
            <?php
            $briefing = isset($absensi_per_pertemuan['Briefing']) ? $absensi_per_pertemuan['Briefing'] : null;
            $hadir_briefing = $briefing && $briefing['status_hadir'] == 'hadir';
            ?>
            <td class="col-jam"><?= $hadir_briefing ? 'H' : '' ?></td>
            <td class="col-jam"><?= $hadir_briefing ? 'H' : '' ?></td>
            <td class="col-jam"><?= $hadir_briefing ? 'H' : '' ?></td>
            <td class="col-pertemuan">
                <?php if ($hadir_briefing): 
                    $sig_web_path = getSignaturePath($asisten['nim'], $briefing['tanggal'], $base_upload_path);
                    if ($sig_web_path): ?>
                        <img src="<?= $sig_web_path ?>" class="signature-img" alt="TTD">
                    <?php else: ?>
                        ✓
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            
            <!-- Prak 1-5 -->
            <?php for ($p = 1; $p <= 5; $p++): 
                $absensi_p = isset($absensi_per_pertemuan[$p]) ? $absensi_per_pertemuan[$p] : null;
                $hadir_p = $absensi_p && $absensi_p['status_hadir'] == 'hadir';
            ?>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-pertemuan">
                    <?php if ($hadir_p): 
                        $sig_web_path = getSignaturePath($asisten['nim'], $absensi_p['tanggal'], $base_upload_path);
                        if ($sig_web_path): ?>
                            <img src="<?= $sig_web_path ?>" class="signature-img" alt="TTD">
                        <?php else: ?>
                            ✓
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
            
            <td class="col-total"><?= $total_hadir ?></td>
        </tr>
        <?php endforeach; ?>
        
        <!-- Tambah baris kosong jika kurang dari 8 -->
        <?php for ($i = count($asisten_list); $i < 8; $i++): ?>
        <tr>
            <td class="col-no"><?= $i + 1 ?></td>
            <td class="col-nim">&nbsp;</td>
            <td class="col-nama">&nbsp;</td>
            <td class="col-praktikum">&nbsp;</td>
            <?php for ($j = 0; $j < 24; $j++): ?>
            <td>&nbsp;</td>
            <?php endfor; ?>
            <td class="col-total">&nbsp;</td>
        </tr>
        <?php endfor; ?>
    </table>

    <br>

    <!-- BAGIAN 2: PRAK 6-11 -->
    <table cellpadding="1" cellspacing="0">
        <tr>
            <td class="table-header col-no" rowspan="3">NO</td>
            <td class="table-header col-nim" rowspan="3">NIM</td>
            <td class="table-header col-nama" rowspan="3">NAMA</td>
            <td class="table-header col-praktikum" rowspan="3">PRAKTIKUM (KELAS)</td>
            <td class="table-header" colspan="4">P6</td>
            <td class="table-header" colspan="4">P7</td>
            <td class="table-header" colspan="4">P8</td>
            <td class="table-header" colspan="4">P9</td>
            <td class="table-header" colspan="4">P10</td>
            <td class="table-header" colspan="4">P11</td>
            <td class="table-header col-total" rowspan="3">TOTAL</td>
        </tr>
        <tr>
            <!-- Tanggal & Jam -->
            <?php for ($p = 6; $p <= 11; $p++): 
                $info = isset($info_pertemuan[$p]) ? $info_pertemuan[$p] : null;
            ?>
            <td class="table-header tanggal-jam" colspan="4">
                <?php if ($info): ?>
                    <?= date('d/m', strtotime($info['tanggal'])) ?><br>
                    <?= substr($info['jam_mulai'], 0, 5) ?>
                <?php else: ?>
                    -<br>-
                <?php endif; ?>
            </td>
            <?php endfor; ?>
        </tr>
        <tr>
            <?php for ($i = 6; $i <= 11; $i++): ?>
            <td class="table-header col-jam">1</td>
            <td class="table-header col-jam">2</td>
            <td class="table-header col-jam">3</td>
            <td class="table-header col-pertemuan">TTD</td>
            <?php endfor; ?>
        </tr>

        <?php
        $no = 1;
        foreach ($data_organized as $nim => $data):
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
        ?>
        <tr>
            <td class="col-no"><?= $no++ ?></td>
            <td class="col-nim"><?= htmlspecialchars($asisten['nim']) ?></td>
            <td class="col-nama text-left"><?= htmlspecialchars($asisten['nama']) ?></td>
            <td class="col-praktikum text-left"><?= htmlspecialchars($praktikum_display) ?></td>
            
            <!-- Prak 6-11 -->
            <?php for ($p = 6; $p <= 11; $p++): 
                $absensi_p = isset($absensi_per_pertemuan[$p]) ? $absensi_per_pertemuan[$p] : null;
                $hadir_p = $absensi_p && $absensi_p['status_hadir'] == 'hadir';
            ?>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-pertemuan">
                    <?php if ($hadir_p): 
                        $sig_web_path = getSignaturePath($asisten['nim'], $absensi_p['tanggal'], $base_upload_path);
                        if ($sig_web_path): ?>
                            <img src="<?= $sig_web_path ?>" class="signature-img" alt="TTD">
                        <?php else: ?>
                            ✓
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
            
            <td class="col-total"><?= $total_hadir_bagian2 ?></td>
        </tr>
        <?php endforeach; ?>
        
        <?php for ($i = count($asisten_list); $i < 8; $i++): ?>
        <tr>
            <td class="col-no"><?= $i + 1 ?></td>
            <td class="col-nim">&nbsp;</td>
            <td class="col-nama">&nbsp;</td>
            <td class="col-praktikum">&nbsp;</td>
            <?php for ($j = 0; $j < 24; $j++): ?>
            <td>&nbsp;</td>
            <?php endfor; ?>
            <td class="col-total">&nbsp;</td>
        </tr>
        <?php endfor; ?>
    </table>

    <br>

    <!-- BAGIAN 3: PRAK 12-14 + TUGAS AKHIR + NILAI AKHIR -->
    <table cellpadding="1" cellspacing="0">
        <tr>
            <td class="table-header col-no" rowspan="3">NO</td>
            <td class="table-header col-nim" rowspan="3">NIM</td>
            <td class="table-header col-nama" rowspan="3">NAMA</td>
            <td class="table-header col-praktikum" rowspan="3">PRAKTIKUM (KELAS)</td>
            <td class="table-header" colspan="4">P12</td>
            <td class="table-header" colspan="4">P13</td>
            <td class="table-header" colspan="4">P14</td>
            <td class="table-header" colspan="5">PRES. TUGAS AKHIR</td>
            <td class="table-header" colspan="3">NILAI AKHIR</td>
            <td class="table-header col-total" rowspan="3">TOTAL</td>
        </tr>
        <tr>
            <!-- Tanggal & Jam untuk P12-P14 -->
            <?php for ($p = 12; $p <= 14; $p++): 
                $info = isset($info_pertemuan[$p]) ? $info_pertemuan[$p] : null;
            ?>
            <td class="table-header tanggal-jam" colspan="4">
                <?php if ($info): ?>
                    <?= date('d/m', strtotime($info['tanggal'])) ?><br>
                    <?= substr($info['jam_mulai'], 0, 5) ?>
                <?php else: ?>
                    -<br>-
                <?php endif; ?>
            </td>
            <?php endfor; ?>
            <!-- Header untuk Tugas Akhir dan Nilai Akhir -->
            <td class="table-header" colspan="5">&nbsp;</td>
            <td class="table-header" colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <?php for ($i = 12; $i <= 14; $i++): ?>
            <td class="table-header col-jam">1</td>
            <td class="table-header col-jam">2</td>
            <td class="table-header col-jam">3</td>
            <td class="table-header col-pertemuan">TTD</td>
            <?php endfor; ?>
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <td class="table-header col-tugas-akhir"><?= $i ?></td>
            <?php endfor; ?>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <td class="table-header col-nilai"><?= $i ?></td>
            <?php endfor; ?>
        </tr>

        <?php
        $no = 1;
        foreach ($data_organized as $nim => $data):
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
        ?>
        <tr>
            <td class="col-no"><?= $no++ ?></td>
            <td class="col-nim"><?= htmlspecialchars($asisten['nim']) ?></td>
            <td class="col-nama text-left"><?= htmlspecialchars($asisten['nama']) ?></td>
            <td class="col-praktikum text-left"><?= htmlspecialchars($praktikum_display) ?></td>
            
            <!-- Prak 12-14 -->
            <?php for ($p = 12; $p <= 14; $p++): 
                $absensi_p = isset($absensi_per_pertemuan[$p]) ? $absensi_per_pertemuan[$p] : null;
                $hadir_p = $absensi_p && $absensi_p['status_hadir'] == 'hadir';
            ?>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-jam"><?= $hadir_p ? 'H' : '' ?></td>
                <td class="col-pertemuan">
                    <?php if ($hadir_p): 
                        $sig_web_path = getSignaturePath($asisten['nim'], $absensi_p['tanggal'], $base_upload_path);
                        if ($sig_web_path): ?>
                            <img src="<?= $sig_web_path ?>" class="signature-img" alt="TTD">
                        <?php else: ?>
                            ✓
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
            
            <!-- Tugas Akhir 1-5 -->
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <td class="col-tugas-akhir">&nbsp;</td>
            <?php endfor; ?>
            
            <!-- Nilai Akhir 1-3 -->
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <td class="col-nilai">&nbsp;</td>
            <?php endfor; ?>
            
            <td class="col-total"><?= $total_keseluruhan ?></td>
        </tr>
        <?php endforeach; ?>
        
        <?php for ($i = count($asisten_list); $i < 8; $i++): ?>
        <tr>
            <td class="col-no"><?= $i + 1 ?></td>
            <td class="col-nim">&nbsp;</td>
            <td class="col-nama">&nbsp;</td>
            <td class="col-praktikum">&nbsp;</td>
            <?php for ($j = 0; $j < 20; $j++): ?>
            <td>&nbsp;</td>
            <?php endfor; ?>
            <td class="col-total">&nbsp;</td>
        </tr>
        <?php endfor; ?>
    </table>

</body>
</html>
<?php
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// JIKA BUKAN DOWNLOAD, TAMPILKAN FORM FILTER (SAMA SEPERTI SEBELUMNYA)
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
                        <form method="GET" action="laporan_admin.php">
                            <input type="hidden" name="download" value="1">
                            
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
                                    📥 Download Rekap Excel
                                </button>
                                <a href="dashboard_admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>