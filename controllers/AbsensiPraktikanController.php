<?php
// controllers/AbsensiPraktikanController.php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['asprak', 'admin'])) {
    header('Location: /unauthorized.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AbsensiPraktikanModel.php';
require_once __DIR__ . '/../models/JadwalPraktikumModel.php';
require_once __DIR__ . '/../models/MahasiswaModel.php';

$db = (new Database())->getConnection();
$absenModel = new AbsensiPraktikanModel($db);
$jadwalModel = new JadwalPraktikumModel($db);
$mahasiswaModel = new MahasiswaModel($db);

$role = $_SESSION['role'];

// Untuk admin: bisa lihat semua absen tanpa kode
if ($role === 'admin') {
    $absenList = $absenModel->getAllAbsen();
    include __DIR__ . '/../views/asisten_praktikumMenu/absen_praktikan_admin.php';
    exit;
}

// Untuk asprak: harus pakai kode jadwal
$kode = isset($_GET['kode']) ? $_GET['kode'] : '';
$jadwal = null;
$mahasiswaList = [];
$absenList = [];
if ($kode) {
    $jadwal = $jadwalModel->getJadwalByKode($kode);
    if ($jadwal) {
        $mahasiswaList = $mahasiswaModel->getMahasiswaByPraktikum($jadwal['praktikum_id']);
        $absenList = $absenModel->getAbsenByJadwal($jadwal['id']);
    }
}
include __DIR__ . '/../views/asisten_praktikumMenu/absen_praktikan.php';
