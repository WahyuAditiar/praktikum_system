<?php
require_once __DIR__ . '/../../config/config.php';
checkAuth();
checkRole(['staff_lab', 'admin']);



$page_title = "Dashboard Staff Laboratorium";
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Staff Laboratorium</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- STATISTICS CARDS -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>15</h3>
                            <p>Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <a href="praktikum.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>150</h3>
                            <p>Mahasiswa</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="mahasiswa.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>20</h3>
                            <p>Asisten Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <a href="asisten_praktikum.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>30</h3>
                            <p>Jadwal</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <a href="JadwalPraktikum.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bolt mr-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="?page=group&action=list" class="btn btn-primary btn-block py-3">
    <i class="fas fa-users mr-2"></i>Kelola Group Praktikum
</a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="absensiStaffnAdmin.php" class="btn btn-success btn-block py-3">
                                        <i class="fas fa-clipboard-check mr-2"></i>Input Absensi
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="asisten_praktikum.php" class="btn btn-warning btn-block py-3">
                                        <i class="fas fa-user-graduate mr-2"></i>Kelola Asisten
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="JadwalPraktikum.php" class="btn btn-info btn-block py-3">
                                        <i class="fas fa-calendar-alt mr-2"></i>Jadwal Praktikum
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT ACTIVITY & STATS -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history mr-2"></i>Recent Activity
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Aktivitas</th>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <i class="fas fa-user-check text-success mr-2"></i>
                                                Absensi Prak. Algoritma - Kelas A
                                            </td>
                                            <td>2 jam lalu</td>
                                            <td><span class="badge badge-success">Selesai</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fas fa-user-plus text-info mr-2"></i>
                                                Asisten baru ditambahkan
                                            </td>
                                            <td>1 hari lalu</td>
                                            <td><span class="badge badge-info">Baru</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar-plus text-warning mr-2"></i>
                                                Jadwal prak. Basis Data diperbarui
                                            </td>
                                            <td>2 hari lalu</td>
                                            <td><span class="badge badge-warning">Updated</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <i class="fas fa-users text-primary mr-2"></i>
                                                Pembagian group prak. Struktur Data
                                            </td>
                                            <td>3 hari lalu</td>
                                            <td><span class="badge badge-primary">Active</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie mr-2"></i>Quick Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <h2 class="text-primary mb-1">5</h2>
                                    <small class="text-muted">Praktikum Aktif</small>
                                </div>
                                <div class="mb-3">
                                    <h2 class="text-success mb-1">8</h2>
                                    <small class="text-muted">Asisten Aktif</small>
                                </div>
                                <div class="mb-3">
                                    <h2 class="text-warning mb-1">3</h2>
                                    <small class="text-muted">Lab Tersedia</small>
                                </div>
                                <div>
                                    <h2 class="text-info mb-1">85%</h2>
                                    <small class="text-muted">Kehadiran Rata-rata</small>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <small class="text-muted">Kapasitas Lab Terpakai</small>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                                </div>
                                
                                <small class="text-muted">Asisten Tersedia</small>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100">90%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UPCOMING SCHEDULE -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-day mr-2"></i>Jadwal Hari Ini
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title">Prak. Algoritma</h6>
                                            <p class="card-text mb-1">
                                                <small><i class="fas fa-clock mr-1"></i>08:00 - 10:00</small>
                                            </p>
                                            <p class="card-text mb-1">
                                                <small><i class="fas fa-map-marker-alt mr-1"></i>Lab A</small>
                                            </p>
                                            <p class="card-text">
                                                <small><i class="fas fa-users mr-1"></i>Group 1 (25 orang)</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h6 class="card-title">Prak. Basis Data</h6>
                                            <p class="card-text mb-1">
                                                <small><i class="fas fa-clock mr-1"></i>10:00 - 12:00</small>
                                            </p>
                                            <p class="card-text mb-1">
                                                <small><i class="fas fa-map-marker-alt mr-1"></i>Lab B</small>
                                            </p>
                                            <p class="card-text">
                                                <small><i class="fas fa-users mr-1"></i>Group 2 (23 orang)</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <h6 class="card-title">Prak. Struktur Data</h6>
                                            <p class="card-text mb-1">
                                                <small><i class="fas fa-clock mr-1"></i>13:00 - 15:00</small>
                                            </p>
                                            <p class="card-text mb-1">
                                                <small><i class="fas fa-map-marker-alt mr-1"></i>Lab C</small>
                                            </p>
                                            <p class="card-text">
                                                <small><i class="fas fa-users mr-1"></i>Group 1 (28 orang)</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>