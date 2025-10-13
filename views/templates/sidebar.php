<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
                <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">
                    <i class="fas fa-user mr-2"></i>
                    <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>
                </span>
                <div class="dropdown-divider"></div>

                <a href="../asisten_praktikumMenu/profile.php" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> My Profile
                </a>

                <a href="../asisten_praktikumMenu/profile_settings.php" class="dropdown-item">
                    <i class="fas fa-signature mr-2"></i> Signature Settings
                </a>

                <div class="dropdown-divider"></div>
                <a href="../auth/logout.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../../index.php" class="brand-link">
        <i class="fas fa-flask brand-icon"></i>
        <span class="brand-text font-weight-light">Sistem Praktikum</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false">

                <!-- MENU LAB (Admin & Staff Lab) -->
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff_lab'): ?>
                    <li class="nav-item">
                        <a href="../staff_lab/dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard Lab</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-flask"></i>
                            <p>
                                Menu Lab
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../staff_lab/absensiStaffnAdmin.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Absen Praktikan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/dosen.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Dosen</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/praktikum.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Praktikum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/asisten_praktikum.php" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Asisten Praktikum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/JadwalPraktikum.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Jadwal Praktikum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/mahasiswa.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Mahasiswa Praktikum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/absen_asistenpraktikum.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Absensi Asisten Praktikum</p>
                                </a>
                            </li>
                            <!-- ✅ MENU BARU: KELOLA GROUP PRAKTIKUM -->
                            <li class="nav-item">
  <a href="/praktikum_system/index.php?page=group&action=config" class="nav-link">
    <i class="far fa-circle nav-icon"></i>
    <p>Kelola Group Praktikum</p>
</a>
</li>


                        </ul>
                    </li>
                <?php endif; ?>

                <!-- MENU PRODI (Admin & Staff Prodi) -->
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff_prodi'): ?>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-university"></i>
                            <p>Menu Prodi<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../staff_prodi/dashboard.php" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard Prodi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/dosen.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Dosen</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/ruangan.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Ruangan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/matakuliah.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Mata Kuliah</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/jadwal.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Manajemen Jadwal</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- MENU ASISTEN PRAKTIKUM -->
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asisten_praktikum'): ?>
                    <li class="nav-item">
                        <a href="../asisten_praktikumMenu/dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard Asisten</p>
                        </a>
                    </li>

                    <!-- ✅ MENU ABSENSI PRAKTIKAN BARU -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Absensi Praktikan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="absensi.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Input Absensi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="?page=absensi&action=lihat_rekap" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lihat Rekap</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="../staff_lab/absen_asistenpraktikum.php" class="nav-link">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>Absensi Asisten</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>