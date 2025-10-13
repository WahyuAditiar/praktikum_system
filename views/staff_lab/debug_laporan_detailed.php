<?php
// debug_laporan_detailed.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

$host = 'localhost';
$dbname = 'praktikum_system';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

echo "<h2>DEBUG DETAILED - CEK STRUKTUR DATA</h2>";

// 1. Cek struktur tabel absen_asisten
echo "<h3>1. STRUKTUR TABEL absen_asisten:</h3>";
$stmt = $pdo->query("DESCRIBE absen_asisten");
$structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($structure);
echo "</pre>";

// 2. Cek beberapa data absen_asisten (tanpa filter)
echo "<h3>2. 10 DATA TERAKHIR absen_asisten (semua data):</h3>";
$stmt = $pdo->query("SELECT * FROM absen_asisten ORDER BY created_at DESC LIMIT 10");
$all_absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>NIM</th><th>Nama</th><th>Praktikum_ID</th><th>Pertemuan</th><th>Status</th><th>Tanggal</th></tr>";
foreach ($all_absensi as $absensi) {
    echo "<tr>";
    echo "<td>{$absensi['id']}</td>";
    echo "<td>{$absensi['nim']}</td>";
    echo "<td>{$absensi['nama']}</td>";
    echo "<td>{$absensi['praktikum_id']}</td>";
    echo "<td>{$absensi['pertemuan']}</td>";
    echo "<td>{$absensi['status_hadir']}</td>";
    echo "<td>{$absensi['tanggal']}</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Cek data asisten praktikum
echo "<h3>3. DATA ASISTEN PRAKTIKUM:</h3>";
$stmt = $pdo->query("SELECT * FROM asisten_praktikum WHERE status = 'active' LIMIT 5");
$asisten = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($asisten);
echo "</pre>";

// 4. Test query dengan praktikum_id yang berbeda
echo "<h3>4. TEST QUERY DENGAN PRAKTIKUM_ID BERBEDA:</h3>";

// Coba praktikum_id = 19 (dari data debug sebelumnya)
$test_praktikum_id = 19;
echo "<h4>Test dengan praktikum_id = $test_praktikum_id:</h4>";
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM absen_asisten WHERE praktikum_id = ?");
$stmt->execute([$test_praktikum_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Jumlah data dengan praktikum_id = $test_praktikum_id: " . $result['total'] . "<br>";

// Coba praktikum_id = 1
$test_praktikum_id = 1;
echo "<h4>Test dengan praktikum_id = $test_praktikum_id:</h4>";
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM absen_asisten WHERE praktikum_id = ?");
$stmt->execute([$test_praktikum_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Jumlah data dengan praktikum_id = $test_praktikum_id: " . $result['total'] . "<br>";

// Coba tanpa filter praktikum_id
echo "<h4>Test TANPA filter praktikum_id:</h4>";
$stmt = $pdo->query("SELECT COUNT(*) as total FROM absen_asisten");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Jumlah data TOTAL di absen_asisten: " . $result['total'] . "<br>";

// 5. Test query dengan NIM spesifik
echo "<h3>5. TEST DENGAN NIM 4521210064 (Agus):</h3>";
$stmt = $pdo->prepare("SELECT * FROM absen_asisten WHERE nim = ?");
$stmt->execute(['4521210064']);
$agus_absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($agus_absensi)) {
    echo "Tidak ada data absensi untuk NIM 4521210064<br>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>NIM</th><th>Nama</th><th>Praktikum_ID</th><th>Pertemuan</th><th>Status</th></tr>";
    foreach ($agus_absensi as $absensi) {
        echo "<tr>";
        echo "<td>{$absensi['nim']}</td>";
        echo "<td>{$absensi['nama']}</td>";
        echo "<td>{$absensi['praktikum_id']}</td>";
        echo "<td>{$absensi['pertemuan']}</td>";
        echo "<td>{$absensi['status_hadir']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>