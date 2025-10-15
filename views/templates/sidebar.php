<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="dashboard.php" class="nav-link text-dark">
                <i class="fas fa-home mr-1"></i>Dashboard
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#" style="gap: 8px;">
                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>
                <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                <i class="fas fa-chevron-down ml-1" style="font-size: 12px;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow border-0">
                <div class="dropdown-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-2" style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="font-weight-bold"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></div>
                            <small class="text-light"><?= ucfirst($_SESSION['role'] ?? 'User') ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <a href="../asisten_praktikumMenu/profile.php" class="dropdown-item py-2">
                    <i class="fas fa-user-circle mr-3 text-primary"></i>
                    <span>My Profile</span>
                </a>
                
                <a href="../asisten_praktikumMenu/profile_settings.php" class="dropdown-item py-2">
                    <i class="fas fa-signature mr-3 text-info"></i>
                    <span>Signature Settings</span>
                </a>
                
                <div class="dropdown-divider"></div>
                
                <a href="../auth/logout.php" class="dropdown-item py-2">
                    <i class="fas fa-sign-out-alt mr-3 text-danger"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li>
    </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../../index.php" class="brand-link text-center border-0">
        <div class="brand-icon bg-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-flask text-primary fa-lg"></i>
        </div>
        <span class="brand-text font-weight-light mt-1">Sistem Praktikum</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex border-bottom">
            <div class="image">
                <div class="user-avatar bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>
            </div>
            <div class="info">
                <div class="d-block text-white font-weight-bold"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></div>
                <small class="text-light"><?= ucfirst($_SESSION['role'] ?? 'User') ?></small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false">

                <!-- MENU LAB (Admin & Staff Lab) -->
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff_lab'): ?>
                    <li class="nav-item">
                        <a href="../staff_lab/dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard Lab</p>
                        </a>
                    </li>
                    
                    <li class="nav-header text-uppercase small font-weight-bold text-muted mt-3 mb-1">Menu Laboratorium</li>
                    
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-flask"></i>
                            <p>
                                Management Lab
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../staff_lab/absensiStaffnAdmin.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Absensi Praktikan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/dosen.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Dosen</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/praktikum.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Praktikum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/asisten_praktikum.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
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
                                    <p>Data Mahasiswa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_lab/absen_asistenpraktikum.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Absensi Asisten</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/praktikum_system/index.php?page=group&action=config" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kelola Group</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- MENU PRODI (Admin & Staff Prodi) -->
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff_prodi'): ?>
                    <li class="nav-header text-uppercase small font-weight-bold text-muted mt-3 mb-1">Menu Program Studi</li>
                    
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-university"></i>
                            <p>
                                Management Prodi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="../staff_prodi/dashboard.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dashboard Prodi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/dosen.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Dosen</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/ruangan.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data Ruangan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/matakuliah.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Mata Kuliah</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="../staff_prodi/jadwal.php" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Jadwal Kuliah</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

<!-- MENU ASISTEN PRAKTIKUM -->
<?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'asisten_praktikum'): ?>
    <li class="nav-header text-uppercase small font-weight-bold text-muted mt-3 mb-1">Menu Asisten Praktikum</li>
    
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-user-graduate"></i>
            <p>
                Asisten Praktikum
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="../asisten_praktikumMenu/dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Dashboard Asisten</p>
                </a>
            </li>
            
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>
                        Absensi Praktikan
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="margin-left: 15px;">
                    <li class="nav-item">
                        <a href="../asisten_praktikumMenu/absensi.php" class="nav-link">
                            <i class="far fa-dot-circle nav-icon"></i>
                            <p>Input Absensi</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="../staff_lab/absen_asistenpraktikum.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Absensi Asisten</p>
                </a>
            </li>
        </ul>
    </li>
<?php endif; ?>

<!-- FOOTER SECTION -->
<li class="nav-footer mt-4 pt-3 border-top">
    <div class="text-center text-muted small">
        <div class="mb-1">Sistem Praktikum</div>
        <div>v1.0.0</div>
    </div>
</li>
            </ul>
        </nav>
    </div>
</aside>

<style>
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --accent-color: #f1f5f9;
    --text-dark: #1e293b;
    --text-light: #94a3b8;
}

.main-sidebar {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
    border-right: 1px solid #334155;
}

.brand-link {
    background: rgba(255, 255, 255, 0.05) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    padding: 1rem 0.5rem !important;
}

.brand-link .brand-icon {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6) !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    transition: all 0.3s ease;
}

.brand-link:hover .brand-icon {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
}

.brand-text {
    color: #f8fafc !important;
    font-weight: 500 !important;
    font-size: 1.1rem;
}

.user-panel {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.user-avatar {
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.nav-sidebar > .nav-item > .nav-link {
    margin: 0.2rem 0.5rem;
    border-radius: 8px;
    color: #cbd5e1;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.nav-sidebar > .nav-item > .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(4px);
}

.nav-sidebar > .nav-item > .nav-link.active {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.nav-sidebar .nav-treeview .nav-link {
    margin: 0.1rem 0;
    padding-left: 3rem !important;
    border-radius: 6px;
    color: #94a3b8;
    border-left: 2px solid transparent;
}

.nav-sidebar .nav-treeview .nav-link:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #e2e8f0;
    border-left-color: var(--primary-color);
}

.nav-sidebar .nav-treeview .nav-link.active {
    background: rgba(37, 99, 235, 0.15);
    color: #ffffff;
    border-left-color: var(--primary-color);
}

.nav-header {
    padding: 1rem 1rem 0.5rem 1rem !important;
    letter-spacing: 0.5px;
    color: #64748b !important;
}

.nav-icon {
    color: #94a3b8;
    transition: all 0.3s ease;
}

.nav-link:hover .nav-icon,
.nav-link.active .nav-icon {
    color: inherit;
}

.nav-footer {
    padding: 1rem;
    opacity: 0.7;
}

/* Dropdown styling */
.dropdown-menu {
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.dropdown-header {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6) !important;
}

/* Main header styling */
.main-header {
    background: #ffffff !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.navbar-nav .nav-link {
    color: var(--text-dark) !important;
}

.navbar-nav .nav-link:hover {
    color: var(--primary-color) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .brand-text {
        font-size: 1rem;
    }
    
    .nav-sidebar > .nav-item > .nav-link {
        margin: 0.1rem 0.3rem;
        font-size: 0.9rem;
    }
}

/* Animation for treeview */
.nav-treeview {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>