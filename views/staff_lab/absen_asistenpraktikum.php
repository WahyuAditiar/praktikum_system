<?php
// views/staff_lab/absen_asistenpraktikum.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AbsensiAsistenController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/PraktikumModel.php';

checkAuth();
checkRole(['asisten_praktikum', 'staff_lab', 'admin']);

$database = new Database();
$pdo = $database->getConnection();

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

$base_url = "http://" . $_SERVER['HTTP_HOST'] . str_replace('/views/staff_lab', '', dirname($_SERVER['PHP_SELF']));
$base_url = rtrim($base_url, '/\\');
$uploads_base_url = 'http://localhost/praktikum_system/uploads';

$controller = new AbsensiAsistenController($pdo);
$praktikumModel = new PraktikumModel($pdo);

$asistenList = $controller->getAsistenList();

$currentUser = [
    'username' => $_SESSION['username'] ?? '',
    'nim'      => $_SESSION['nim'] ?? '',
    'nama'     => $_SESSION['nama'] ?? '',
    'role'     => $_SESSION['role'] ?? '',
    'kelas'    => $_SESSION['kelas'] ?? '',
    'nama_praktikum' => $_SESSION['nama_praktikum'] ?? ''
];

$currentRole = $currentUser['role'];

$praktikumList = [];
if (!empty($currentUser['nama'])) {
    $praktikumList = $praktikumModel->getByAsistenNama($currentUser['nama']);
}

$has_signature = false;
$user_signature_data = '';

if (!empty($_SESSION['user_signature_data'])) {
    $has_signature = true;
    $user_signature_data = $_SESSION['user_signature_data'];
}

error_log("Signature Check - Username: " . $currentUser['username'] . ", NIM: " . $currentUser['nim'] . ", Has Signature: " . ($has_signature ? 'YES' : 'NO'));

$error = $success = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $success = $_GET['message'] ?? 'Berhasil memproses data';
    } elseif ($_GET['status'] === 'error') {
        $error = $_GET['message'] ?? 'Terjadi kesalahan';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['validasi_pulang'])) {
        $res = $controller->validasiPulang($_POST);
        if ($res['success']) {
            echo '<script>window.location.href = "absen_asistenpraktikum.php?status=success&message=' . urlencode($res['message']) . '";</script>';
        } else {
            echo '<script>window.location.href = "absen_asistenpraktikum.php?status=error&message=' . urlencode($res['message']) . '";</script>';
        }
        exit;
    }

    if (isset($_POST['tambah_absen'])) {
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

$onlyOwn = ($currentRole === 'asisten_praktikum');
$rows = $controller->getAll($onlyOwn, $currentUser['username']);

include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';
?>

<!-- ✅ DEBUG SIGNATURE STATUS -->
<div class="alert alert-info d-none">
    <strong>Debug Signature Status:</strong><br>
    Session Signature: <?= !empty($_SESSION['user_signature_data']) ? 'ADA (' . strlen($_SESSION['user_signature_data']) . ' chars)' : 'TIDAK ADA' ?><br>
    Username: <?= $currentUser['username'] ?><br>
    Has Signature: <?= $has_signature ? 'YES' : 'NO' ?>
</div>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-primary">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Absensi Asisten Praktikum
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item active">Absensi Asisten</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- Signature Status Alert -->
            <?php if (!$has_signature && $currentRole === 'asisten_praktikum'): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                        <div>
                            <h5 class="mb-1">Signature Belum Diatur!</h5>
                            <p class="mb-0">Anda belum mengatur tanda tangan digital. 
                                <a href="../asisten_praktikumMenu/profile_settings.php" class="alert-link font-weight-bold">Klik di sini</a> 
                                untuk mengatur tanda tangan terlebih dahulu.
                            </p>
                        </div>
                    </div>
                </div>
            <?php elseif ($has_signature): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x mr-3"></i>
                        <div>
                            <h5 class="mb-1">Signature Siap Digunakan!</h5>
                            <p class="mb-0">Tanda tangan digital Anda sudah tersimpan dan akan otomatis digunakan saat absen.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h3><?= count($rows) ?></h3>
                            <p>Total Absensi</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h3><?= count(array_filter($rows, fn($r) => $r['status_hadir'] === 'hadir')) ?></h3>
                            <p>Hadir</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h3><?= count(array_filter($rows, fn($r) => $r['status_hadir'] === 'izin' || $r['status_hadir'] === 'sakit')) ?></h3>
                            <p>Izin/Sakit</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-gradient-danger">
                        <div class="inner">
                            <h3><?= count(array_filter($rows, fn($r) => $r['status_hadir'] === 'alpha')) ?></h3>
                            <p>Alpha</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Absensi Masuk -->
            <form method="POST" enctype="multipart/form-data" id="formAbsensi">
                <input type="hidden" name="tambah_absen" value="1">
                <input type="hidden" name="signature_data" id="signatureData" value="<?= htmlspecialchars($user_signature_data) ?>">

                <!-- Data Asisten Card -->
                <div class="card card-primary mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle mr-2"></i>
                            Data Asisten
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if ($currentRole === 'staff_lab' || $currentRole === 'admin'): ?>
                                <!-- Form untuk staff/admin -->
                                <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">NIM Asisten</label>
                                    <select class="form-control select2" id="nim" name="nim" required style="width: 100%;">
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
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Nama Asisten</label>
                                    <input type="text" id="nama" name="nama" class="form-control bg-light" readonly>
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Praktikum</label>
                                    <select id="praktikum_name" name="praktikum_name" class="form-control select2" required style="width: 100%;">
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
                                <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Kelas</label>
                                    <input type="text" id="kelas" name="kelas" class="form-control bg-light" readonly placeholder="Pilih praktikum terlebih dahulu">
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Tahun Ajaran</label>
                                    <select class="form-control select2" id="tahun_ajaran" name="tahun_ajaran" required style="width: 100%;">
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
                                <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">NIM Asisten</label>
                                    <div class="form-control bg-light font-weight-bold"><?= htmlspecialchars($currentUser['nim'] ?? '-') ?></div>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Nama Asisten</label>
                                    <div class="form-control bg-light font-weight-bold"><?= htmlspecialchars($currentUser['nama'] ?? '-') ?></div>
                                    <input type="hidden" name="nama" value="<?= htmlspecialchars($currentUser['nama'] ?? '') ?>">
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Praktikum</label>
                                    <select id="praktikum_name" name="praktikum_name" class="form-control select2" required style="width: 100%;">
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
                                <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Kelas</label>
                                    <input type="text" id="kelas" name="kelas" class="form-control bg-light" readonly placeholder="Pilih praktikum terlebih dahulu">
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                    <label class="font-weight-bold text-primary">Tahun Ajaran</label>
                                    <select class="form-control select2" id="tahun_ajaran" name="tahun_ajaran" required style="width: 100%;">
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

                <!-- Form Absensi Masuk Card -->
                <div class="card card-success mb-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Absensi Masuk
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Pertemuan & Tanggal -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <label class="font-weight-bold text-success">Pertemuan</label>
                                <select name="pertemuan" class="form-control select2" required style="width: 100%;">
                                    <option value="">Pilih Pertemuan</option>
                                    <option value="Briefing">Briefing</option>
                                    <?php for ($i = 1; $i <= 14; $i++): ?>
                                        <option value="<?= $i ?>">Pertemuan <?= $i ?></option>
                                    <?php endfor; ?>
                                    <option value="Presentasi Tugas Akhir">Presentasi Tugas Akhir</option>
                                    <option value="Pengisian Nilai Akhir">Pengisian Nilai Akhir</option>
                                </select>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <label class="font-weight-bold text-success">Tanggal</label>
                                <div class="input-group">
                                    <input type="date" name="tanggal" id="tanggalMasuk" class="form-control" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <label class="font-weight-bold text-success">Jam Masuk</label>
                                <div class="input-group">
                                    <input type="time" name="jam_mulai" id="jamMulai" class="form-control" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Materi Praktikum -->
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                                <label class="font-weight-bold text-success">Materi Praktikum</label>
                                <input type="text" name="materi" class="form-control" placeholder="Ketik materi praktikum..." id="materiInput">
                                <small class="form-text text-muted">Contoh: Pengenalan Alat, Titrasi Asam Basa, dll.</small>
                            </div>
                        </div>

                        <!-- Status Kehadiran dengan Auto Signature -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="font-weight-bold text-success">Status Kehadiran</label>
                                <div class="input-group">
                                    <select name="status_hadir" class="form-control" id="statusSelect" required>
                                        <option value="">Pilih Status</option>
                                        <option value="hadir" <?= $has_signature ? 'selected' : '' ?>>Hadir</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="izin">Izin</option>
                                        <option value="alpha">Alpha</option>
                                    </select>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fas <?= $has_signature ? 'fa-check text-success' : 'fa-exclamation-triangle text-warning' ?>"></i>
                                        </span>
                                    </div>
                                </div>
                                <small class="form-text" id="signatureInfo">
                                    <?php if ($has_signature): ?>
                                        <i class="fas fa-check-circle text-success mr-1"></i> 
                                        <strong>Status "Hadir" dipilih otomatis</strong> - Signature dari profile akan digunakan
                                    <?php else: ?>
                                        <i class="fas fa-exclamation-triangle text-warning mr-1"></i> 
                                        Anda belum memiliki signature. 
                                        <a href="../asisten_praktikumMenu/profile_settings.php" class="text-warning font-weight-bold">Atur signature di profile</a>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>

                        <!-- Foto Bukti -->
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                                <label class="font-weight-bold text-success">Foto Bukti <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input" id="fotoInput" accept="image/*" capture="environment" required>
                                    <label class="custom-file-label" for="fotoInput">Pilih file foto...</label>
                                </div>
                                <small class="form-text text-muted">Ambil foto selfie atau dokumentasi kegiatan praktikum</small>
                            </div>

                            <!-- Laporan Opsional -->
                            <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                                <label class="font-weight-bold text-success">Laporan (Opsional)</label>
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
                                <label class="font-weight-bold text-success">Lokasi GPS <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="gps_lat" class="form-control gps-coord" placeholder="Latitude" readonly>
                                    <input type="text" name="gps_lng" class="form-control gps-coord" placeholder="Longitude" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success" id="btnGetLocation">
                                            <i class="fas fa-map-marker-alt mr-1"></i> Ambil Lokasi
                                        </button>
                                        <button type="button" class="btn btn-info" id="btnMapModal" data-toggle="modal" data-target="#mapModal">
                                            <i class="fas fa-map mr-1"></i> Lihat Peta
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Klik "Ambil Lokasi" untuk mendapatkan koordinat GPS Anda secara otomatis</small>
                            </div>
                        </div>

                        <!-- INFO AUTO SIGNATURE -->
                        <?php if ($has_signature): ?>
                            <div class="alert alert-success mt-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-robot fa-2x mr-3"></i>
                                    <div>
                                        <h5 class="mb-1">Sistem Auto Signature Aktif!</h5>
                                        <p class="mb-0">Tanda tangan Anda akan otomatis diambil dari profile saat memilih status "Hadir".</p>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                                    <div>
                                        <h5 class="mb-1">Signature Belum Diatur!</h5>
                                        <p class="mb-0">Anda belum mengatur tanda tangan digital. 
                                            <a href="../asisten_praktikumMenu/profile_settings.php" class="alert-link font-weight-bold">Klik di sini</a> 
                                            untuk mengatur tanda tangan terlebih dahulu.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-success btn-lg btn-block mt-3 shadow-sm" id="submitButton">
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
                        <h3 class="card-title">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Absensi Pulang
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-8 mb-3">
                                <label class="font-weight-bold text-danger">Jam Akhir <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="time" name="jam_akhir" id="jamAkhir" class="form-control" placeholder="Klik tombol untuk mengisi jam" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-primary" onclick="isiJamAkhir()" title="Klik untuk mengisi jam sekarang">
                                            <i class="fas fa-clock"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Klik tombol jam untuk mengisi waktu sekarang</small>
                            </div>
                            <div class="col-xl-8 col-lg-6 col-md-12 mb-3">
                                <label class="font-weight-bold text-danger">Info</label>
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-info mr-2"></i>
                                    Pastikan Praktikum dan Pertemuan sudah dipilih di form atas sebelum validasi pulang
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-warning" onclick="isiJamAkhir()">
                                <i class="fas fa-clock mr-1"></i>Isi Jam Sekarang
                            </button>
                            <button type="submit" class="btn btn-danger" id="btnValidasiPulang" disabled>
                                <i class="fas fa-check-circle mr-1"></i>Validasi Pulang
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Button Export -->
            <div class="mb-3 text-right">
                <?php if ($currentRole === 'admin'): ?>
                    <a href="laporan_admin.php" class="btn btn-success shadow-sm">
                        <i class="fas fa-file-excel mr-2"></i> Export Rekap Kehadiran (Admin)
                    </a>
                    <small class="text-muted d-block mt-1">Buka halaman filter untuk download laporan</small>
                <?php else: ?>
                    <a href="export_rekap.php" class="btn btn-success shadow-sm">
                        <i class="fas fa-file-excel mr-2"></i> Export Rekap Kehadiran
                    </a>
                    <small class="text-muted d-block mt-1">Format sesuai template sistem</small>
                <?php endif; ?>
            </div>

            <!-- Tabel Daftar Absensi -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        Daftar Absensi
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="absenTable" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead class="table-primary">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIM</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Praktikum</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">Pertemuan</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Jam Mulai</th>
                                    <th class="text-center">Jam Akhir</th>
                                    <th class="text-center">Materi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Bukti</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                    <tr>
                                        <td colspan="13" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                                            Belum ada data absen
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($rows as $i => $r): ?>
                                        <tr>
                                            <td class="text-center align-middle"><?= $i + 1 ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($r['nim'] ?? '-') ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($r['nama'] ?? '-') ?></td>
                                            <td class="align-middle"><?= htmlspecialchars($r['praktikum_name'] ?? '-') ?></td>
                                            <td class="text-center align-middle"><?= htmlspecialchars($r['kelas'] ?? '-') ?></td>
                                            <td class="text-center align-middle"><?= htmlspecialchars($r['pertemuan'] ?? '-') ?></td>
                                            <td class="text-center align-middle"><?= htmlspecialchars($r['tanggal'] ?? '-') ?></td>
                                            <td class="text-center align-middle"><?= htmlspecialchars($r['jam_mulai'] ?? '-') ?></td>
                                            <td class="text-center align-middle"><?= htmlspecialchars($r['jam_akhir'] ?? '-') ?></td>
                                            <td class="align-middle">
                                                <?php if (!empty($r['materi'])): ?>
                                                    <span class="materi-text" title="<?= htmlspecialchars($r['materi']) ?>">
                                                        <?= htmlspecialchars($r['materi']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-<?=
                                                    ($r['status_hadir'] == 'hadir') ? 'success' : 
                                                    (($r['status_hadir'] == 'sakit') ? 'warning' : 
                                                    (($r['status_hadir'] == 'izin') ? 'info' : 'danger'))
                                                ?> badge-pill">
                                                    <?= htmlspecialchars(ucfirst($r['status_hadir'] ?? '-')) ?>
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
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
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                    data-id="<?= $r['id'] ?>"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

<!-- Modal untuk Signature -->
<div class="modal fade" id="signatureModal" tabindex="-1" role="dialog" aria-labelledby="signatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="signatureModalLabel">
                    <i class="fas fa-signature mr-2"></i>
                    Tanda Tangan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="signatureImage" src="" alt="Tanda Tangan" class="img-fluid rounded shadow" style="max-height: 400px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Foto -->
<div class="modal fade" id="fotoModal" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="fotoModalLabel">
                    <i class="fas fa-camera mr-2"></i>
                    Foto Bukti
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="fotoImage" src="" alt="Foto Bukti" class="img-fluid rounded shadow" style="max-height: 500px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk GPS -->
<div class="modal fade" id="gpsModal" tabindex="-1" role="dialog" aria-labelledby="gpsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="gpsModalLabel">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Lokasi GPS
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Informasi Koordinat
                                </h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Latitude:</strong> <span id="coordLat" class="text-primary">-</span></p>
                                <p><strong>Longitude:</strong> <span id="coordLng" class="text-primary">-</span></p>
                                <p><strong>Alamat:</strong> <span id="coordAddress" class="text-muted">-</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-map mr-2"></i>
                                    Peta Lokasi
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="map" style="height: 300px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Peta Input Lokasi -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="mapModalLabel">
                    <i class="fas fa-map mr-2"></i>
                    Pilih Lokasi di Peta
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i> 
                    Klik pada peta untuk memilih lokasi, atau gunakan tombol "Dapatkan Lokasi Saat Ini"
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Latitude:</strong> <span id="modalLat" class="text-primary">-</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Longitude:</strong> <span id="modalLng" class="text-primary">-</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Alamat:</strong> <span id="modalAddress" class="text-muted">-</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Status:</strong> <span id="locationStatus" class="badge badge-secondary">Belum ada lokasi</span>
                    </div>
                </div>
                <div id="liveMap" style="height: 400px; width: 100%;" class="rounded border"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="getLocationFromMap()">
                    <i class="fas fa-location-arrow mr-1"></i> Dapatkan Lokasi Saat Ini
                </button>
                <button type="button" class="btn btn-success" onclick="useCurrentLocation()">
                    <i class="fas fa-check mr-1"></i> Gunakan Lokasi Ini
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h5>Apakah Anda yakin ingin menghapus data absensi ini?</h5>
                    <p class="text-muted">Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
            </div>
            <div class="modal-footer">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="hapus_absen" value="1">
                    <input type="hidden" name="id" id="deleteId" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<!-- Include Leaflet CSS & JS untuk peta -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Include Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Improved Responsive Design */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 10px;
        }
        
        .card-body {
            padding: 15px;
        }
        
        .btn-group-sm > .btn {
            padding: 0.25rem 0.4rem;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .small-box .icon {
            font-size: 60px;
        }
        
        .input-group-append .btn {
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .card-header .card-title {
            font-size: 1.1rem;
        }
        
        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
        
        .alert {
            padding: 0.75rem 1rem;
        }
        
        .modal-dialog {
            margin: 10px;
        }
    }

    /* Enhanced UI Styles */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .small-box {
        border-radius: 0.5rem;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }

    .small-box:hover {
        transform: translateY(-2px);
    }

    .btn {
        border-radius: 0.375rem;
        transition: all 0.2s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .table th {
        border-top: none;
        font-weight: 600;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        height: calc(2.25rem + 2px);
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }

    /* Style untuk input yang terisi otomatis */
    .form-control[readonly] {
        background-color: #f8f9fa !important;
        border-color: #ced4da;
        color: #495057 !important;
        font-weight: 500;
    }

    /* Style untuk GPS coordinates yang berhasil */
    .gps-coord.gps-success {
        color: #28a745 !important;
        font-weight: bold !important;
        background-color: #d4edda !important;
        border-color: #28a745 !important;
    }

    /* Style untuk jam yang terisi */
    .time-filled {
        background-color: #d4edda !important;
        border-color: #28a745 !important;
        color: #155724 !important;
        font-weight: bold !important;
    }

    /* Loading state untuk GPS */
    .gps-loading {
        color: #ffc107 !important;
        font-style: italic;
        background-color: #fff3cd !important;
    }

    /* Auto-hide notification */
    .auto-hide-notification {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        animation: slideInRight 0.3s ease-out;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .notification-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .notification-warning {
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }

    .notification-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    .notification-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    /* Style untuk materi di tabel */
    .materi-text {
        max-width: 200px;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: help;
    }

    /* Style untuk input materi */
    #materiInput {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    #materiInput:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        background-color: #fff;
    }

    /* Custom badge styles */
    .badge-pill {
        border-radius: 50rem;
        padding: 0.375em 0.75em;
    }

    /* Improved table hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.025);
    }

    /* Custom file input styling */
    .custom-file-label::after {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border-left: 1px solid #0056b3;
    }

    /* Gradient backgrounds for cards */
    .card-primary .card-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        color: white;
    }

    .card-success .card-header {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
        color: white;
    }

    .card-danger .card-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        color: white;
    }

    /* Comprehensive Select2 Styling */
.select2-container--bootstrap4 .select2-selection--single {
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    height: calc(2.25rem + 2px) !important;
    background-color: #fff !important;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    color: #495057 !important;
    line-height: calc(2.25rem + 2px) !important;
    padding-left: 0.75rem !important;
    padding-right: 2.25rem !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px) !important;
    right: 0.75rem !important;
}

/* Focus state */
.select2-container--bootstrap4.select2-container--focus .select2-selection--single {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

/* Dropdown styling */
.select2-container--bootstrap4 .select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.select2-container--bootstrap4 .select2-results__option--highlighted {
    background-color: #007bff !important;
    color: white !important;
}

/* Disabled state */
.select2-container--bootstrap4 .select2-selection--single[aria-disabled="true"] {
    background-color: #e9ecef !important;
    border-color: #ced4da !important;
}

</style>

<script>
    // Initialize Select2
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });

    // ========== GLOBAL VARIABLES ==========
    let liveMap, liveMarker;
    window.map = null;
    window.marker = null;

    // ========== AUTO-FILL FORM FUNCTIONS ==========
    function autoFillForm() {
        // Auto-fill tanggal dan jam masuk
        updateWaktuRealtime();
        
        // Auto-fill kelas berdasarkan praktikum yang dipilih
        const praktikumSelect = document.getElementById('praktikum_name');
        if (praktikumSelect && praktikumSelect.value) {
            updateKelas();
        }
        
        // Auto-get GPS location
        setTimeout(() => {
            getCurrentLocation();
        }, 1500);
    }

    function updateKelas() {
        const selectPraktikum = document.getElementById("praktikum_name");
        const inputKelas = document.getElementById("kelas");
        const inputPraktikumId = document.getElementById("praktikum_id");
        
        if (selectPraktikum && inputKelas) {
            const selected = selectPraktikum.options[selectPraktikum.selectedIndex];
            const kelas = selected.getAttribute("data-kelas") || "";
            const praktikumId = selected.getAttribute("data-praktikum-id") || "";

            inputKelas.value = kelas;
            if (inputPraktikumId) inputPraktikumId.value = praktikumId;
            
            // Update form absensi pulang juga
            document.getElementById('kelas_pulang').value = kelas;
            document.getElementById('praktikum_name_pulang').value = selectPraktikum.value;
            
            console.log('Kelas updated to:', kelas);
        }
    }

    function getCurrentLocation() {
        if (!navigator.geolocation) {
            console.log('Browser tidak mendukung GPS');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                document.querySelector('input[name="gps_lat"]').value = lat.toFixed(6);
                document.querySelector('input[name="gps_lng"]').value = lng.toFixed(6);
                
                // Add success styling
                const gpsInputs = document.querySelectorAll('.gps-coord');
                gpsInputs.forEach(input => {
                    input.classList.add('gps-success');
                });
                
                console.log('GPS location auto-filled:', lat, lng);
            },
            function(error) {
                console.log('GPS auto-location failed:', error.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        );
    }

    // ========== IMPROVED TIME FUNCTIONS ==========
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

        // Update form absensi masuk
        const tanggalMasuk = document.getElementById('tanggalMasuk');
        const jamMulai = document.getElementById('jamMulai');
        
        if (tanggalMasuk) tanggalMasuk.value = formattedDate;
        if (jamMulai) jamMulai.value = formattedTime;

        // Update form absensi pulang (hanya tanggal)
        const tanggalPulang = document.getElementById('tanggalPulang');
        if (tanggalPulang) tanggalPulang.value = formattedDate;
        
        console.log('Time updated - Date:', formattedDate, 'Time:', formattedTime);
    }

    // ========== IMPROVED GPS FUNCTIONS ==========
    function initGPSFunctions() {
        // Tombol Ambil Lokasi
        document.getElementById('btnGetLocation')?.addEventListener('click', function() {
            getCurrentLocationWithFeedback();
        });

        // Tombol Lihat Peta
        document.getElementById('btnMapModal')?.addEventListener('click', function() {
            initMapModal();
        });
    }

    function getCurrentLocationWithFeedback() {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung GPS. Pastikan Anda mengizinkan akses lokasi.');
            return;
        }

        const latInput = document.querySelector('input[name="gps_lat"]');
        const lngInput = document.querySelector('input[name="gps_lng"]');
        
        // Tampilkan loading state
        latInput.value = 'Mendapatkan lokasi...';
        lngInput.value = 'Mendapatkan lokasi...';
        latInput.classList.add('gps-loading');
        lngInput.classList.add('gps-loading');
        latInput.classList.remove('gps-success');
        lngInput.classList.remove('gps-success');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
                latInput.classList.remove('gps-loading');
                lngInput.classList.remove('gps-loading');
                latInput.classList.add('gps-success');
                lngInput.classList.add('gps-success');
                
                showAutoHideNotification('Lokasi berhasil didapatkan!', 'success');
                console.log('Location acquired:', lat, lng);
            },
            function(error) {
                let errorMessage = 'Gagal mendapatkan lokasi: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Akses lokasi ditolak. Izinkan akses lokasi di browser settings.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Informasi lokasi tidak tersedia.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Request lokasi timeout.';
                        break;
                    default:
                        errorMessage += 'Error tidak diketahui.';
                        break;
                }
                
                latInput.value = '';
                lngInput.value = '';
                latInput.classList.remove('gps-loading', 'gps-success');
                lngInput.classList.remove('gps-loading', 'gps-success');
                
                alert(errorMessage);
                console.error('GPS Error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 60000
            }
        );
    }

    function initMapModal() {
        // Inisialisasi peta di modal
        $('#mapModal').on('shown.bs.modal', function() {
            setTimeout(() => {
                if (!liveMap) {
                    initLiveMap();
                }
                liveMap.invalidateSize();
                
                // Coba dapatkan lokasi saat ini untuk center map
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            liveMap.setView([lat, lng], 16);
                            
                            // Tambahkan marker untuk lokasi saat ini
                            if (liveMarker) {
                                liveMap.removeLayer(liveMarker);
                            }
                            liveMarker = L.marker([lat, lng]).addTo(liveMap)
                                .bindPopup('Lokasi Anda Saat Ini')
                                .openPopup();
                                
                            // Update modal coordinates
                            document.getElementById('modalLat').textContent = lat.toFixed(6);
                            document.getElementById('modalLng').textContent = lng.toFixed(6);
                            getAddressFromCoordinates(lat, lng);
                        },
                        function(error) {
                            console.log('Tidak bisa dapatkan lokasi untuk map center');
                        }
                    );
                }
            }, 100);
        });
    }

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
        
        // Add success styling
        const gpsInputs = document.querySelectorAll('.gps-coord');
        gpsInputs.forEach(input => {
            input.classList.add('gps-success');
        });
        
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

    // ========== JAM AKHIR FUNCTION ==========
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
        jamAkhirInput.classList.add('time-filled');

        // Auto-sync data untuk absensi pulang
        syncAbsensiData();

        // Tampilkan notifikasi otomatis
        showAutoHideNotification(`Jam akhir berhasil diisi: <strong>${formattedTime}</strong>`, 'success', 2500);
        
        console.log('Jam akhir diisi:', formattedTime);
    }

    // ========== SYNC DATA FUNCTION ==========
    function syncAbsensiData() {
        const praktikum = document.querySelector('[name="praktikum_name"]')?.value || '';
        const pertemuan = document.querySelector('[name="pertemuan"]')?.value || '';
        const kelas = document.querySelector('[name="kelas"]')?.value || '';
        const tahunAjaran = document.querySelector('[name="tahun_ajaran"]')?.value || '';

        document.getElementById('praktikum_name_pulang').value = praktikum;
        document.getElementById('pertemuan_pulang').value = pertemuan;
        document.getElementById('kelas_pulang').value = kelas;
        
        console.log('Data synced for absensi pulang:', { praktikum, pertemuan, kelas });
    }

    // ========== NOTIFICATION FUNCTION ==========
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

    function getNotificationIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle',
            'danger': 'fa-times-circle'
        };
        return icons[type] || 'fa-info-circle';
    }

    // ========== TABLE BUKTI HANDLERS ==========
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

        // Signature Modal untuk tabel
        signatureButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const signaturePath = this.getAttribute('data-signature');
                console.log('Signature clicked:', signaturePath);
                
                const signatureImage = document.getElementById('signatureImage');
                
                if (signaturePath.startsWith('data:image')) {
                    signatureImage.src = signaturePath;
                } else {
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
            getAddressFromCoordinatesForTable(lat, lng);
        });
    }

    function initMap(lat, lng) {
        if (window.map) {
            window.map.remove();
        }

        window.map = L.map('map').setView([lat, lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(window.map);

        if (window.marker) {
            window.map.removeLayer(window.marker);
        }
        window.marker = L.marker([lat, lng]).addTo(window.map)
            .bindPopup('Lokasi Absensi<br>Lat: ' + lat + '<br>Lng: ' + lng)
            .openPopup();
    }

    function getAddressFromCoordinatesForTable(lat, lng) {
        document.getElementById('coordAddress').textContent = 'Mengambil alamat...';

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('coordAddress').textContent = data?.display_name || 'Alamat tidak ditemukan';
            })
            .catch(error => {
                document.getElementById('coordAddress').textContent = 'Gagal mengambil alamat';
            });
    }

    // Cleanup ketika modal GPS ditutup
    $('#gpsModal').on('hidden.bs.modal', function() {
        if (window.map) {
            window.map.remove();
            window.map = null;
            window.marker = null;
        }
    });

    // ========== DOM CONTENT LOADED ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing all handlers...');

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Auto-fill form saat halaman load
    autoFillForm();

    // Initialize GPS functions
    initGPSFunctions();

    // Event listeners untuk praktikum - FIX FOR SELECT2
    const selectPraktikum = $("#praktikum_name");
    if (selectPraktikum.length) {
        selectPraktikum.on("change", function() {
            console.log('Praktikum changed:', this.value);
            updateKelas();
            syncAbsensiData();
        });
    }

    // Staff/Admin: ketika pilih NIM - FIX FOR SELECT2
    const nimSelect = $('#nim');
    if (nimSelect.length) {
        nimSelect.on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const nama = selectedOption.data('nama') || '';
            const praktikum = selectedOption.data('praktikum') || '';
            const tahunAjaran = selectedOption.data('tahun') || '';
            
            $('#nama').val(nama);
            $('#praktikum_name').val(praktikum).trigger('change');
            
            if (tahunAjaran) {
                $('#tahun_ajaran').val(tahunAjaran).trigger('change');
            }

            syncAbsensiData();
        });
    }

    // Event listeners untuk sync data - FIX FOR SELECT2
    const pertemuanSelect = $('select[name="pertemuan"]');
    if (pertemuanSelect.length) {
        pertemuanSelect.on('change', syncAbsensiData);
    }

    const tahunSelect = $('#tahun_ajaran');
    if (tahunSelect.length) {
        tahunSelect.on('change', syncAbsensiData);
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

    // Update waktu setiap detik
    setInterval(updateWaktuRealtime, 1000);

    // Inisialisasi table handlers
    initTableBuktiHandlers();

    console.log('All handlers initialized successfully');
});

// PERBAIKAN FUNGSI updateKelas
function updateKelas() {
    const selectPraktikum = $("#praktikum_name");
    const inputKelas = document.getElementById("kelas");
    const inputPraktikumId = document.getElementById("praktikum_id");
    
    if (selectPraktikum.length && inputKelas) {
        const selectedOption = selectPraktikum.find('option:selected');
        const kelas = selectedOption.data('kelas') || "";
        const praktikumId = selectedOption.data('praktikum-id') || "";

        console.log('Updating kelas:', {
            selectedValue: selectPraktikum.val(),
            kelas: kelas,
            praktikumId: praktikumId
        });

        inputKelas.value = kelas;
        if (inputPraktikumId) inputPraktikumId.value = praktikumId;
        
        // Update form absensi pulang juga
        document.getElementById('kelas_pulang').value = kelas;
        document.getElementById('praktikum_name_pulang').value = selectPraktikum.val();
        
        console.log('Kelas updated to:', kelas);
    } else {
        console.log('Element not found:', {
            selectPraktikum: selectPraktikum.length,
            inputKelas: !!inputKelas
        });
    }
}

// TAMBAHKAN FUNGSI INI UNTUK DEBUG
function debugSelect2() {
    console.log('Select2 Debug Info:');
    console.log('Praktikum value:', $('#praktikum_name').val());
    console.log('Praktikum selected data:', $('#praktikum_name').find('option:selected').data());
    console.log('Kelas value:', $('#kelas').val());
}

// PANGGIL FUNGSI DEBUG JIKA PERLU
// debugSelect2();

</script>