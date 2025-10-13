<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

checkAuth();
checkRole(['asisten_praktikum', 'staff_lab']);

$page_title = "Dashboard Asisten Praktikum";

// ✅ PERBAIKAN: Inisialisasi koneksi database
$database = new Database();
$pdo = $database->getConnection();

// ✅ PERBAIKAN: Query yang benar untuk tabel users (bukan user)
$user_id = $_SESSION['users_id'] ?? $_SESSION['user_id'] ?? null; // Coba kedua kemungkinan

if ($user_id) {
    // ✅ PERBAIKAN: Menggunakan tabel users (bukan user)
    $check_sig = "SELECT signature_data FROM users WHERE id = :user_id";
    $stmt_sig = $pdo->prepare($check_sig);
    $stmt_sig->execute([':user_id' => $user_id]);
    $user_sig = $stmt_sig->fetch(PDO::FETCH_ASSOC);
} else {
    $user_sig = null;
}

// Get user info for welcome message
$username = $_SESSION['username'] ?? 'Asisten';
$nama = $_SESSION['nama'] ?? 'Asisten Praktikum';
$role = $_SESSION['role'] ?? 'Asisten';
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <!-- Main Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <div class="d-flex align-items-center">
                        <div class="bg-gradient-primary p-3 rounded-circle mr-3">
                            <i class="fas fa-user-cog fa-2x text-white"></i>
                        </div>
                        <div>
                            <h1 class="m-0 text-dark">Dashboard Asisten</h1>
                            <small class="text-muted">Selamat datang, <strong><?= htmlspecialchars($nama) ?></strong></small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 text-right">
                    <div class="btn-group">
                        <a href="profile_settings.php" class="btn btn-outline-primary">
                            <i class="fas fa-signature mr-2"></i>Atur Tanda Tangan
                        </a>
                        <a href="?page=absensi" class="btn btn-primary ml-2">
                            <i class="fas fa-users mr-2"></i>Input Absensi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Signature Alert -->
            <?php if (!$user_sig || empty($user_sig['signature_data'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <div class="alert-icon mr-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Tanda Tangan Belum Diatur!</h5>
                        <p class="mb-0">Anda belum mengatur tanda tangan digital. Tanda tangan akan digunakan otomatis saat absen.</p>
                    </div>
                    <div class="ml-3">
                        <a href="profile_settings.php" class="btn btn-warning btn-sm">
                            <i class="fas fa-signature mr-1"></i>Atur Sekarang
                        </a>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php else: ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <div class="alert-icon mr-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">Tanda Tangan Siap Digunakan!</h5>
                        <p class="mb-0">Tanda tangan digital Anda sudah tersimpan dan siap digunakan untuk absensi.</p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Quick Stats Cards -->
            <div class="row">
                <!-- Input Absensi Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Input Absensi
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Praktikan</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="?page=absensi" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-arrow-right mr-1"></i>Input Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rekap Data Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Data Rekap
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Absensi</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="rekap_absensi.php" class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-chart-line mr-1"></i>Lihat Rekap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Profile & Signature
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Pengaturan</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-cog fa-2x text-gray-300"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="profile_settings.php" class="btn btn-warning btn-sm btn-block">
                                    <i class="fas fa-cog mr-1"></i>Kelola Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Signature Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Status Signature
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php if ($user_sig && !empty($user_sig['signature_data'])): ?>
                                            <span class="badge badge-success">Ready</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Not Set</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-signature fa-2x text-gray-300"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="profile_settings.php" class="btn btn-info btn-sm btn-block">
                                    <i class="fas fa-sync-alt mr-1"></i>Check Status
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="row">
                <!-- Quick Guide -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-gradient-primary">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-graduation-cap mr-2"></i>Panduan Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
<div class="list-group-item d-flex align-items-center">
    <div class="bg-primary rounded-circle p-3 mr-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
        <i class="fas fa-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Input Absensi Praktikan</h6>
                                        <small class="text-muted">Klik tombol "Input Sekarang" untuk mengisi absensi harian</small>
                                    </div>
                                </div>
<div class="list-group-item d-flex align-items-center">
    <div class="bg-success rounded-circle p-3 mr-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
        <i class="fas fa-2 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Atur Tanda Tangan</h6>
                                        <small class="text-muted">Pastikan tanda tangan sudah diatur untuk validasi otomatis</small>
                                    </div>
                                </div>
<div class="list-group-item d-flex align-items-center">
    <div class="bg-info rounded-circle p-3 mr-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
        <i class="fas fa-3 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Lihat Rekap Data</h6>
                                        <small class="text-muted">Pantau perkembangan absensi melalui menu rekap</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Features -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-gradient-success">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-star mr-2"></i>Fitur Sistem
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item text-center p-3 border rounded">
                                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                        <h6>Validasi Real-time</h6>
                                        <small class="text-muted">Signature otomatis</small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item text-center p-3 border rounded">
                                        <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                        <h6>Responsive Design</h6>
                                        <small class="text-muted">Akses dari device apapun</small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item text-center p-3 border rounded">
                                        <i class="fas fa-chart-pie fa-2x text-info mb-2"></i>
                                        <h6>Analytics</h6>
                                        <small class="text-muted">Laporan lengkap</small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="feature-item text-center p-3 border rounded">
                                        <i class="fas fa-shield-alt fa-2x text-warning mb-2"></i>
                                        <h6>Secure</h6>
                                        <small class="text-muted">Data terproteksi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-gradient-info">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-bolt mr-2"></i>Aksi Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="?page=absensi" class="btn btn-outline-primary btn-lg w-100 h-100 py-3">
                                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                        <span>Input Absensi</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="rekap_absensi.php" class="btn btn-outline-success btn-lg w-100 h-100 py-3">
                                        <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                        <span>Lihat Rekap</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="profile_settings.php" class="btn btn-outline-warning btn-lg w-100 h-100 py-3">
                                        <i class="fas fa-signature fa-2x mb-2 d-block"></i>
                                        <span>Atur Signature</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="export_data.php" class="btn btn-outline-info btn-lg w-100 h-100 py-3">
                                        <i class="fas fa-download fa-2x mb-2 d-block"></i>
                                        <span>Export Data</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
if (isset($_GET['page']) && $_GET['page'] == 'absensi') {
    include __DIR__ . '/absensi.php';
}
?>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<style>
/* Custom Styles for Better UI/UX */
.content-header {
    padding: 20px 0;
}

.card {
    border: none;
    border-radius: 10px;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%) !important;
}

.feature-item {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0 !important;
}

.feature-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: #4e73df !important;
}

.alert {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.alert-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.list-group-item {
    border: none;
    padding: 1rem 0.5rem;
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.btn-block {
    border-radius: 6px;
    font-weight: 500;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .content-header .col-sm-4 {
        text-align: left !important;
        margin-top: 15px;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-group .btn {
        margin-left: 0 !important;
    }
    
    .card-body .row.text-center .col-md-3 {
        margin-bottom: 15px;
    }
}

@media (max-width: 576px) {
    .d-flex.align-items-center {
        flex-direction: column;
        text-align: center;
    }
    
    .bg-gradient-primary.p-3.rounded-circle.mr-3 {
        margin-right: 0 !important;
        margin-bottom: 15px;
    }
    
    .alert .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .alert .ml-3 {
        margin-left: 0 !important;
        margin-top: 15px;
    }
}
</style>