<?php


// load Database class
require_once __DIR__ . '/Database.php';

// start session sekali saja
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/praktikum_system/');
// Di config file atau bagian atas halaman
define('UPLOADS_BASE_URL', '/praktikum_system/uploads');

// buat instance Database dan ambil koneksi PDO
$database = new Database();
$db = $database->getConnection();

// Redirect jika belum login
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "views/login.php");
        exit();
    }
}

// Check role access
function checkRole($allowed_roles) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: " . BASE_URL . "views/login.php");
        exit();
    }
}

error_reporting(E_ALL);
ini_set('display_errors', 1);