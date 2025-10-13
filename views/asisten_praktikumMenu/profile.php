<?php
// views/asisten_praktikumMenu/profile.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

checkAuth();
checkRole(['asisten_praktikum', 'staff_lab']);

$page_title = "Profile User";

// Inisialisasi koneksi database
$database = new Database();
$pdo = $database->getConnection();

// Ambil data user
$users_id = $_SESSION['users_id'] ?? $_SESSION['user_id'] ?? null;

if (!$users_id) {
    $_SESSION['error'] = "User ID tidak ditemukan. Silakan login kembali.";
    header("Location: login.php");
    exit;
}

$query = "SELECT u.*, ap.kelas, p.nama_praktikum 
          FROM users u 
          LEFT JOIN asisten_praktikum ap ON u.nim = ap.nim 
          LEFT JOIN praktikum p ON ap.praktikum_id = p.id 
          WHERE u.id = :users_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':users_id' => $users_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profile User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Profile</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="user-profile-image mb-3">
                                        <i class="fas fa-user-circle fa-6x text-primary"></i>
                                    </div>
                                    <a href="profile_settings.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-signature"></i> Atur Tanda Tangan
                                    </a>
                                </div>
                                <div class="col-md-9">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Username</th>
                                            <td><?= htmlspecialchars($user['username'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <td><?= htmlspecialchars($user['nama'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>NIM</th>
                                            <td><?= htmlspecialchars($user['nim'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Role</th>
                                            <td>
                                                <span class="badge badge-info"><?= htmlspecialchars($user['role'] ?? '-') ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Praktikum</th>
                                            <td><?= htmlspecialchars($user['nama_praktikum'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Kelas</th>
                                            <td><?= htmlspecialchars($user['kelas'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status Tanda Tangan</th>
                                            <td>
                                                <?php if (!empty($user['signature_data'])): ?>
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Tersedia
                                                    </span>
                                                    <small class="text-muted d-block">
                                                        Terakhir update: <?= date('d/m/Y H:i', strtotime($user['signature_updated_at'] ?? 'now')) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> Belum Diatur
                                                    </span>
                                                    <a href="profile_settings.php" class="btn btn-sm btn-outline-primary mt-1">
                                                        Atur Sekarang
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Quick Actions -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="dashboard.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                </a>
                                <a href="../staff_lab/absen_asistenpraktikum.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-calendar-check mr-2"></i> Absen Sekarang
                                </a>
                                <a href="profile_settings.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-signature mr-2"></i> Atur Tanda Tangan
                                </a>
                                <a href="../auth/logout.php" class="list-group-item list-group-item-action text-danger">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Status Info -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Status Sistem</h3>
                        </div>
                        <div class="card-body">
                            <div class="small">
                                <p><strong>Auto Signature:</strong> 
                                    <?php if (!empty($user['signature_data'])): ?>
                                        <span class="text-success">AKTIF</span>
                                    <?php else: ?>
                                        <span class="text-danger">NON-AKTIF</span>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Role:</strong> <?= htmlspecialchars($user['role'] ?? '-') ?></p>
                                <p><strong>Login sebagai:</strong> <?= htmlspecialchars($_SESSION['username'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>