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
                    <h1 class="m-0 text-dark">Dashboard Staff Laboratorium</h1>
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
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h3>15</h3>
                            <p>Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <a href="praktikum.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h3>150</h3>
                            <p>Mahasiswa</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="mahasiswa.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h3>20</h3>
                            <p>Asisten Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <a href="asisten_praktikum.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-info">
                        <div class="inner">
                            <h3>30</h3>
                            <p>Jadwal</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <a href="JadwalPraktikum.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bolt mr-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="?page=group&action=list" class="btn btn-outline-primary btn-block p-3 text-left">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Kelola Group</div>
                                        <small class="text-muted">Praktikum</small>
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="absensiStaffnAdmin.php" class="btn btn-outline-success btn-block p-3 text-left">
                                        <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Input Absensi</div>
                                        <small class="text-muted">Praktikan</small>
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="asisten_praktikum.php" class="btn btn-outline-warning btn-block p-3 text-left">
                                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Kelola Asisten</div>
                                        <small class="text-muted">Praktikum</small>
                                    </a>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <a href="JadwalPraktikum.php" class="btn btn-outline-info btn-block p-3 text-left">
                                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Jadwal</div>
                                        <small class="text-muted">Praktikum</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT ROW -->
            <div class="row mt-4">
                <!-- RECENT ACTIVITY -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header border-bottom-0">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history mr-2"></i>Recent Activity
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-user-check text-success fa-lg"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="font-weight-bold">Absensi Prak. Algoritma - Kelas A</div>
                                            <small class="text-muted">2 jam lalu</small>
                                        </div>
                                        <span class="badge badge-success">Selesai</span>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-user-plus text-primary fa-lg"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="font-weight-bold">Asisten baru ditambahkan</div>
                                            <small class="text-muted">1 hari lalu</small>
                                        </div>
                                        <span class="badge badge-primary">Baru</span>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-calendar-plus text-warning fa-lg"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="font-weight-bold">Jadwal prak. Basis Data diperbarui</div>
                                            <small class="text-muted">2 hari lalu</small>
                                        </div>
                                        <span class="badge badge-warning">Updated</span>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-users text-info fa-lg"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="font-weight-bold">Pembagian group prak. Struktur Data</div>
                                            <small class="text-muted">3 hari lalu</small>
                                        </div>
                                        <span class="badge badge-info">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UPCOMING SCHEDULE -->
                    <div class="card mt-4">
                        <div class="card-header border-bottom-0">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-day mr-2"></i>Jadwal Hari Ini
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-primary shadow-sm h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">Prak. Algoritma</h6>
                                            <div class="mb-2">
                                                <i class="fas fa-clock text-muted mr-1"></i>
                                                <small>08:00 - 10:00</small>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-map-marker-alt text-muted mr-1"></i>
                                                <small>Lab A</small>
                                            </div>
                                            <div>
                                                <i class="fas fa-users text-muted mr-1"></i>
                                                <small>Group 1 (25 orang)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-success shadow-sm h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">Prak. Basis Data</h6>
                                            <div class="mb-2">
                                                <i class="fas fa-clock text-muted mr-1"></i>
                                                <small>10:00 - 12:00</small>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-map-marker-alt text-muted mr-1"></i>
                                                <small>Lab B</small>
                                            </div>
                                            <div>
                                                <i class="fas fa-users text-muted mr-1"></i>
                                                <small>Group 2 (23 orang)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-warning shadow-sm h-100">
                                        <div class="card-body">
                                            <h6 class="card-title text-warning">Prak. Struktur Data</h6>
                                            <div class="mb-2">
                                                <i class="fas fa-clock text-muted mr-1"></i>
                                                <small>13:00 - 15:00</small>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-map-marker-alt text-muted mr-1"></i>
                                                <small>Lab C</small>
                                            </div>
                                            <div>
                                                <i class="fas fa-users text-muted mr-1"></i>
                                                <small>Group 1 (28 orang)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR STATS -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header border-bottom-0">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie mr-2"></i>Quick Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Stats Numbers -->
                            <div class="text-center mb-4">
                                <div class="mb-4">
                                    <h2 class="text-primary mb-1">5</h2>
                                    <small class="text-muted">Praktikum Aktif</small>
                                </div>
                                <div class="mb-4">
                                    <h2 class="text-success mb-1">8</h2>
                                    <small class="text-muted">Asisten Aktif</small>
                                </div>
                                <div class="mb-4">
                                    <h2 class="text-warning mb-1">3</h2>
                                    <small class="text-muted">Lab Tersedia</small>
                                </div>
                                <div>
                                    <h2 class="text-info mb-1">85%</h2>
                                    <small class="text-muted">Kehadiran Rata-rata</small>
                                </div>
                            </div>
                            
                            <!-- Progress Bars -->
                            <div class="mt-4">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Kapasitas Lab</small>
                                        <small class="text-muted">75%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Asisten Tersedia</small>
                                        <small class="text-muted">90%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Kehadiran</small>
                                        <small class="text-muted">85%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SYSTEM INFO -->
                    <div class="card mt-4">
                        <div class="card-header border-bottom-0">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle mr-2"></i>System Info
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <tr>
                                    <td width="40%"><strong>Role</strong></td>
                                    <td><span class="badge badge-success">Staff Lab</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login</strong></td>
                                    <td><?php echo date('d/m/Y H:i:s'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Server Time</strong></td>
                                    <td><?php echo date('H:i:s'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.small-box {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
}

.card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.card-outline {
    border-top: 3px solid;
}

.btn-outline-primary, 
.btn-outline-success, 
.btn-outline-warning, 
.btn-outline-info {
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 2px solid;
}

.btn-outline-primary:hover, 
.btn-outline-success:hover, 
.btn-outline-warning:hover, 
.btn-outline-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.badge {
    border-radius: 6px;
    font-weight: 500;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.list-group-item:last-child {
    border-bottom: none;
}

.border-left-primary { border-left: 4px solid #007bff !important; }
.border-left-success { border-left: 4px solid #28a745 !important; }
.border-left-warning { border-left: 4px solid #ffc107 !important; }
.border-left-info { border-left: 4px solid #17a2b8 !important; }

.progress {
    border-radius: 10px;
    background-color: #f8f9fa;
}

.progress-bar {
    border-radius: 10px;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>