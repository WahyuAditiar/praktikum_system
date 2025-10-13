<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/GroupModel.php';

$database = new Database();
$db = $database->getConnection();
$groupModel = new GroupModel($db);

echo "<h2>🔍 Debug Group Assignment</h2>";

// Ganti dengan jadwal_id dan user_id yang ingin dicek
$jadwal_id = 1; // Ganti dengan jadwal_id yang dipilih
$user_id = $_SESSION['user_id'] ?? 1; // Ganti dengan user_id asisten

echo "<h3>User ID: $user_id</h3>";
echo "<h3>Jadwal ID: $jadwal_id</h3>";

// 1. Cek group asisten
$asprak_group = $groupModel->getAsprakGroup($user_id, $jadwal_id);
echo "<h4>Group Asisten: " . ($asprak_group ?? 'Tidak ada') . "</h4>";

// 2. Debug assignment
$debug_data = $groupModel->debugAssignment($jadwal_id);

echo "<h4>Data Assignment:</h4>";
echo "<pre>";
print_r($debug_data);
echo "</pre>";

// 3. Cek mahasiswa per group
if ($asprak_group) {
    $mahasiswa_group = $groupModel->getMahasiswaByGroup($jadwal_id, $asprak_group);
    echo "<h4>Mahasiswa di Group $asprak_group:</h4>";
    echo "<pre>";
    print_r($mahasiswa_group);
    echo "</pre>";
}

// 4. Cek semua mahasiswa di jadwal
$all_mahasiswa = $groupModel->getAllMahasiswaInJadwal($jadwal_id);
echo "<h4>Semua Mahasiswa di Jadwal:</h4>";
echo "<pre>";
print_r($all_mahasiswa);
echo "</pre>";
?>