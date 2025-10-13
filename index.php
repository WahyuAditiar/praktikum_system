<?php
session_start();

// kalau belum login, redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

<<<<<<< HEAD



=======
// Koneksi Database
require_once 'config/database.php';

// Dapatkan parameter dari URL
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Dapatkan role user
$role = $_SESSION['role'] ?? '';

// MAIN ROUTING
switch($page) {

    // ✅ ABSENSI ROUTES
    case 'absensi':
        require_once 'controllers/AbsensiController.php';
        $controller = new AbsensiController($db, $role);

        if ($action == 'simpan') {
            $controller->simpan();
            exit();
        } 
        elseif ($action == 'lihat_rekap') {
            $controller->lihatRekap();
            exit();
        } 
        else {
            $controller->index();
            exit();
        }
        break;

    // ✅ GROUP ROUTES - DITAMBAH assignAllAsprak
    case 'group':
        if (!in_array($role, ['admin', 'staff_lab'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses ke halaman ini';
            header("Location: ?page=dashboard");
            exit;
        }

        require_once 'controllers/GroupController.php';
        $controller = new GroupController();

        switch($action) {
            case 'config':
                $controller->config();
                break;
            case 'updateConfig':
                $controller->updateConfig();
                break;
            case 'bagiOtomatis':
                $controller->bagiOtomatis();
                break;
            case 'resetAssignments':
                $controller->resetAssignments();
                break;
            case 'assignManual':
                $controller->assignManual();
                break;
            case 'updateAssignments':
                $controller->updateAssignments();
                break;
            case 'assignAllAsprak': // ✅ TAMBAHKAN INI
                $controller->assignAllAsprak();
                break;
            default:
                $controller->config();
                break;
        }
        exit();

    // ✅ DASHBOARD ROUTES
    case 'dashboard':
        switch ($role) {
            case 'admin':
                header("Location: views/admin/dashboard.php");
                break;
            case 'staff_lab':
                header("Location: views/staff_lab/dashboard.php");
                break;
            case 'staff_prodi':
                header("Location: views/staff_prodi/dashboard.php");
                break;
            case 'asisten_praktikum':
                header("Location: views/asisten_praktikumMenu/dashboard.php");
                break;
            default:
                header("Location: login.php");
        }
        exit();

    // ✅ DEFAULT
    default:
        header("Location: ?page=dashboard");
        exit();
}
>>>>>>> 43c0fe9 (update_backup_kesalahan commite)
