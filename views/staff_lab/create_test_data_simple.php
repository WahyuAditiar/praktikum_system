<?php
// create_test_data_simple.php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<h3>Membuat Data Testing Sederhana</h3>";
    
    // Insert data testing langsung
    $sql = "INSERT INTO absen_asisten 
            (nim, nama, praktikum_id, praktikum_name, kelas, pertemuan, tanggal, 
             jam_mulai, jam_akhir, status_hadir, signature_path, created_by) 
            VALUES 
            ('123456', 'Agus Wahyu Prasetyo', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Briefing', '2025-10-08', '08:00:00', '10:00:00', 'hadir', 'uploads/signatures/test_123456.png', 'System'),
            ('123456', 'Agus Wahyu Prasetyo', 1, 'Prak. Algoritma dan Pemrograman', 'A', '1', '2025-10-15', '08:00:00', '10:00:00', 'hadir', 'uploads/signatures/test_123456.png', 'System'),
            ('123457', 'Kevin Khozimah Zaki', 1, 'Prak. Algoritma dan Pemrograman', 'A', 'Briefing', '2025-10-08', '08:00:00', '10:00:00', 'hadir', 'uploads/signatures/test_123457.png', 'System')";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute();
    
    if ($result) {
        echo "Berhasil membuat data testing!<br>";
        echo "<a href='laporan_admin.php'>Kembali ke Laporan Admin</a>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>