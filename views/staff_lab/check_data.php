<?php
// debug_signature.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

$host = 'localhost';
$dbname = 'praktikum_system'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    echo "<h2>DEBUG DATA TANDA TANGAN</h2>";
    
    // 1. Cek struktur tabel
    echo "<h3>1. Struktur Tabel absen_asisten:</h3>";
    $stmt = $pdo->query("DESCRIBE absen_asisten");
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    print_r($structure);
    echo "</pre>";
    
    // 2. Cek data dengan signature
    echo "<h3>2. Data dengan Signature Path:</h3>";
    $query = "SELECT nim, nama, pertemuan, tanggal, signature_path, LENGTH(signature_path) as path_length
              FROM absen_asisten 
              WHERE signature_path IS NOT NULL 
              AND signature_path != ''";
    $stmt = $pdo->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<p style='color: red;'>TIDAK ADA DATA DENGAN SIGNATURE PATH!</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>NIM</th><th>Nama</th><th>Pertemuan</th><th>Tanggal</th><th>Signature Path</th><th>Path Length</th><th>File Exists</th><th>Preview</th></tr>";
        
        foreach ($results as $row) {
            $file_exists = file_exists($row['signature_path']) ? 'YA' : 'TIDAK';
            $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $row['signature_path'];
            $file_exists_full = file_exists($full_path) ? 'YA' : 'TIDAK';
            
            echo "<tr>";
            echo "<td>{$row['nim']}</td>";
            echo "<td>{$row['nama']}</td>";
            echo "<td>{$row['pertemuan']}</td>";
            echo "<td>{$row['tanggal']}</td>";
            echo "<td>{$row['signature_path']}</td>";
            echo "<td>{$row['path_length']}</td>";
            echo "<td>Relative: {$file_exists}<br>Full: {$file_exists_full}</td>";
            echo "<td>";
            if (file_exists($row['signature_path'])) {
                echo '<img src="' . $row['signature_path'] . '" style="max-width: 100px;">';
            } else if (file_exists($full_path)) {
                echo '<img src="' . $row['signature_path'] . '" style="max-width: 100px;">';
            } else {
                echo 'File tidak ditemukan';
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 3. Cek folder uploads
    echo "<h3>3. Cek Folder Uploads:</h3>";
    $upload_dir = 'uploads/';
    if (is_dir($upload_dir)) {
        $files = scandir($upload_dir);
        echo "<p>Isi folder uploads:</p>";
        echo "<pre>";
        print_r($files);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>Folder uploads tidak ditemukan!</p>";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>