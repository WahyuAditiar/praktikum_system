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
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Asisten Praktikum</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="profile_settings.php" class="btn btn-info btn-sm">
                                <i class="fas fa-signature"></i> Atur Tanda Tangan
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Alert jika belum ada signature -->
            <?php if (!$user_sig || empty($user_sig['signature_data'])): ?>
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
                Anda belum mengatur tanda tangan. <a href="profile_settings.php" class="alert-link">Klik di sini</a> untuk mengatur tanda tangan digital Anda.
                Tanda tangan akan digunakan otomatis saat absen.
            </div>
            <?php endif; ?>

            <!-- Widget Dashboard -->
            <div class="row">
<<<<<<< HEAD
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>Absen</h3>
                            <p>Presensi Harian</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="../staff_lab/absen_asistenpraktikum.php" class="small-box-footer">
                            Absen Sekarang <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>150</h3>
=======
                <!-- Widget Input Absensi Praktikan -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>Input</h3>
>>>>>>> 43c0fe9 (update_backup_kesalahan commite)
                            <p>Absen Praktikan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
<<<<<<< HEAD
                        <a href="mahasiswa.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
=======
                        <a href="absensi.php" class="small-box-footer">
  Input Sekarang <i class="fas fa-arrow-circle-right"></i>
</a>
                    </div>
                </div>

                <!-- Widget Lihat Rekap -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>Rekap</h3>
                            <p>Data Absensi</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <a href="?page=absensi" class="small-box-footer">
                            Lihat Rekap <i class="fas fa-arrow-circle-right"></i>
>>>>>>> 43c0fe9 (update_backup_kesalahan commite)
                        </a>
                    </div>
                </div>

                <!-- Widget Profile -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>Profile</h3>
                            <p>Atur Tanda Tangan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-signature"></i>
                        </div>
                        <a href="profile_settings.php" class="small-box-footer">
                            Atur Sekarang <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Widget Status Signature -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>Status</h3>
                            <p>
                                <?php if ($user_sig && !empty($user_sig['signature_data'])): ?>
                                    <span class="badge badge-success">Signature Ready</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No Signature</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="profile_settings.php" class="small-box-footer">
                            Check Status <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Info Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
<<<<<<< HEAD
                                <i class="fas fa-info-circle mr-2"></i>Informasi Sistem Tanda Tangan
=======
                                <i class="fas fa-info-circle mr-2"></i>Sistem Absensi Praktikan
>>>>>>> 43c0fe9 (update_backup_kesalahan commite)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
<<<<<<< HEAD
                                    <h5>Keuntungan Sistem Baru:</h5>
                                    <ul>
                                        <li>✅ <strong>Hemat Storage</strong> - 1 signature untuk semua absen</li>
                                        <li>✅ <strong>Konsisten</strong> - Signature sama setiap kali absen</li>
                                        <li>✅ <strong>Praktis</strong> - Tidak perlu tanda tangan manual tiap absen</li>
                                        <li>✅ <strong>Auto-fill</strong> - Signature otomatis terisi saat absen</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5>Cara Penggunaan:</h5>
                                    <ol>
                                        <li>Klik "Atur Tanda Tangan" di kanan atas</li>
                                        <li>Gambar tanda tangan di canvas</li>
                                        <li>Simpan ke profile</li>
                                        <li>Klik "Absen Sekarang" - signature otomatis terisi</li>
                                    </ol>
                                    <?php if (!$user_sig || empty($user_sig['signature_data'])): ?>
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-lightbulb mr-2"></i>
                                            <strong>Tip:</strong> Atur tanda tangan Anda sekarang untuk pengalaman absen yang lebih mudah!
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-success mt-3">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            <strong>Siap!</strong> Signature Anda sudah tersimpan. Klik "Absen Sekarang" untuk mencoba.
                                        </div>
                                    <?php endif; ?>
=======
                                    <h5>Fitur Absensi Praktikan:</h5>
                                    <ul>
                                        <li>✅ <strong>Input Kehadiran</strong> - Hadir, Sakit, Izin, Alfa</li>
                                        <li>✅ <strong>Multiple Pertemuan</strong> - Pertemuan 1 sampai 16</li>
                                        <li>✅ <strong>Rekap Data</strong> - Lihat history absensi</li>
                                        <li>✅ <strong>Export Data</strong> - Download laporan</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5>Cara Menggunakan:</h5>
                                    <ol>
                                        <li>Klik "Input Sekarang" untuk mengisi absensi</li>
                                        <li>Pilih mata kuliah dan pertemuan</li>
                                        <li>Input status kehadiran praktikan</li>
                                        <li>Klik "Entri Absen" untuk menyimpan</li>
                                        <li>Gunakan "Lihat Rekap" untuk melihat data</li>
                                    </ol>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-lightbulb mr-2"></i>
                                        <strong>Tip:</strong> Pastikan tanda tangan sudah diatur untuk validasi absensi.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <a href="?page=absensi" class="btn btn-primary btn-lg mr-3">
                                            <i class="fas fa-users mr-2"></i>Input Absensi Praktikan
                                        </a>
                                        <a href="profile_settings.php" class="btn btn-warning btn-lg">
                                            <i class="fas fa-signature mr-2"></i>Atur Tanda Tangan
                                        </a>
                                    </div>
>>>>>>> 43c0fe9 (update_backup_kesalahan commite)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<<<<<<< HEAD
<?php include __DIR__ . '/../templates/footer.php'; ?>
=======


<?php
if (isset($_GET['page']) && $_GET['page'] == 'absensi') {
    include __DIR__ . '/absensi.php';
} ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>
>>>>>>> 43c0fe9 (update_backup_kesalahan commite)
