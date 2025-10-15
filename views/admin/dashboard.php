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
                    <h1 class="m-0 text-dark">Dashboard Administrator</h1>
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
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h3>150</h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="users.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h3>12</h3>
                            <p>Active Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <a href="../staff_lab/praktikum.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h3>45</h3>
                            <p>Dosen</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <a href="../staff_prodi/dosen.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-info">
                        <div class="inner">
                            <h3>28</h3>
                            <p>Asisten Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <a href="../staff_lab/asisten_praktikum.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Access Menu -->
            <div class="row">
                <!-- Staff Lab Section -->
                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-flask mr-2"></i>
                                Management Laboratorium
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-light">Staff Lab</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_lab/praktikum.php" class="btn btn-outline-primary btn-block text-left p-3">
                                        <i class="fas fa-flask fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Praktikum</div>
                                        <small class="text-muted">Manage praktikum</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_lab/mahasiswa.php" class="btn btn-outline-success btn-block text-left p-3">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Mahasiswa</div>
                                        <small class="text-muted">Data praktikan</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_lab/asisten_praktikum.php" class="btn btn-outline-warning btn-block text-left p-3">
                                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Asisten</div>
                                        <small class="text-muted">Asisten praktikum</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_lab/JadwalPraktikum.php" class="btn btn-outline-info btn-block text-left p-3">
                                        <i class="fas fa-calendar fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Jadwal</div>
                                        <small class="text-muted">Jadwal praktikum</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_lab/absensiStaffnAdmin.php" class="btn btn-outline-danger btn-block text-left p-3">
                                        <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Absensi</div>
                                        <small class="text-muted">Monitoring absensi</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="/praktikum_system/index.php?page=group&action=config" class="btn btn-outline-secondary btn-block text-left p-3">
                                        <i class="fas fa-layer-group fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Kelola Group</div>
                                        <small class="text-muted">Group praktikum</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff Prodi Section -->
                <div class="col-lg-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-university mr-2"></i>
                                Management Program Studi
                            </h3>
                            <div class="card-tools">
                                <span class="badge badge-light">Staff Prodi</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_prodi/dosen.php" class="btn btn-outline-primary btn-block text-left p-3">
                                        <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Dosen</div>
                                        <small class="text-muted">Data pengajar</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_prodi/ruangan.php" class="btn btn-outline-success btn-block text-left p-3">
                                        <i class="fas fa-door-open fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Ruangan</div>
                                        <small class="text-muted">Manage ruangan</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_prodi/matakuliah.php" class="btn btn-outline-warning btn-block text-left p-3">
                                        <i class="fas fa-book fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Mata Kuliah</div>
                                        <small class="text-muted">Kurikulum</small>
                                    </a>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <a href="../staff_prodi/jadwal.php" class="btn btn-outline-info btn-block text-left p-3">
                                        <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                        <div class="font-weight-bold">Jadwal Kuliah</div>
                                        <small class="text-muted">Jadwal mengajar</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Tools Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-dark card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>
                                Administrator Tools
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card bg-dark text-white h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div class="me-3">
                                                    <div class="text-white-75 small">User Management</div>
                                                    <div class="mb-2">Manage all users</div>
                                                </div>
                                                <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                        <a class="card-footer text-white clearfix small z-1" href="users.php">
                                            <span class="float-left">View Details</span>
                                            <span class="float-right">
                                                <i class="fas fa-angle-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card bg-secondary text-white h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div class="me-3">
                                                    <div class="text-white-75 small">System Logs</div>
                                                    <div class="mb-2">Activity monitoring</div>
                                                </div>
                                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                        <a class="card-footer text-white clearfix small z-1" href="logs.php">
                                            <span class="float-left">View Logs</span>
                                            <span class="float-right">
                                                <i class="fas fa-angle-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card bg-info text-white h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div class="me-3">
                                                    <div class="text-white-75 small">Data Backup</div>
                                                    <div class="mb-2">System backup</div>
                                                </div>
                                                <i class="fas fa-database fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                        <a class="card-footer text-white clearfix small z-1" href="backup.php">
                                            <span class="float-left">Backup Now</span>
                                            <span class="float-right">
                                                <i class="fas fa-angle-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card bg-warning text-dark h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div class="me-3">
                                                    <div class="text-dark-75 small">System Settings</div>
                                                    <div class="mb-2">Configuration</div>
                                                </div>
                                                <i class="fas fa-sliders-h fa-2x text-gray-600"></i>
                                            </div>
                                        </div>
                                        <a class="card-footer text-dark clearfix small z-1" href="settings.php">
                                            <span class="float-left">Configure</span>
                                            <span class="float-right">
                                                <i class="fas fa-angle-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-bottom-0">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>
                                System Information
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <tr>
                                    <td width="40%"><strong>System Version</strong></td>
                                    <td><span class="badge badge-primary">v2.1.0</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login</strong></td>
                                    <td><?php echo date('d/m/Y H:i:s'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>User Role</strong></td>
                                    <td><span class="badge badge-success">Super Administrator</span></td>
                                </tr>
                                <tr>
                                    <td><strong>PHP Version</strong></td>
                                    <td><?php echo phpversion(); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Server Time</strong></td>
                                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-bottom-0">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Recent Activities
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-user-plus text-success"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <small>New user registration</small>
                                            <div class="text-muted small">2 minutes ago</div>
                                        </div>
                                        <span class="badge badge-success">New</span>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-flask text-primary"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <small>Praktikum schedule updated</small>
                                            <div class="text-muted small">1 hour ago</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-database text-info"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <small>System backup completed</small>
                                            <div class="text-muted small">3 hours ago</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-shield-alt text-warning"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <small>Security audit passed</small>
                                            <div class="text-muted small">Yesterday</div>
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
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.card-outline {
    border-top: 3px solid;
}

.btn-outline-primary, 
.btn-outline-success, 
.btn-outline-warning, 
.btn-outline-info, 
.btn-outline-danger, 
.btn-outline-secondary {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover, 
.btn-outline-success:hover, 
.btn-outline-warning:hover, 
.btn-outline-info:hover, 
.btn-outline-danger:hover, 
.btn-outline-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.badge {
    border-radius: 6px;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>