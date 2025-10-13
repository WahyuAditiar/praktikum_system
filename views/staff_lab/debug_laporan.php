<?php
// debug_laporan.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

$host = 'localhost';
$dbname = 'praktikum_system';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Parameter yang sama seperti di form
$praktikum_id = 1;
$kelas = 'all';
$start_date = '2025-01-01';
$end_date = '2025-12-31';

echo "<h2>DEBUG LAPORAN - STEP BY STEP</h2>";

// 1. Cek data asisten
echo "<h3>1. DATA ASISTEN:</h3>";
$query_asisten = "SELECT DISTINCT 
                    ap.nim, 
                    ap.nama,
                    ap.kelas,
                    p.nama_praktikum,
                    p.id as praktikum_id
                  FROM asisten_praktikum ap 
                  JOIN praktikum p ON ap.praktikum_id = p.id 
                  WHERE ap.praktikum_id = :praktikum_id 
                  AND ap.status = 'active'
                  ORDER BY ap.nama";

$stmt_asisten = $pdo->prepare($query_asisten);
$stmt_asisten->execute([':praktikum_id' => $praktikum_id]);
$asisten_list = $stmt_asisten->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>NIM</th><th>Nama</th><th>Kelas</th><th>Praktikum</th></tr>";
foreach ($asisten_list as $asisten) {
    echo "<tr>";
    echo "<td>{$asisten['nim']}</td>";
    echo "<td>{$asisten['nama']}</td>";
    echo "<td>{$asisten['kelas']}</td>";
    echo "<td>{$asisten['nama_praktikum']}</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Cek data absensi
echo "<h3>2. DATA ABSENSI:</h3>";
$nim_list = array_column($asisten_list, 'nim');
$placeholders = str_repeat('?,', count($nim_list) - 1) . '?';

$query_absensi = "SELECT 
                    aa.nim, 
                    aa.nama, 
                    aa.pertemuan, 
                    aa.tanggal,
                    aa.status_hadir,
                    aa.signature_path
                  FROM absen_asisten aa 
                  WHERE aa.nim IN ($placeholders)
                  AND aa.tanggal BETWEEN ? AND ?
                  AND aa.praktikum_id = ?
                  ORDER BY aa.nim, aa.tanggal, aa.pertemuan";

$absensi_params = $nim_list;
$absensi_params[] = $start_date;
$absensi_params[] = $end_date;
$absensi_params[] = $praktikum_id;

echo "<p>Query: $query_absensi</p>";
echo "<p>Params: " . implode(', ', $absensi_params) . "</p>";

$stmt_absensi = $pdo->prepare($query_absensi);
$stmt_absensi->execute($absensi_params);
$data_absensi = $stmt_absensi->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>NIM</th><th>Nama</th><th>Pertemuan</th><th>Tanggal</th><th>Status</th><th>Signature</th></tr>";
foreach ($data_absensi as $absensi) {
    echo "<tr>";
    echo "<td>{$absensi['nim']}</td>";
    echo "<td>{$absensi['nama']}</td>";
    echo "<td>{$absensi['pertemuan']}</td>";
    echo "<td>{$absensi['tanggal']}</td>";
    echo "<td>{$absensi['status_hadir']}</td>";
    echo "<td>{$absensi['signature_path']}</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Cek organisasi data
echo "<h3>3. ORGANISASI DATA:</h3>";
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

foreach ($data_organized as $nim => $data) {
    $asisten = $data['info'];
    $absensi_asisten = $data['absensi'];
    
    echo "<h4>Asisten: {$asisten['nama']} ({$asisten['nim']})</h4>";
    echo "<p>Jumlah absensi: " . count($absensi_asisten) . "</p>";
    
    if (count($absensi_asisten) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Pertemuan</th><th>Status</th><th>Tanggal</th></tr>";
        foreach ($absensi_asisten as $absensi) {
            echo "<tr>";
            echo "<td>{$absensi['pertemuan']}</td>";
            echo "<td>{$absensi['status_hadir']}</td>";
            echo "<td>{$absensi['tanggal']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada data absensi</p>";
    }
    
    // Test logic untuk Briefing
    $absensi_per_pertemuan = [];
    foreach ($absensi_asisten as $absensi) {
        $pertemuan = $absensi['pertemuan'];
        $absensi_per_pertemuan[$pertemuan] = $absensi;
    }
    
    $briefing = isset($absensi_per_pertemuan['Briefing']) ? $absensi_per_pertemuan['Briefing'] : null;
    $hadir_briefing = $briefing && $briefing['status_hadir'] == 'hadir';
    
    echo "<p>Briefing - Data: " . ($briefing ? 'ADA' : 'TIDAK ADA') . "</p>";
    echo "<p>Briefing - Hadir: " . ($hadir_briefing ? 'H' : 'KOSONG') . "</p>";
    echo "<hr>";
}
?>