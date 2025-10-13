<?php
require_once __DIR__ . '/../../config/config.php';
checkAuth();
checkRole(['admin']);

$page_title = "Dashboard Administrator";
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Administrator</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Admin Overview -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>3</h3>
                            <p>Total User</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <a href="users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>15</h3>
                            <p>Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <a href="../staff_lab/praktikum.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>25</h3>
                            <p>Dosen</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <a href="../staff_prodi/dosen.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>50</h3>
                            <p>Mata Kuliah</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <a href="../staff_prodi/matakuliah.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Menu Sections -->
            <div class="row">
                <!-- Staff Lab Menu -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-flask mr-2"></i>Menu Staff Laboratorium</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <a href="../staff_lab/praktikum.php" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-flask mr-1"></i> Praktikum
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="../staff_lab/mahasiswa.php" class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-users mr-1"></i> Mahasiswa
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="../staff_lab/asisten_praktikum.php" class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-user-graduate mr-1"></i> Asisten
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="../staff_lab/jadwal.php" class="btn btn-danger btn-block mb-2">
                                        <i class="fas fa-calendar mr-1"></i> Jadwal
                                    </a>
                                </div>
                                <div class="col-12">
                                    <a href="../staff_lab/absensi.php" class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-clipboard-check mr-1"></i> Absensi
                                    </a>
                                </div>
                                <!-- Di bagian menu admin -->
<div class="card">
    <div class="card-header">
        <h5>Manajemen Praktikum</h5>
    </div>
    <div class="card-body">
        <a href="?page=group&action=config&jadwal_id=5" class="btn btn-primary mb-2">
            <i class="fas fa-users"></i> Kelola Group Praktikum
        </a>
        <!-- menu lainnya -->
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Staff Prodi Menu -->
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-graduation-cap mr-2"></i>Menu Staff Prodi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <a href="../staff_prodi/dosen.php" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i> Dosen
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="../staff_prodi/ruangan.php" class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-door-open mr-1"></i> Ruangan
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="../staff_prodi/matakuliah.php" class="btn btn-warning btn-block mb-2">
                                        <i class="fas fa-book mr-1"></i> Mata Kuliah
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="../staff_prodi/jadwal.php" class="btn btn-danger btn-block mb-2">
                                        <i class="fas fa-calendar-alt mr-1"></i> Jadwal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Admin Specific Menu -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-dark">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-cogs mr-2"></i>Menu Administrator</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <a href="users.php" class="btn btn-dark btn-block mb-3">
                                        <i class="fas fa-users-cog mr-2"></i>Manajemen User
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="logs.php" class="btn btn-secondary btn-block mb-3">
                                        <i class="fas fa-clipboard-list mr-2"></i>Log System
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="backup.php" class="btn btn-light btn-block mb-3">
                                        <i class="fas fa-database mr-2"></i>Backup Data
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="settings.php" class="btn btn-info btn-block mb-3">
                                        <i class="fas fa-sliders-h mr-2"></i>Pengaturan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Sistem</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Versi Sistem</strong></td>
                                    <td>v1.0.0</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login</strong></td>
                                    <td><?php echo date('d/m/Y H:i:s'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Role</strong></td>
                                    <td><span class="badge badge-success">Administrator</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aktivitas Terbaru</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <small><i class="fas fa-info-circle"></i> User stafflab login</small>
                            </div>
                            <div class="alert alert-success">
                                <small><i class="fas fa-check-circle"></i> Backup database completed</small>
                            </div>
                            <div class="alert alert-warning">
                                <small><i class="fas fa-exclamation-triangle"></i> New user registered</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>