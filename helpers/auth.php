<?php
session_start();

/**
 * Cek apakah user sudah login dan punya role tertentu
 * 
 * @param array $roles daftar role yang diizinkan, contoh ['staff_lab']
 */
function checkAuth($roles = []) {
    if (!isset($_SESSION['role'])) {
        header("Location: ../../login.php");
        exit;
    }

    if (!empty($roles) && !in_array($_SESSION['role'], $roles)) {
        echo "Akses ditolak!";
        exit;
    }
}
