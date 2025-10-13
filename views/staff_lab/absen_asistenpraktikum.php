<?php
// views/staff_lab/absen_asistenpraktikum.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ PERBAIKAN: LOAD REQUIRED FILES TERLEBIH DAHULU
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AbsensiAsistenController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/PraktikumModel.php';

checkAuth();
checkRole(['asisten_praktikum', 'staff_lab', 'admin']);

// ✅ PERBAIKAN: INISIALISASI DATABASE TERLEBIH DAHULU
$database = new Database();
$pdo = $database->getConnection();

// ✅ PERBAIKAN: FORCE RELOAD SIGNATURE DARI DATABASE - SETELAH $pdo ADA
if (!empty($_SESSION['username'])) {
    $force_reload_sql = "SELECT signature_data FROM users WHERE username = ?";
    $force_reload_stmt = $pdo->prepare($force_reload_sql);
    $force_reload_stmt->execute([$_SESSION['username']]);
    $latest_signature = $force_reload_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($latest_signature && !empty($latest_signature['signature_data'])) {
        $_SESSION['user_signature_data'] = $latest_signature['signature_data'];
        $_SESSION['has_signature'] = true;
    } else {
        unset($_SESSION['user_signature_data']);
        unset($_SESSION['has_signature']);
    }
}

// Set base URL untuk uploads
$base_url = "http://" . $_SERVER['HTTP_HOST'] . str_replace('/views/staff_lab', '', dirname($_SERVER['PHP_SELF']));
$base_url = rtrim($base_url, '/\\');
$uploads_base_url = 'http://localhost/praktikum_system/uploads';

$controller = new AbsensiAsistenController($pdo);
$praktikumModel = new PraktikumModel($pdo);

// ambil daftar asisten (untuk staff/admin memilih NIM)
$asistenList = $controller->getAsistenList();

// ambil data user dari session
$currentUser = [
    'username' => $_SESSION['username'] ?? '',
    'nim'      => $_SESSION['nim'] ?? '',
    'nama'     => $_SESSION['nama'] ?? '',
    'role'     => $_SESSION['role'] ?? '',
    'kelas'    => $_SESSION['kelas'] ?? '',
    'nama_praktikum' => $_SESSION['nama_praktikum'] ?? ''
];

$currentRole = $currentUser['role'];

// ambil list praktikum berdasarkan nama asisten
$praktikumList = [];
if (!empty($currentUser['nama'])) {
    $praktikumList = $praktikumModel->getByAsistenNama($currentUser['nama']);
}

// ✅ PERBAIKAN: Signature check yang sederhana dan efektif
$has_signature = false;
$user_signature_data = '';

// Cek signature dari session (setelah force reload)
if (!empty($_SESSION['user_signature_data'])) {
    $has_signature = true;
    $user_signature_data = $_SESSION['user_signature_data'];
}

// Debug info
error_log("Signature Check - Username: " . $currentUser['username'] . ", NIM: " . $currentUser['nim'] . ", Has Signature: " . ($has_signature ? 'YES' : 'NO'));

// pesan redirect
$error = $success = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $success = $_GET['message'] ?? 'Berhasil memproses data';
    } elseif ($_GET['status'] === 'error') {
        $error = $_GET['message'] ?? 'Terjadi kesalahan';
    }
}

// PROSES FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prioritaskan aksi validasi pulang
    if (isset($_POST['validasi_pulang'])) {
        $res = $controller->validasiPulang($_POST);
        if ($res['success']) {
            echo '<script>window.location.href = "absen_asistenpraktikum.php?status=success&message=' . urlencode($res['message']) . '";</script>';
        } else {
            echo '<script>window.location.href = "absen_asistenpraktikum.php?status=error&message=' . urlencode($res['message']) . '";</script>';
        }
        exit;
    }

    // Aksi tambah/edit/hapus absensi masuk
    if (isset($_POST['tambah_absen'])) {
        // ✅ Gunakan signature dari profile jika ada
        if ($has_signature && !empty($user_signature_data)) {
            $_POST['signature_data'] = $user_signature_data;
            $_POST['auto_signature'] = '1';
            $_POST['status_hadir'] = 'hadir';
        }
        
        $res = $controller->create($_POST, $_FILES, $currentUser['username']);
    } elseif (isset($_POST['edit_absen'])) {
        $id = (int)($_POST['id'] ?? 0);
        $res = $controller->update($id, $_POST, $_FILES, $currentUser['username']);
    } elseif (isset($_POST['hapus_absen'])) {
        $id = (int)($_POST['id'] ?? 0);
        $ok = $controller->delete($id);
        echo '<script>window.location.href = "absen_asistenpraktikum.php?status=' . ($ok ? 'success' : 'error') . '&message=' . urlencode($ok ? 'Berhasil menghapus.' : 'Gagal menghapus.') . '";</script>';
        exit;
    }

    if (!empty($res)) {
        if ($res['success']) {
            echo '<script>window.location.href = "absen_asistenpraktikum.php?status=success&message=' . urlencode($res['message']) . '";</script>';
        } else {
            echo '<script>window.location.href = "absen_asistenpraktikum.php?status=error&message=' . urlencode(implode(', ', $res['errors'] ?? [$res['message'] ?? 'Error'])) . '";</script>';
        }
        exit;
    }
}

// ambil daftar absensi
$onlyOwn = ($currentRole === 'asisten_praktikum');
$rows = $controller->getAll($onlyOwn, $currentUser['username']);

// include header/sidebar
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';
?>

<!-- ✅ DEBUG SIGNATURE STATUS -->
<div class="alert alert-info d-none"> <!-- tambahkan class d-none untuk hide -->
    <strong>Debug Signature Status:</strong><br>
    Session Signature: <?= !empty($_SESSION['user_signature_data']) ? 'ADA (' . strlen($_SESSION['user_signature_data']) . ' chars)' : 'TIDAK ADA' ?><br>
    Username: <?= $currentUser['username'] ?><br>
    Has Signature: <?= $has_signature ? 'YES' : 'NO' ?>
</div>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Absensi Asisten Praktikum</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <!-- ✅ ALERT SIGNATURE STATUS -->
            <?php if (!$has_signature && $currentRole === 'asisten_praktikum'): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Signature Belum Diatur!</h5>
                    Anda belum mengatur tanda tangan digital. 
                    <a href="../asisten_praktikumMenu/profile_settings.php" class="alert-link">Klik di sini</a> 
                    untuk mengatur tanda tangan terlebih dahulu. 
                    <strong>Signature akan digunakan otomatis saat absen.</strong>
                </div>
            <?php elseif ($has_signature): ?>
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check-circle"></i> Signature Siap Digunakan!</h5>
                    Tanda tangan digital Anda sudah tersimpan. 
                    <strong>Signature akan otomatis digunakan saat Anda absen.</strong>
                    <a href="../asisten_praktikumMenu/profile_settings.php" class="alert-link">Kelola signature</a>
                </div>
            <?php endif; ?>

            <!-- Form Absensi Masuk -->
            <form method="POST" enctype="multipart/form-data" id="formAbsensi">
                <input type="hidden" name="tambah_absen" value="1">
                <input type="hidden" name="signature_data" id="signatureData" value="<?= htmlspecialchars($user_signature_data) ?>">

                <!-- Data Asisten -->
                <div class="card card-primary mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-circle mr-2"></i>Data Asisten</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if ($currentRole === 'staff_lab' || $currentRole === 'admin'): ?>
                                <!-- Form untuk staff/admin -->
                                <div class="col-md-2 mb-3">
                                    <label class="font-weight-bold">NIM Asisten</label>
                                    <select class="form-control" id="nim" name="nim" required>
                                        <option value="">-- Pilih NIM --</option>
                                        <?php foreach ($asistenList as $a): ?>
                                            <option value="<?= htmlspecialchars($a['nim']); ?>"
                                                data-nama="<?= htmlspecialchars($a['nama']); ?>"
                                                data-praktikum="<?= htmlspecialchars($a['nama_praktikum'] ?? ''); ?>"
                                                data-kelas="<?= htmlspecialchars($a['kelas'] ?? ''); ?>"
                                                data-tahun="<?= htmlspecialchars($a['tahun_ajaran'] ?? ''); ?>">
                                                <?= htmlspecialchars($a['nim']); ?> - <?= htmlspecialchars($a['nama']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="font-weight-bold">Nama Asisten</label>
                                    <input type="text" id="nama" name="nama" class="form-control bg-light" readonly>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="font-weight-bold">Praktikum</label>
                                    <select id="praktikum_name" name="praktikum_name" class="form-control" required>
                                        <option value="">-- Pilih Praktikum --</option>
                                        <?php foreach ($praktikumList as $p): ?>
                                            <option value="<?= htmlspecialchars($p['nama_praktikum']) ?>"
                                                data-kelas="<?= htmlspecialchars($p['kelas'] ?? '') ?>"
                                                data-praktikum-id="<?= htmlspecialchars($p['id'] ?? '') ?>">
                                                <?= htmlspecialchars($p['nama_praktikum'] ?? '-') ?>
                                                (Kelas <?= htmlspecialchars($p['kelas'] ?? '-') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" id="praktikum_id" name="praktikum_id">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="font-weight-bold">Kelas</label>
                                    <input type="text" id="kelas" name="kelas" class="form-control bg-light" readonly>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="font-weight-bold">Tahun Ajaran</label>
                                    <select class="form-control" id="tahun_ajaran" name="tahun_ajaran" required>
                                        <option value="">-- Pilih Tahun --</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($i = 0; $i <= 3; $i++) {
                                            $year = $currentYear - $i;
                                            $tahunOption = $year . '/' . ($year + 1);
                                            echo "<option value='$tahunOption'>$tahunOption</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <!-- Form untuk asisten -->
                                <input type="hidden" id="nim" name="nim" value="<?= htmlspecialchars($currentUser['nim']); ?>">
                                <div class="col-md-2 mb-3">
                                    <label class="font-weight-bold">NIM Asisten</label>
                                    <div class="form-control bg-light"><?= htmlspecialchars($currentUser['nim'] ?? '-') ?></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="font-weight-bold">Nama Asisten</label>
                                    <div class="form-control bg-light"><?= htmlspecialchars($currentUser['nama'] ?? '-') ?></div>
                                    <input type="hidden" name="nama" value="<?= htmlspecialchars($currentUser['nama'] ?? '') ?>">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="font-weight-bold">Praktikum</label>
                                    <select id="praktikum_name" name="praktikum_name" class="form-control" required onchange="updateKelas()">
                                        <option value="">-- Pilih Praktikum --</option>
                                        <?php foreach ($praktikumList as $p): ?>
                                            <option value="<?= htmlspecialchars($p['nama_praktikum']) ?>"
                                                data-kelas="<?= htmlspecialchars($p['kelas'] ?? '') ?>"
                                                data-praktikum-id="<?= htmlspecialchars($p['id'] ?? '') ?>">
                                                <?= htmlspecialchars($p['nama_praktikum'] ?? '-') ?>
                                                (Kelas <?= htmlspecialchars($p['kelas'] ?? '-') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" id="praktikum_id" name="praktikum_id">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="font-weight-bold">Kelas</label>
                                    <input type="text" id="kelas" name="kelas" class="form-control bg-light" readonly placeholder="Pilih praktikum terlebih dahulu">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="font-weight-bold">Tahun Ajaran</label>
                                    <select class="form-control" id="tahun_ajaran" name="tahun_ajaran" required>
                                        <option value="">-- Pilih Tahun --</option>
                                        <?php
                                        if (!empty($currentUser['nim'])) {
                                            $riwayatTahun = $controller->getRiwayatTahunAjaran($currentUser['nim']);
                                            $currentTahun = date('Y') . '/' . (date('Y') + 1);

                                            foreach ($riwayatTahun as $tahun) {
                                                $selected = ($tahun == $currentTahun) ? 'selected' : '';
                                                echo "<option value='$tahun' $selected>$tahun</option>";
                                            }

                                            if (!in_array($currentTahun, $riwayatTahun)) {
                                                echo "<option value='$currentTahun'>$currentTahun (Tahun Baru)</option>";
                                            }
                                        } else {
                                            $currentYear = date('Y');
                                            for ($i = 0; $i <= 2; $i++) {
                                                $year = $currentYear - $i;
                                                $tahunOption = $year . '/' . ($year + 1);
                                                $selected = ($i == 0) ? 'selected' : '';
                                                echo "<option value='$tahunOption' $selected>$tahunOption</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Info Riwayat Tahun Ajaran -->
                        <?php if ($currentRole === 'asisten_praktikum' && !empty($currentUser['nim'])): ?>
                            <?php
                            $riwayatTahun = $controller->getRiwayatTahunAjaran($currentUser['nim']);
                            if (count($riwayatTahun) > 1): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info alert-dismissible fade show mt-2" style="padding: 8px 15px; font-size: 0.9rem;">
                                            <i class="fas fa-history mr-2"></i>
                                            <strong>Riwayat Tahun Ajaran:</strong>
                                            <?= implode(', ', $riwayatTahun) ?>
                                            <button type="button" class="close" data-dismiss="alert" style="padding: 0.5rem;">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Absensi Masuk -->
                <div class="card card-success mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sign-in-alt mr-2"></i>Absensi Masuk</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Pertemuan & Tanggal -->
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Pertemuan</label>
                                <select name="pertemuan" class="form-control" required>
                                    <option value="">Pilih Pertemuan</option>
                                    <option value="Briefing">Briefing</option>
                                    <?php for ($i = 1; $i <= 14; $i++): ?>
                                        <option value="<?= $i ?>">Pertemuan <?= $i ?></option>
                                    <?php endfor; ?>
                                    <option value="Presentasi Tugas Akhir">Presentasi Tugas Akhir</option>
                                    <option value="Pengisian Nilai Akhir">Pengisian Nilai Akhir</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggalMasuk" class="form-control bg-light" readonly>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Jam Masuk</label>
                                <input type="time" name="jam_mulai" id="jamMulai" class="form-control bg-light" readonly>
                            </div>

                            <!-- Status Kehadiran dengan Auto Signature -->
                            <div class="col-md-3 mb-3">
                                <label class="font-weight-bold">Status Kehadiran</label>
                                <div class="input-group">
                                    <select name="status_hadir" class="form-control" id="statusSelect" onchange="handleStatusChange()" required>
                                        <option value="">Pilih Status</option>
                                        <option value="hadir" <?= $has_signature ? 'selected' : '' ?>>Hadir</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="izin">Izin</option>
                                        <option value="alpha">Alpha</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" disabled>
                                            <i class="fas fa-signature"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted" id="signatureInfo">
                                    <?php if ($has_signature): ?>
                                        <i class="fas fa-check-circle text-success"></i> 
                                        <strong>Status "Hadir" dipilih otomatis</strong> - Signature dari profile akan digunakan
                                    <?php else: ?>
                                        <i class="fas fa-exclamation-triangle text-warning"></i> 
                                        Anda belum memiliki signature. 
                                        <a href="../asisten_praktikumMenu/profile_settings.php">Atur signature di profile</a>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>

                        <!-- Foto Bukti -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Foto Bukti <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input" id="fotoInput" accept="image/*" capture="environment" required>
                                    <label class="custom-file-label" for="fotoInput">Pilih file foto...</label>
                                </div>
                                <small class="form-text text-muted">Ambil foto selfie atau dokumentasi kegiatan praktikum</small>
                            </div>

                            <!-- Laporan Opsional -->
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Laporan (Opsional)</label>
                                <div class="custom-file">
                                    <input type="file" name="laporan" class="custom-file-input" id="laporanInput" accept=".pdf,.doc,.docx,.jpg,.png">
                                    <label class="custom-file-label" for="laporanInput">Pilih file laporan...</label>
                                </div>
                                <small class="form-text text-muted">PDF, DOC, atau gambar (maks. 5MB)</small>
                            </div>
                        </div>

                        <!-- GPS dengan Tombol Peta -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold">Lokasi GPS <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="gps_lat" class="form-control" placeholder="Latitude" readonly>
                                    <input type="text" name="gps_lng" class="form-control" placeholder="Longitude" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-success" id="btnGetLocation">
                                            <i class="fas fa-map-marker-alt mr-1"></i> Ambil Lokasi
                                        </button>
                                        <button type="button" class="btn btn-outline-info" id="btnMapModal" data-toggle="modal" data-target="#mapModal">
                                            <i class="fas fa-map mr-1"></i> Lihat Peta
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Klik "Ambil Lokasi" untuk mendapatkan koordinat GPS Anda</small>
                            </div>
                        </div>

                        <!-- ✅ INFO AUTO SIGNATURE -->
                        <?php if ($has_signature): ?>
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-robot mr-2"></i>
                                <strong>Sistem Auto Signature Aktif!</strong><br>
                                Tanda tangan Anda akan otomatis diambil dari profile saat memilih status "Hadir".
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Signature Belum Diatur!</strong><br>
                                Anda belum mengatur tanda tangan digital. 
                                <a href="../asisten_praktikumMenu/profile_settings.php" class="alert-link">Klik di sini</a> 
                                untuk mengatur tanda tangan terlebih dahulu.
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-success btn-lg btn-block mt-3" id="submitButton">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Absensi Masuk
                        </button>
                    </div>
                </div>
            </form>

            <!-- Form Absensi Pulang -->
            <form method="POST" action="">
                <input type="hidden" name="validasi_pulang" value="1">
                <input type="hidden" name="nim" id="nim_pulang" value="<?= htmlspecialchars($currentUser['nim'] ?? '') ?>">
                <input type="hidden" name="praktikum_name" id="praktikum_name_pulang" value="">
                <input type="hidden" name="kelas" id="kelas_pulang" value="">
                <input type="hidden" name="pertemuan" id="pertemuan_pulang" value="">
                <input type="hidden" name="tanggal" id="tanggalPulang">

                <div class="card card-danger mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-sign-out-alt mr-2"></i>Absensi Pulang</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="font-weight-bold">Jam Akhir <span class="text-danger">*</span></label>
                                <input type="time" name="jam_akhir" id="jamAkhir" class="form-control" placeholder="Klik tombol untuk mengisi jam" readonly>
                                <small class="form-text text-muted">Jam akan terisi otomatis saat klik tombol</small>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="font-weight-bold">Info</label>
                                <div class="form-control bg-light">
                                    Pastikan Praktikum dan Pertemuan sudah dipilih di form atas sebelum validasi pulang
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning" onclick="isiJamAkhir()">
                            <i class="fas fa-clock mr-1"></i>Isi Jam Sekarang
                        </button>
                        <button type="submit" class="btn btn-danger" id="btnValidasiPulang" disabled>
                            <i class="fas fa-check-circle mr-1"></i>Validasi Pulang
                        </button>
                    </div>
                </div>
            </form>

            <!-- Button Export -->
            <div class="mb-3 text-right">
                <?php if ($currentRole === 'admin'): ?>
                    <a href="laporan_admin.php" class="btn btn-success">
                        <i class="fas fa-file-excel mr-2"></i> Export Rekap Kehadiran (Admin)
                    </a>
                    <small class="text-muted d-block">Buka halaman filter untuk download laporan</small>
                <?php else: ?>
                    <a href="export_rekap.php" class="btn btn-success">
                        <i class="fas fa-file-excel mr-2"></i> Export Rekap Kehadiran
                    </a>
                    <small class="text-muted d-block">Format sesuai template sistem</small>
                <?php endif; ?>
            </div>

            <!-- Tabel Daftar Absensi -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-2"></i>Daftar Absensi</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="absenTable" class="table table-striped table-bordered" style="width:100%">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Praktikum</th>
                                    <th>Kelas</th>
                                    <th>Pertemuan</th>
                                    <th>Tanggal</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Akhir</th>
                                    <th>Status</th>
                                    <th>Bukti</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                    <tr>
                                        <td colspan="12" class="text-center text-muted">Belum ada data absen</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $i => $r): ?>
                                        <tr>
                                            <td class="text-center"><?= $i + 1 ?></td>
                                            <td><?= htmlspecialchars($r['nim'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($r['nama'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($r['praktikum_name'] ?? '-') ?></td>
                                            <td class="text-center"><?= htmlspecialchars($r['kelas'] ?? '-') ?></td>
                                            <td class="text-center"><?= htmlspecialchars($r['pertemuan'] ?? '-') ?></td>
                                            <td class="text-center"><?= htmlspecialchars($r['tanggal'] ?? '-') ?></td>
                                            <td class="text-center"><?= htmlspecialchars($r['jam_mulai'] ?? '-') ?></td>
                                            <td class="text-center"><?= htmlspecialchars($r['jam_akhir'] ?? '-') ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?=
                                                    ($r['status_hadir'] == 'hadir') ? 'success' : 
                                                    (($r['status_hadir'] == 'sakit') ? 'warning' : 
                                                    (($r['status_hadir'] == 'izin') ? 'info' : 'danger'))
                                                ?>">
                                                    <?= htmlspecialchars(ucfirst($r['status_hadir'] ?? '-')) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <!-- Signature -->
                                                    <?php if (!empty($r['signature_data'])): ?>
                                                        <?php
                                                        $signature_url = '';
                                                        if (strpos($r['signature_data'], 'data:image') === 0) {
                                                            $signature_url = $r['signature_data'];
                                                        } elseif (strpos($r['signature_data'], 'absen_asisten/') === 0) {
                                                            $signature_url = '/praktikum_system/uploads/' . $r['signature_data'];
                                                        } else {
                                                            $signature_url = '/praktikum_system/uploads/absen_asisten/' . basename($r['signature_data']);
                                                        }
                                                        ?>
                                                        <button type="button" class="btn btn-outline-primary btn-signature"
                                                            data-signature="<?= htmlspecialchars($signature_url) ?>"
                                                            title="Lihat Tanda Tangan">
                                                            <i class="fas fa-signature"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-outline-secondary" disabled title="Tidak ada tanda tangan">
                                                            <i class="fas fa-signature"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Foto -->
                                                    <?php if (!empty($r['foto_path'])): ?>
                                                        <?php
                                                        $foto_url = '';
                                                        if (strpos($r['foto_path'], 'absen_asisten/') === 0) {
                                                            $foto_url = '/praktikum_system/uploads/' . $r['foto_path'];
                                                        } else {
                                                            $foto_url = '/praktikum_system/uploads/absen_asisten/' . basename($r['foto_path']);
                                                        }
                                                        ?>
                                                        <button type="button" class="btn btn-outline-success btn-foto"
                                                            data-foto="<?= htmlspecialchars($foto_url) ?>"
                                                            title="Lihat Foto">
                                                            <i class="fas fa-camera"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-outline-secondary" disabled title="Tidak ada foto">
                                                            <i class="fas fa-camera"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Laporan -->
                                                    <?php if (!empty($r['laporan_path'])): ?>
                                                        <?php
                                                        $laporan_url = '';
                                                        if (strpos($r['laporan_path'], 'absen_asisten/') === 0) {
                                                            $laporan_url = '/praktikum_system/uploads/' . $r['laporan_path'];
                                                        } else {
                                                            $laporan_url = '/praktikum_system/uploads/absen_asisten/' . basename($r['laporan_path']);
                                                        }
                                                        ?>
                                                        <button type="button" class="btn btn-outline-info btn-laporan"
                                                            data-laporan="<?= htmlspecialchars($laporan_url) ?>"
                                                            data-filename="<?= htmlspecialchars(basename($r['laporan_path'])) ?>"
                                                            title="Download Laporan">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-outline-secondary" disabled title="Tidak ada laporan">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- GPS -->
                                                    <?php if (!empty($r['gps_lat']) && !empty($r['gps_lng'])): ?>
                                                        <button type="button" class="btn btn-outline-warning btn-gps"
                                                            data-lat="<?= htmlspecialchars($r['gps_lat']) ?>"
                                                            data-lng="<?= htmlspecialchars($r['gps_lng']) ?>"
                                                            title="Lihat Lokasi GPS">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-outline-secondary" disabled title="Tidak ada data GPS">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-danger btn-delete"
                                                        data-id="<?= $r['id'] ?>"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<!-- Sisanya modal dan JavaScript tetap sama seperti sebelumnya -->
<!-- ... (modal dan JavaScript code) ... -->

<!-- Include Leaflet CSS & JS untuk peta -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>



<script>
    // ========== SIGNATURE CANVAS ==========
    let signatureCanvas, signatureCtx;
    let isDrawing = false;

    // ========== GLOBAL VARIABLES ==========
    let liveMap, liveMarker;
    window.map = null; // Untuk preview GPS di tabel
    window.marker = null; // Untuk preview GPS di tabel

    // ========== AUTO SIGNATURE HANDLING ==========
    function handleStatusChange() {
        const statusSelect = document.getElementById('statusSelect');
        const selectedStatus = statusSelect.value;
        
        <?php if ($has_signature): ?>
        // Jika user punya signature dan memilih SELAIN "hadir", tampilkan warning
        if (selectedStatus !== 'hadir') {
            const confirmChange = confirm(
                'Anda memiliki signature di profile. ' +
                'Status "Hadir" akan menggunakan signature otomatis.\n\n' +
                'Yakin ingin mengubah status? Signature tidak akan digunakan.'
            );
            
            if (!confirmChange) {
                statusSelect.value = 'hadir';
                return;
            }
        }
        <?php else: ?>
        // Jika user tidak punya signature dan memilih "hadir", minta buat signature
        if (selectedStatus === 'hadir') {
            alert('Anda belum memiliki signature. Silakan buat tanda tangan terlebih dahulu.');
            $('#signatureInputModal').modal('show');
            setTimeout(() => {
                initSignatureCanvas();
            }, 100);
            
            // Reset ke pilihan sebelumnya
            statusSelect.value = '';
        }
        <?php endif; ?>
    }

    // ✅ AUTO-SET STATUS HADIR JIKA USER PUNYA SIGNATURE
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($has_signature): ?>
        // Set status kehadiran otomatis ke "Hadir"
        const statusSelect = document.getElementById('statusSelect');
        if (statusSelect) {
            statusSelect.value = 'hadir';
            
            // Tambahkan class untuk indikasi auto signature
            const statusContainer = statusSelect.closest('.card');
            if (statusContainer) {
                statusContainer.classList.add('auto-signature-active');
            }
            
            // Tampilkan notifikasi
            showAutoHideNotification('✅ Status "Hadir" dipilih otomatis. Signature dari profile akan digunakan.', 'success', 4000);
            
            console.log('Auto signature system activated for user with signature');
        }
        <?php endif; ?>
    });

    function initSignatureCanvas() {
        signatureCanvas = document.getElementById('signatureCanvas');
        if (!signatureCanvas) return;
        
        signatureCtx = signatureCanvas.getContext('2d');

        // Reset canvas
        signatureCtx.fillStyle = 'white';
        signatureCtx.fillRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        signatureCtx.lineWidth = 2;
        signatureCtx.lineCap = 'round';
        signatureCtx.lineJoin = 'round';
        signatureCtx.strokeStyle = '#000';

        // Clear existing event listeners
        signatureCanvas.removeEventListener('mousedown', startDrawing);
        signatureCanvas.removeEventListener('mousemove', draw);
        signatureCanvas.removeEventListener('mouseup', stopDrawing);
        signatureCanvas.removeEventListener('mouseout', stopDrawing);

        // Add new event listeners
        signatureCanvas.addEventListener('mousedown', startDrawing);
        signatureCanvas.addEventListener('mousemove', draw);
        signatureCanvas.addEventListener('mouseup', stopDrawing);
        signatureCanvas.addEventListener('mouseout', stopDrawing);
    }

    function startDrawing(e) {
        e.preventDefault();
        isDrawing = true;
        const rect = signatureCanvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        signatureCtx.beginPath();
        signatureCtx.moveTo(x, y);

        document.getElementById('signatureStatus').textContent = 'Sedang menggambar...';
        document.getElementById('signatureStatus').className = 'text-warning';
    }

    function draw(e) {
        if (!isDrawing) return;

        e.preventDefault();

        const rect = signatureCanvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        signatureCtx.lineTo(x, y);
        signatureCtx.stroke();
    }

    function stopDrawing() {
        isDrawing = false;
        signatureCtx.beginPath();
        document.getElementById('signatureStatus').textContent = 'Tanda tangan tersimpan sementara';
        document.getElementById('signatureStatus').className = 'text-success';
        updateSignatureData();
    }

    function updateSignatureData() {
        if (signatureCanvas) {
            document.getElementById('signatureData').value = signatureCanvas.toDataURL();
        }
    }

    function clearSignature() {
        if (signatureCtx) {
            signatureCtx.fillStyle = 'white';
            signatureCtx.fillRect(0, 0, signatureCanvas.width, signatureCanvas.height);
            document.getElementById('signatureStatus').textContent = 'Belum ada tanda tangan';
            document.getElementById('signatureStatus').className = 'text-muted';
            document.getElementById('signatureData').value = '';
        }
    }

    function saveSignature() {
        if (!signatureCanvas || signatureCanvas.toDataURL() === document.createElement('canvas').toDataURL()) {
            alert('Harap buat tanda tangan terlebih dahulu!');
            return;
        }

        updateSignatureData();
        $('#signatureInputModal').modal('hide');
        document.getElementById('statusSelect').value = 'hadir';
        showAutoHideNotification('Tanda tangan berhasil disimpan!', 'success');
    }

    // ========== GPS & MAP FUNCTIONS ==========
    function initLiveMap() {
        liveMap = L.map('liveMap').setView([-6.2, 106.8], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(liveMap);

        liveMap.on('click', function(e) {
            updateMapMarker(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateMapMarker(lat, lng) {
        if (liveMarker) {
            liveMap.removeLayer(liveMarker);
        }

        liveMarker = L.marker([lat, lng]).addTo(liveMap)
            .bindPopup(`Lokasi Terpilih<br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`)
            .openPopup();

        document.getElementById('modalLat').textContent = lat.toFixed(6);
        document.getElementById('modalLng').textContent = lng.toFixed(6);
        document.getElementById('locationStatus').textContent = 'Lokasi dipilih';
        document.getElementById('locationStatus').className = 'badge badge-success';

        getAddressFromCoordinates(lat, lng);
    }

    function getLocationFromMap() {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung GPS');
            return;
        }

        document.getElementById('locationStatus').textContent = 'Mendapatkan lokasi...';
        document.getElementById('locationStatus').className = 'badge badge-warning';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                updateMapMarker(lat, lng);
                liveMap.setView([lat, lng], 16);
            },
            function(error) {
                document.getElementById('locationStatus').textContent = 'Gagal mendapatkan lokasi';
                document.getElementById('locationStatus').className = 'badge badge-danger';
            }
        );
    }

    function useCurrentLocation() {
        const lat = document.getElementById('modalLat').textContent;
        const lng = document.getElementById('modalLng').textContent;

        if (lat === '-' || lng === '-') {
            alert('Silakan dapatkan lokasi terlebih dahulu!');
            return;
        }

        document.querySelector('input[name="gps_lat"]').value = lat;
        document.querySelector('input[name="gps_lng"]').value = lng;
        $('#mapModal').modal('hide');
        showAutoHideNotification('Lokasi berhasil disimpan!', 'success');
    }

    function getAddressFromCoordinates(lat, lng) {
        document.getElementById('modalAddress').textContent = 'Mengambil alamat...';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalAddress').textContent = data?.display_name || 'Alamat tidak ditemukan';
            })
            .catch(error => {
                document.getElementById('modalAddress').textContent = 'Gagal mengambil alamat';
            });
    }

    // ========== FORM HANDLERS ==========
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing handlers...');

        // Auto-fill kelas ketika praktikum dipilih
        const selectPraktikum = document.getElementById("praktikum_name");
        const inputKelas = document.getElementById("kelas");
        const inputPraktikumId = document.getElementById("praktikum_id");

        if (selectPraktikum && inputKelas) {
            selectPraktikum.addEventListener("change", function() {
                const selected = this.options[this.selectedIndex];
                const kelas = selected.getAttribute("data-kelas") || "";
                const praktikumId = selected.getAttribute("data-praktikum-id") || "";

                inputKelas.value = kelas;
                if (inputPraktikumId) inputPraktikumId.value = praktikumId;
                syncAbsensiData();
            });
        }

        // Staff/Admin: ketika pilih NIM, isi nama/praktikum/kelas/TAHUN AJARAN
        const nimSelect = document.getElementById('nim');
        if (nimSelect) {
            nimSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                document.getElementById('nama').value = opt.getAttribute('data-nama') || '';
                document.getElementById('praktikum_name').value = opt.getAttribute('data-praktikum') || '';
                document.getElementById('kelas').value = opt.getAttribute('data-kelas') || '';

                // AUTO-FILL TAHUN AJARAN JIKA ADA DATA
                const tahunAjaran = opt.getAttribute('data-tahun') || '';
                if (tahunAjaran) {
                    document.getElementById('tahun_ajaran').value = tahunAjaran;
                }

                syncAbsensiData();
            });
        }

        // Sync ketika pertemuan berubah
        const pertemuanSelect = document.querySelector('select[name="pertemuan"]');
        if (pertemuanSelect) {
            pertemuanSelect.addEventListener('change', syncAbsensiData);
        }

        // Sync ketika tahun ajaran berubah
        const tahunSelect = document.getElementById('tahun_ajaran');
        if (tahunSelect) {
            tahunSelect.addEventListener('change', syncAbsensiData);
        }

        // File input handlers
        document.getElementById('fotoInput')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file foto...';
            document.querySelector('.custom-file-label[for="fotoInput"]').textContent = fileName;
        });

        document.getElementById('laporanInput')?.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file laporan...';
            document.querySelector('.custom-file-label[for="laporanInput"]').textContent = fileName;
        });

        // GPS button handler
        document.getElementById('btnGetLocation')?.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser tidak mendukung GPS');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    document.querySelector('input[name="gps_lat"]').value = lat.toFixed(6);
                    document.querySelector('input[name="gps_lng"]').value = lng.toFixed(6);
                    showAutoHideNotification('Lokasi berhasil didapatkan!', 'success');
                },
                function(error) {
                    alert('Gagal mendapatkan lokasi: ' + error.message);
                }
            );
        });

        // Inisialisasi table handlers
        initTableBuktiHandlers();

        // Auto get location on page load
        setTimeout(() => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        document.querySelector('input[name="gps_lat"]').value = lat.toFixed(6);
                        document.querySelector('input[name="gps_lng"]').value = lng.toFixed(6);
                    },
                    function(error) {
                        console.log('GPS auto-location failed');
                    }
                );
            }
        }, 1000);
    });

    // ========== UTILITY FUNCTIONS ==========
    function syncAbsensiData() {
        const praktikum = document.querySelector('[name="praktikum_name"]')?.value || '';
        const pertemuan = document.querySelector('[name="pertemuan"]')?.value || '';
        const kelas = document.querySelector('[name="kelas"]')?.value || '';
        const tahunAjaran = document.querySelector('[name="tahun_ajaran"]')?.value || '';

        document.getElementById('praktikum_name_pulang').value = praktikum;
        document.getElementById('pertemuan_pulang').value = pertemuan;
        document.getElementById('kelas_pulang').value = kelas;
    }

    function isiJamAkhir() {
        const now = new Date();
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const formattedTime = `${jam}:${menit}`;

        // Isi jam akhir
        const jamAkhirInput = document.getElementById('jamAkhir');
        jamAkhirInput.value = formattedTime;

        // Enable button validasi pulang
        document.getElementById('btnValidasiPulang').disabled = false;

        // Tambahkan style untuk menunjukkan sudah terisi
        jamAkhirInput.style.backgroundColor = '#d4edda';
        jamAkhirInput.style.borderColor = '#28a745';
        jamAkhirInput.style.color = '#155724';
        jamAkhirInput.style.fontWeight = 'bold';

        // Tampilkan notifikasi otomatis
        showAutoHideNotification(`Jam akhir berhasil diisi: <strong>${formattedTime}</strong>`, 'success', 2500);
    }

    function showAutoHideNotification(message, type = 'success', duration = 3000) {
        // Hapus notifikasi sebelumnya jika ada
        const existingNotification = document.querySelector('.auto-hide-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Buat notifikasi baru
        const notification = document.createElement('div');
        notification.className = `alert auto-hide-notification notification-${type}`;
        notification.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas ${getNotificationIcon(type)} mr-2"></i>
                ${message}
            </div>
            <button type="button" class="close" onclick="this.parentElement.parentElement.remove()">
                <span>&times;</span>
            </button>
        </div>
    `;

        // Tambahkan ke body
        document.body.appendChild(notification);

        // Auto remove setelah duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
    }

    // Fungsi untuk get icon berdasarkan type
    function getNotificationIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle',
            'danger': 'fa-times-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    // ========== MODAL INITIALIZATION ==========
    $('#mapModal').on('shown.bs.modal', function() {
        setTimeout(() => {
            if (!liveMap) {
                initLiveMap();
            }
            liveMap.invalidateSize();
        }, 100);
    });

    $('#gpsModal').on('hidden.bs.modal', function() {
        if (liveMap) {
            liveMap.remove();
            liveMap = null;
            liveMarker = null;
        }
    });

    // ========== MODAL HANDLERS UNTUK BUKTI DI TABEL ==========
    function initTableBuktiHandlers() {
        console.log('Initializing table bukti handlers...');

        const signatureButtons = document.querySelectorAll('.btn-signature');
        const fotoButtons = document.querySelectorAll('.btn-foto');
        const laporanButtons = document.querySelectorAll('.btn-laporan');
        const gpsButtons = document.querySelectorAll('.btn-gps');
        const deleteButtons = document.querySelectorAll('.btn-delete');

        console.log('Found buttons:', {
            signature: signatureButtons.length,
            foto: fotoButtons.length,
            laporan: laporanButtons.length,
            gps: gpsButtons.length,
            delete: deleteButtons.length
        });

        // Signature Modal untuk tabel - PERBAIKAN UNTUK BASE64
        signatureButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const signaturePath = this.getAttribute('data-signature');
                console.log('Signature clicked:', signaturePath);
                
                const signatureImage = document.getElementById('signatureImage');
                
                // ✅ HANDLE BASE64 DAN REGULAR PATH
                if (signaturePath.startsWith('data:image')) {
                    // Jika base64, langsung gunakan
                    signatureImage.src = signaturePath;
                } else {
                    // Jika path file, tambahkan timestamp untuk avoid cache
                    signatureImage.src = signaturePath + '?t=' + new Date().getTime();
                }
                
                signatureImage.onerror = function() {
                    console.error('Gagal memuat signature:', signaturePath);
                    signatureImage.alt = 'Gagal memuat tanda tangan';
                    signatureImage.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5HYWdhbCBtdWF0IHRhbmRhIHRhbmdhbjwvdGV4dD48L3N2Zz4=';
                };

                $('#signatureModal').modal('show');
            });
        });

        // Foto Modal untuk tabel
        fotoButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const fotoPath = this.getAttribute('data-foto');
                console.log('Foto clicked:', fotoPath);

                const fotoImage = document.getElementById('fotoImage');
                fotoImage.src = '';
                fotoImage.alt = 'Memuat foto...';

                // Handle image load error
                fotoImage.onerror = function() {
                    console.error('Gagal memuat foto:', fotoPath);
                    fotoImage.alt = 'Gagal memuat foto';
                    fotoImage.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1zbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5HYWdhbCBtdWF0IGZvdG88L3RleHQ+PC9zdmc+';
                };

                fotoImage.onload = function() {
                    console.log('Foto berhasil dimuat:', fotoPath);
                };

                // Tambahkan timestamp untuk avoid cache
                fotoImage.src = fotoPath + '?t=' + new Date().getTime();
                $('#fotoModal').modal('show');
            });
        });

        // Laporan Handler untuk tabel
        laporanButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const laporanPath = this.getAttribute('data-laporan');
                const filename = this.getAttribute('data-filename');
                console.log('Laporan clicked:', laporanPath);

                // Buat link download
                const link = document.createElement('a');
                link.href = laporanPath;
                link.download = filename;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });

        // GPS Modal untuk tabel
        gpsButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                console.log('GPS clicked:', lat, lng);
                showGPSLocation(lat, lng);
            });
        });

        // Delete Handler untuk tabel
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                console.log('Delete clicked:', id);
                document.getElementById('deleteId').value = id;
                $('#deleteModal').modal('show');
            });
        });
    }

    // Fungsi untuk GPS preview di tabel
    function showGPSLocation(lat, lng) {
        document.getElementById('coordLat').textContent = lat;
        document.getElementById('coordLng').textContent = lng;
        document.getElementById('coordAddress').textContent = 'Mengambil alamat...';

        $('#gpsModal').modal('show');

        $('#gpsModal').on('shown.bs.modal', function() {
            initMap(lat, lng);
            getAddressFromCoordinates(lat, lng);
        });
    }

    function initMap(lat, lng) {
        // Hapus map existing jika ada
        if (window.map) {
            window.map.remove();
        }

        // Inisialisasi map
        window.map = L.map('map').setView([lat, lng], 16);

        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(window.map);

        // Tambahkan marker
        if (window.marker) {
            window.map.removeLayer(window.marker);
        }
        window.marker = L.marker([lat, lng]).addTo(window.map)
            .bindPopup('Lokasi Absensi<br>Lat: ' + lat + '<br>Lng: ' + lng)
            .openPopup();
    }

    // Cleanup ketika modal GPS ditutup
    $('#gpsModal').on('hidden.bs.modal', function() {
        if (window.map) {
            window.map.remove();
            window.map = null;
            window.marker = null;
        }
    });

    // ========== WAKTU REALTIME FUNCTIONS ==========
    function updateWaktuRealtime() {
        const now = new Date();

        // Format tanggal: YYYY-MM-DD
        const tahun = now.getFullYear();
        const bulan = String(now.getMonth() + 1).padStart(2, '0');
        const tanggal = String(now.getDate()).padStart(2, '0');
        const formattedDate = `${tahun}-${bulan}-${tanggal}`;

        // Format waktu: HH:MM
        const jam = String(now.getHours()).padStart(2, '0');
        const menit = String(now.getMinutes()).padStart(2, '0');
        const formattedTime = `${jam}:${menit}`;

        // Update form absensi masuk saja
        document.getElementById('tanggalMasuk').value = formattedDate;
        document.getElementById('jamMulai').value = formattedTime;

        // Update form absensi pulang (hanya tanggal)
        document.getElementById('tanggalPulang').value = formattedDate;
    }

    // Update waktu setiap detik (hanya untuk absensi masuk)
    setInterval(updateWaktuRealtime, 1000);

    // Jalankan sekali saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
        updateWaktuRealtime();

        // Tambahkan placeholder styling
        const jamAkhirInput = document.getElementById('jamAkhir');
        if (jamAkhirInput) {
            jamAkhirInput.style.color = '#6c757d';
            jamAkhirInput.style.fontStyle = 'italic';
        }
    });

    // Validasi form sebelum submit - dengan notifikasi
    document.querySelector('form[action=""]')?.addEventListener('submit', function(e) {
        const jamAkhir = document.getElementById('jamAkhir').value;

        if (!jamAkhir) {
            e.preventDefault();
            showAutoHideNotification('Silakan klik "Isi Jam Sekarang" terlebih dahulu!', 'warning', 3000);
            return false;
        }

        // Jika valid, tampilkan notifikasi sukses
        showAutoHideNotification('Absensi pulang berhasil disimpan!', 'success', 2500);
    });

    /* --- Sidebar Minimize Only Hide Text, Dropdown Tetap Berfungsi --- */
    <style>
    .sidebar.minimized .nav-link > p,
    .sidebar.minimized .menu-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
        transition: opacity 0.2s, width 0.2s;
    }
    .sidebar .nav-link > p,
    .sidebar .menu-text {
        transition: opacity 0.2s, width 0.2s;
    }
    </style>
    <script>
    // Toggle sidebar minimize (hanya sembunyikan teks menu)
    document.addEventListener('DOMContentLoaded', function() {
        var sidebar = document.querySelector('.sidebar, .main-sidebar');
        var content = document.querySelector('.content-wrapper');
        var toggleBtn = document.getElementById('sidebarToggle');
        if (sidebar && toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('minimized');
                if (sidebar.classList.contains('minimized')) {
                    if(content) content.style.marginLeft = '60px';
                } else {
                    if(content) content.style.marginLeft = '220px';
                }
            });
        }
    });
    </script>
</script>

