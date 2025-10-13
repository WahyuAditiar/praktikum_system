<?php
// debug_signature_final.php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

$host = 'localhost';
$dbname = 'praktikum_system';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

echo "<h2>DEBUG SIGNATURE PATH FINAL</h2>";

// Ambil data signature
$query = "SELECT id, nim, pertemuan, signature_path FROM absen_asisten WHERE signature_path IS NOT NULL LIMIT 5";
$stmt = $pdo->query($query);
$signatures = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($signatures as $sig) {
    echo "<h3>Data: {$sig['nim']} - {$sig['pertemuan']}</h3>";
    echo "Path di database: {$sig['signature_path']}<br>";
    
    // Test berbagai kemungkinan path
    $possible_paths = [
        $sig['signature_path'],
        'praktikum_system/' . $sig['signature_path'],
        '../' . $sig['signature_path'],
        '../../' . $sig['signature_path'],
        'C:/xampp/htdocs/praktikum_system/' . $sig['signature_path']
    ];
    
    foreach ($possible_paths as $test_path) {
        $exists = file_exists($test_path) ? "✅ ADA" : "❌ TIDAK ADA";
        echo "Test: {$test_path} - {$exists}<br>";
        
        if (file_exists($test_path)) {
            echo "Preview: <img src='{$test_path}' style='max-width: 100px; border: 1px solid green;'><br>";
        }
    }
    echo "<hr>";
}
?>