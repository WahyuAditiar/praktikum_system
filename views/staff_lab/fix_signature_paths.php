<?php
// fix_signature_paths.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

$host = 'localhost';
$dbname = 'praktikum_system';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

echo "<h2>PERBAIKI PATH SIGNATURE DI DATABASE</h2>";

// 1. Lihat data sebelum diperbaiki
echo "<h3>Data sebelum diperbaiki:</h3>";
$query = "SELECT id, nim, signature_path FROM absen_asisten WHERE signature_path IS NOT NULL";
$stmt = $pdo->query($query);
$data_before = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>NIM</th><th>Signature Path</th></tr>";
foreach ($data_before as $row) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['nim']}</td>";
    echo "<td>{$row['signature_path']}</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Perbaiki path
echo "<h3>Memperbaiki path...</h3>";
$update_query = "UPDATE absen_asisten SET signature_path = CONCAT('praktikum_system/', signature_path) WHERE signature_path IS NOT NULL AND signature_path NOT LIKE 'praktikum_system/%'";
$stmt = $pdo->prepare($update_query);
$result = $stmt->execute();

if ($result) {
    echo "✅ Path berhasil diperbaiki!<br>";
    
    // 3. Lihat data setelah diperbaiki
    echo "<h3>Data setelah diperbaiki:</h3>";
    $query = "SELECT id, nim, signature_path FROM absen_asisten WHERE signature_path IS NOT NULL";
    $stmt = $pdo->query($query);
    $data_after = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>NIM</th><th>Signature Path</th></tr>";
    foreach ($data_after as $row) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nim']}</td>";
        echo "<td>{$row['signature_path']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Gagal memperbaiki path!";
}
?>