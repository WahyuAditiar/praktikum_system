<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AbsensiController.php';
require_once __DIR__ . '/../../models/GroupModel.php';

// buat koneksi DB
$database = new Database();
$db = $database->getConnection();

if (!in_array($_SESSION['role'] ?? '', ['asisten_praktikum'])) {
    header("Location: ../../index.php");
    exit;
}

$controller = new AbsensiController($db, $_SESSION['role']);
$groupModel = new GroupModel($db);

// ambil daftar jadwal utk dropdown
$jadwal_praktikum = $controller->getJadwalPraktikum();

$mahasiswa = [];
$absensi_existing = [];
$selected_jadwal = '';
$selected_pertemuan = '';
$kode_random_input = '';
$validKode = false;
$error = '';
$showRekap = false;
$kode_warning = '';
$absensi_terisi = false;
$asprak_group = null;
$detail_jadwal = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CEK JIKA INI SIMPAN ABSENSI
    if (isset($_POST['action']) && $_POST['action'] == 'simpan') {
        $controller->simpan();
        exit;
    }

    // CEK JIKA INI LIHAT REKAP
    if (isset($_POST['action']) && $_POST['action'] == 'lihat_rekap') {
        $showRekap = true;
    }

    // JIKA INI TAMPILKAN DATA
    $selected_jadwal = $_POST['jadwal_praktikum_id'] ?? '';
    $selected_pertemuan = $_POST['pertemuan'] ?? '';
    $kode_random_input = $_POST['kode_random'] ?? '';

    // Validasi kode random
    if ($selected_jadwal && $kode_random_input && $selected_pertemuan) {
        $validKode = $controller->validateKodeRandom($selected_jadwal, $kode_random_input);
        if ($validKode) {
            // DAPATKAN GROUP ASISTEN SEBELUM AMBIL MAHASISWA
            $asprak_group = $groupModel->getAsprakGroup($_SESSION['user_id'], $selected_jadwal);

            // AMBIL MAHASISWA DENGAN FILTER GROUP
            if ($asprak_group) {
                $mahasiswa = $groupModel->getMahasiswaByGroup($selected_jadwal, $asprak_group);
            } else {
                $mahasiswa = $groupModel->getAllMahasiswaInJadwal($selected_jadwal);
            }
            
            // PERBAIKAN: Ambil absensi dengan filter group
            if ($asprak_group) {
                $absensi_existing = $controller->getAbsensi($selected_jadwal, $selected_pertemuan, $asprak_group);
            } else {
                $absensi_existing = $controller->getAbsensi($selected_jadwal, $selected_pertemuan);
            }
            
            $detail_jadwal = $controller->getDetailJadwal($selected_jadwal);

            // CEK APAKAH ABSENSI SUDAH DIISI UNTUK GROUP INI
            $absensi_terisi = !empty($absensi_existing);

            // OTOMATIS MODE REKAP JIKA ABSENSI SUDAH DIISI
            if ($absensi_terisi) {
                $showRekap = true;
            }

            // Cek waktu absen_open_until untuk notifikasi
            if (isset($detail_jadwal['absen_open_until'])) {
                $waktu_berakhir = strtotime($detail_jadwal['absen_open_until']);
                $waktu_sekarang = time();
                $selisih_menit = ($waktu_berakhir - $waktu_sekarang) / 60;

                if ($selisih_menit > 0 && $selisih_menit <= 5) {
                    $kode_warning = "⏰ Kode random akan berakhir dalam " . round($selisih_menit) . " menit!";
                } elseif ($selisih_menit <= 0) {
                    $kode_warning = "❌ Kode random sudah kadaluarsa!";
                    $validKode = false;
                }
            }
        } else {
            $error = "Kode random salah atau jadwal tidak ditemukan.";
        }
    } else {
        $error = "Harap pilih jadwal, pertemuan, dan masukkan kode random.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi Praktikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --info-color: #4895ef;
            --light-bg: #f8f9fa;
            --card-shadow: 0 8px 25px rgba(0,0,0,0.1);
            --hover-shadow: 0 12px 35px rgba(0,0,0,0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin-top: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-2px);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border-bottom: none;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #4cc9f0, #4895ef);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f72585, #b5179e);
            color: white;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .rekap-mode .absensi-input {
            display: none;
        }

        .rekap-mode .absensi-tampil {
            display: block;
        }

        .absensi-tampil {
            display: none;
        }

        .group-badge {
            background: linear-gradient(135deg, #ff9a00, #ff6a00);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--hover-shadow);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .filter-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            border-radius: 0 0 30px 30px;
            margin-bottom: 2rem;
        }

        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header-section {
                border-radius: 0 0 20px 20px;
                padding: 1.5rem 0;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.875rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .floating-action {
                bottom: 1rem;
                right: 1rem;
            }
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-users me-3"></i>Input Absensi Praktikan
                    </h1>
                    <p class="lead mb-0 opacity-75">Kelola kehadiran praktikan dengan mudah dan efisien</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="dashboard.php" class="btn btn-light btn-lg rounded-pill">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="main-container p-4">
            <!-- Alert Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show fade-in">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">Berhasil!</h5>
                            <p class="mb-0"><?= $_SESSION['success'] ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show fade-in">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">Terjadi Kesalahan!</h5>
                            <p class="mb-0"><?= $_SESSION['error'] ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Group Information -->
            <?php if ($validKode && $asprak_group): ?>
                <div class="alert alert-info fade-in">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users fa-2x me-3"></i>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">Anda bertugas di <span class="group-badge">Group <?= $asprak_group ?></span></h5>
                            <p class="mb-0">Menampilkan <?= count($mahasiswa) ?> mahasiswa dari group Anda</p>
                        </div>
                        <?php if ($absensi_terisi): ?>
                            <span class="badge bg-success fs-6 p-2">
                                <i class="fas fa-check me-1"></i>Absensi Tersimpan
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Warning Messages -->
            <?php if ($kode_warning): ?>
                <div class="alert alert-warning alert-dismissible fade show fade-in pulse-animation">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock fa-2x me-3"></i>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">Peringatan Waktu!</h5>
                            <p class="mb-0"><?= $kode_warning ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filter Card -->
            <div class="card fade-in">
                <div class="card-header text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Data Absensi</h4>
                        <?php if ($validKode && !empty($mahasiswa)): ?>
                            <div class="btn-group">
                                <?php if ($showRekap && !$absensi_terisi): ?>
                                    <button type="button" class="btn btn-warning" onclick="toggleRekap()">
                                        <i class="fas fa-edit me-1"></i>Edit Mode
                                    </button>
                                <?php elseif (!$showRekap): ?>
                                    <button type="button" class="btn btn-info" onclick="toggleRekap()">
                                        <i class="fas fa-chart-bar me-1"></i>Lihat Rekap
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="filterForm">
                        <div class="row g-3">
                            <!-- Pilih Praktikum -->
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label fw-semibold">Pilih Praktikum</label>
                                <select class="form-select" name="jadwal_praktikum_id" required>
                                    <option value="">-- Pilih Praktikum --</option>
                                    <?php foreach ($jadwal_praktikum as $jadwal): ?>
                                        <option value="<?= $jadwal['id'] ?>" <?= $selected_jadwal == $jadwal['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($jadwal['nama_mk'] ?? $jadwal['nama_praktikum'] ?? 'Praktikum') ?> - Kelas <?= $jadwal['kelas'] ?? '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Kode Random -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">Kode Random</label>
                                <input type="text" name="kode_random" class="form-control" required
                                    placeholder="Masukkan kode" value="<?= htmlspecialchars($kode_random_input) ?>">
                            </div>

                            <!-- Pertemuan -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">Pertemuan</label>
                                <select class="form-select" name="pertemuan" required>
                                    <option value="">-- Pilih --</option>
                                    <?php for ($i = 1; $i <= 16; $i++): ?>
                                        <option value="<?= $i ?>" <?= $selected_pertemuan == $i ? 'selected' : '' ?>>Pertemuan <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <!-- Tanggal -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">Tanggal</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" value="<?= date('d/m/Y') ?>" readonly>
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Tampilkan -->
                            <div class="col-lg-2 col-md-12 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="fas fa-search me-2"></i>Tampilkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat data absensi...</p>
            </div>

            <!-- Data Absensi -->
            <?php if ($validKode && !empty($mahasiswa)): ?>
                <div class="fade-in">
                    <!-- Statistics Cards -->
                    <?php if ($showRekap): ?>
                        <?php
                        $total_mahasiswa = count($mahasiswa);
                        $hadir = 0; $sakit = 0; $izin = 0; $alfa = 0;

                        foreach ($mahasiswa as $mhs) {
                            $absen = array_filter($absensi_existing, fn($a) => $a['mahasiswa_id'] == $mhs['id']);
                            $data = $absen ? reset($absen) : null;
                            $status = $data['status'] ?? 'belum_diisi';

                            switch ($status) {
                                case 'hadir': $hadir++; break;
                                case 'sakit': $sakit++; break;
                                case 'izin': $izin++; break;
                                case 'alfa': $alfa++; break;
                            }
                        }
                        ?>
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card">
                                    <div class="stat-number text-success"><?= $hadir ?></div>
                                    <div class="stat-label">Hadir</div>
                                    <small class="text-muted"><?= $total_mahasiswa > 0 ? round(($hadir / $total_mahasiswa) * 100, 1) : 0 ?>%</small>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card">
                                    <div class="stat-number text-warning"><?= $sakit ?></div>
                                    <div class="stat-label">Sakit</div>
                                    <small class="text-muted"><?= $total_mahasiswa > 0 ? round(($sakit / $total_mahasiswa) * 100, 1) : 0 ?>%</small>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card">
                                    <div class="stat-number text-info"><?= $izin ?></div>
                                    <div class="stat-label">Izin</div>
                                    <small class="text-muted"><?= $total_mahasiswa > 0 ? round(($izin / $total_mahasiswa) * 100, 1) : 0 ?>%</small>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card">
                                    <div class="stat-number text-danger"><?= $alfa ?></div>
                                    <div class="stat-label">Alfa</div>
                                    <small class="text-muted"><?= $total_mahasiswa > 0 ? round(($alfa / $total_mahasiswa) * 100, 1) : 0 ?>%</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Absensi Table Card -->
                    <div class="card <?= $showRekap ? 'rekap-mode' : '' ?>" id="absensiCard">
                        <div class="card-header text-white d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">
                                    <i class="fas fa-list-check me-2"></i>
                                    <?= $showRekap ? 'Rekap Absensi' : 'Form Absensi' ?> - Pertemuan <?= $selected_pertemuan ?>
                                </h4>
                                <small class="opacity-75">
                                    <?= $detail_jadwal['nama_praktikum'] ?? '' ?> - Kelas <?= $detail_jadwal['kelas'] ?? '' ?>
                                    <?php if ($asprak_group): ?>
                                        • Group <?= $asprak_group ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-dark fs-6">
                                    <i class="fas fa-users me-1"></i><?= count($mahasiswa) ?> Mahasiswa
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="absensiForm">
                                <?php if (!$showRekap && !$absensi_terisi): ?>
                                    <input type="hidden" name="action" value="simpan">
                                <?php else: ?>
                                    <input type="hidden" name="action" value="lihat_rekap">
                                <?php endif; ?>
                                <input type="hidden" name="jadwal_praktikum_id" value="<?= $selected_jadwal ?>">
                                <input type="hidden" name="pertemuan" value="<?= $selected_pertemuan ?>">
                                <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">
                                <input type="hidden" name="kode_random" value="<?= $kode_random_input ?>">

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th width="15%">NIM</th>
                                                <th width="30%">Nama Mahasiswa</th>
                                                <th width="20%">Status Kehadiran</th>
                                                <th width="20%">Keterangan</th>
                                                <?php if ($showRekap): ?>
                                                    <th width="10%" class="text-center">Status</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($mahasiswa as $i => $mhs):
                                                $absen = array_filter($absensi_existing, fn($a) => $a['mahasiswa_id'] == $mhs['id']);
                                                $data = $absen ? reset($absen) : null;
                                                $hasAbsensi = !empty($data);
                                            ?>
                                                <tr class="fade-in">
                                                    <td class="text-center fw-bold"><?= $i + 1 ?></td>
                                                    <td><span class="fw-semibold text-primary"><?= htmlspecialchars($mhs['nim']) ?></span></td>
                                                    <td><?= htmlspecialchars($mhs['nama']) ?></td>
                                                    <td>
                                                        <div class="absensi-input">
                                                            <select name="absensi[<?= $mhs['id'] ?>][status]"
                                                                class="form-select form-select-sm"
                                                                <?= (($showRekap || $absensi_terisi)) ? 'disabled' : '' ?>>
                                                                <option value="hadir" <?= ($data['status'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                                                <option value="sakit" <?= ($data['status'] ?? '') == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                                                <option value="izin" <?= ($data['status'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                                                                <option value="alfa" <?= ($data['status'] ?? '') == 'alfa' ? 'selected' : '' ?>>Alfa</option>
                                                            </select>
                                                        </div>
                                                        <div class="absensi-tampil">
                                                            <?php if ($hasAbsensi): ?>
                                                                <?php
                                                                $badge_class = [
                                                                    'hadir' => 'bg-success',
                                                                    'sakit' => 'bg-warning',
                                                                    'izin' => 'bg-info',
                                                                    'alfa' => 'bg-danger'
                                                                ];
                                                                $status = $data['status'] ?? 'belum_diisi';
                                                                $class = $badge_class[$status] ?? 'bg-secondary';
                                                                ?>
                                                                <span class="status-badge <?= $class ?>"><?= ucfirst($status) ?></span>
                                                            <?php else: ?>
                                                                <span class="status-badge bg-secondary">Belum Absen</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="absensi-input">
                                                           <input type="text"
                                                                name="absensi[<?= $mhs['id'] ?>][keterangan]"
                                                                class="form-control form-control-sm"
                                                                value="<?= htmlspecialchars($data['keterangan'] ?? '') ?>"
                                                                placeholder="Opsional"
                                                                <?= (($showRekap || $absensi_terisi)) ? 'disabled' : '' ?>>
                                                        </div>
                                                        <div class="absensi-tampil">
                                                            <small class="text-muted"><?= htmlspecialchars($data['keterangan'] ?? '-') ?></small>
                                                        </div>
                                                    </td>
                                                    <?php if ($showRekap): ?>
                                                        <td class="text-center">
                                                            <?php if ($hasAbsensi): ?>
                                                                <i class="fas fa-check-circle text-success fa-lg"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-clock text-muted"></i>
                                                            <?php endif; ?>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                                    <a href="?page=absensi" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Batal
                                    </a>

                                    <button type="button" class="btn btn-warning" onclick="resetForm()">
                                        <i class="fas fa-redo me-1"></i>Reset
                                    </button>

                                    <?php if (!$showRekap && !$absensi_terisi): ?>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i>Simpan Absensi
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                            <i class="fas fa-print me-1"></i>Print
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php elseif ($validKode && empty($mahasiswa)): ?>
                <div class="card text-center fade-in">
                    <div class="card-body py-5">
                        <i class="fas fa-users-slash fa-5x text-warning mb-4"></i>
                        <h3 class="text-warning">Tidak Ada Data Mahasiswa</h3>
                        <p class="text-muted mb-4">
                            <?php if ($asprak_group): ?>
                                Tidak ada mahasiswa di <strong>Group <?= $asprak_group ?></strong> untuk praktikum ini.
                            <?php else: ?>
                                Tidak ada mahasiswa yang terdaftar pada praktikum ini.
                            <?php endif; ?>
                        </p>
                        <button type="button" class="btn btn-primary" onclick="resetForm()">
                            <i class="fas fa-redo me-1"></i>Coba Lagi
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Action Button -->
    <?php if ($validKode && !empty($mahasiswa)): ?>
        <div class="floating-action">
            <div class="btn-group-vertical shadow">
                <?php if (!$showRekap && !$absensi_terisi): ?>
                    <button type="submit" form="absensiForm" class="btn btn-success btn-lg rounded-circle p-3">
                        <i class="fas fa-save"></i>
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary btn-lg rounded-circle p-3" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang belum disimpan akan hilang.')) {
                document.getElementById('filterForm').reset();
                window.location.href = '?page=absensi';
            }
        }

        function toggleRekap() {
            const form = document.getElementById('filterForm');
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = '<?= $showRekap ? "" : "lihat_rekap" ?>';
            form.appendChild(actionInput);
            form.submit();
        }

        // Show loading spinner on form submit
        document.getElementById('filterForm').addEventListener('submit', function() {
            document.getElementById('loadingSpinner').style.display = 'block';
        });

        // Auto-submit jika kode random kadaluarsa
        <?php if ($kode_warning && strpos($kode_warning, 'kadaluarsa') !== false): ?>
            setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 3000);
        <?php endif; ?>

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>