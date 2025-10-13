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

            // DEBUG DETAILED
            if (isset($_GET['debug_detail'])) {
                $debug_info = $groupModel->debugAsprakAssignment($_SESSION['user_id'], $selected_jadwal);
                echo "<div class='alert alert-warning'>";
                echo "<h6>🐛 Detailed Debug Info:</h6>";
                echo "<pre>" . print_r($debug_info, true) . "</pre>";
                echo "</div>";
            }

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
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
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

        .kode-warning {
            background: linear-gradient(45deg, #ffeb3b, #ff9800);
            color: #000;
            font-weight: bold;
            border: none;
        }

        .absensi-terisi-alert {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
        }

        .filter-container .form-select,
        .filter-container .form-control {
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .filter-container .form-label {
            font-size: 0.875rem;
            color: #495057;
        }

        .filter-container .form-text {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .filter-container .input-group-text {
            border: 1px solid #dee2e6;
            border-left: none;
        }
        
        .group-badge {
            font-size: 0.9rem;
            padding: 6px 12px;
        }
        
        .debug-info {
            font-size: 0.8rem;
            background: #f8f9fa;
            border-left: 4px solid #6c757d;
        }
        
        .multi-group-info {
            background: linear-gradient(45deg, #2196F3, #21CBF3);
            color: white;
            border: none;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-primary mb-1"><i class="fas fa-users me-2"></i>Input Absensi Praktikan</h2>
                <p class="text-muted">Isi kehadiran praktikan berdasarkan jadwal praktikum</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Debug Info (Hapus setelah berhasil) -->
        <?php if ($validKode && isset($_GET['debug'])): ?>
            <div class="alert debug-info">
                <h6><i class="fas fa-bug me-2"></i>Debug Information:</h6>
                <div class="row">
                    <div class="col-md-3">
                        <strong>User ID:</strong> <?= $_SESSION['user_id'] ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Jadwal ID:</strong> <?= $selected_jadwal ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Group:</strong> <?= $asprak_group ?? 'NULL' ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Jumlah Mahasiswa:</strong> <?= count($mahasiswa) ?>
                    </div>
                </div>
                <?php if ($asprak_group): ?>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Status Absensi Group <?= $asprak_group ?>:</strong> 
                        <?= $absensi_terisi ? '✅ SUDAH DIISI' : '❌ BELUM DIISI' ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Info Multi Group Support -->
        <?php if ($validKode && $asprak_group): ?>
            <div class="alert multi-group-info alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users me-3 fa-lg"></i>
                    <div>
                        <h6 class="mb-1">Sistem Multi-Group Support</h6>
                        <p class="mb-0">
                            Anda di <strong>Group <?= $asprak_group ?></strong>. 
                            Setiap group dapat mengisi absensi independen dalam praktikum yang sama.
                            <?php if ($absensi_terisi): ?>
                                <br><strong>Status: Absensi Group <?= $asprak_group ?> sudah tersimpan.</strong>
                            <?php else: ?>
                                <br><strong>Status: Absensi Group <?= $asprak_group ?> belum diisi.</strong>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Info Group Asisten -->
        <?php if ($asprak_group): ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-users me-3 fa-lg"></i>
                <div>
                    <h6 class="mb-1">Anda bertugas di <span class="badge group-badge bg-primary">Group <?= $asprak_group ?></span></h6>
                    <p class="mb-0">Menampilkan <?= count($mahasiswa) ?> mahasiswa dari group Anda</p>
                </div>
            </div>
        <?php elseif ($validKode && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-warning">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                    <div>
                        <h6 class="mb-1">Anda belum ditugaskan di group manapun</h6>
                        <p class="mb-0">Silakan hubungi admin untuk ditugaskan ke group tertentu.</p>
                        <?php if ($_SESSION['role'] === 'asisten_praktikum'): ?>
                            <small class="text-muted">Saat ini menampilkan semua mahasiswa dalam praktikum</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Kode Random -->
        <?php if ($kode_warning): ?>
            <div class="alert kode-warning alert-dismissible fade show">
                <i class="fas fa-clock me-2"></i><?= $kode_warning ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Absensi Sudah Diisi -->
        <?php if ($absensi_terisi && $asprak_group): ?>
            <div class="alert absensi-terisi-alert alert-dismissible fade show">
                <i class="fas fa-info-circle me-2"></i>
                Absensi untuk <strong>Group <?= $asprak_group ?></strong> pertemuan ini sudah disimpan. Mode baca-only.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($absensi_terisi && !$asprak_group): ?>
            <div class="alert alert-warning alert-dismissible fade show">
                <i class="fas fa-info-circle me-2"></i>
                Absensi untuk praktikum ini sudah disimpan oleh salah satu group. Mode baca-only.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Input Data -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Data</h5>
                    <?php if ($validKode && !empty($mahasiswa)): ?>
                        <div>
                            <?php if ($showRekap && !$absensi_terisi): ?>
                                <button type="button" class="btn btn-warning btn-sm" onclick="toggleRekap()">
                                    <i class="fas fa-edit me-1"></i>Edit Absensi
                                </button>
                            <?php elseif (!$showRekap): ?>
                                <button type="button" class="btn btn-info btn-sm" onclick="toggleRekap()">
                                    <i class="fas fa-chart-bar me-1"></i>Lihat Rekap
                                </button>
                            <?php else: ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Absensi Group <?= $asprak_group ?> Tersimpan
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-3">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <!-- Pilih Praktikum -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold mb-1">Pilih Praktikum</label>
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
                        <div class="col-md-2">
                            <label class="form-label fw-semibold mb-1">Kode Random</label>
                            <input type="text" name="kode_random" class="form-control" required
                                placeholder="Masukkan kode" value="<?= htmlspecialchars($kode_random_input) ?>">
                        </div>

                        <!-- Tanggal -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold mb-1">Tanggal</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" value="<?= date('d/m/Y') ?>" readonly>
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Pertemuan -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold mb-1">Pertemuan</label>
                            <select class="form-select" name="pertemuan" required>
                                <option value="">-- Pilih --</option>
                                <?php for ($i = 1; $i <= 16; $i++): ?>
                                    <option value="<?= $i ?>" <?= $selected_pertemuan == $i ? 'selected' : '' ?>>Pertemuan <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Tombol Tampilkan -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Tampilkan
                            </button>
                        </div>

                        <!-- Debug Link -->
                        <div class="col-md-1">
                            <div class="btn-group">
                                <a href="?<?= $_SERVER['QUERY_STRING'] ?>&debug=1" class="btn btn-outline-secondary btn-sm" title="Debug Basic">
                                    <i class="fas fa-bug"></i>
                                </a>
                                <a href="?<?= $_SERVER['QUERY_STRING'] ?>&debug_detail=1" class="btn btn-outline-danger btn-sm" title="Debug Detail">
                                    <i class="fas fa-bug"></i>+
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Absensi -->
        <?php if ($validKode && !empty($mahasiswa)): ?>
            <div class="card <?= $showRekap ? 'rekap-mode' : '' ?>" id="absensiCard">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-list-check me-2"></i>
                                <?= $showRekap ? 'Rekap Absensi' : 'Form Absensi' ?> - Pertemuan <?= $selected_pertemuan ?>
                                <?php if ($asprak_group): ?>
                                    <span class="badge bg-light text-dark ms-2">Group <?= $asprak_group ?></span>
                                <?php endif; ?>
                            </h5>
                            <?php if ($asprak_group): ?>
                                <small class="text-light">Menampilkan <?= count($mahasiswa) ?> mahasiswa dari Group <?= $asprak_group ?></small>
                            <?php else: ?>
                                <small class="text-light">Menampilkan semua <?= count($mahasiswa) ?> mahasiswa (belum ada group assignment)</small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <?php if (isset($detail_jadwal)): ?>
                                <span class="badge bg-light text-dark me-2">
                                    <?= $detail_jadwal['nama_praktikum'] ?? $detail_jadwal['nama_matkul'] ?? '' ?> - Kelas <?= $detail_jadwal['kelas'] ?? '' ?>
                                </span>
                            <?php endif; ?>
                            <span class="badge bg-info me-2">
                                <i class="fas fa-calendar me-1"></i><?= date('d/m/Y') ?>
                            </span>
                            <?php if ($absensi_terisi): ?>
                                <span class="badge bg-warning">
                                    <?= $asprak_group ? 'Group ' . $asprak_group . ' Tersimpan' : 'Tersimpan' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Statistik Kehadiran -->
                    <?php if ($showRekap): ?>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>
                                    Statistik Kehadiran 
                                    <?php if ($asprak_group): ?>
                                        - Group <?= $asprak_group ?>
                                    <?php endif; ?>
                                </h5>
                                <?php
                                $total_mahasiswa = count($mahasiswa);
                                $hadir = 0;
                                $sakit = 0;
                                $izin = 0;
                                $alfa = 0;

                                foreach ($mahasiswa as $mhs) {
                                    $absen = array_filter($absensi_existing, fn($a) => $a['mahasiswa_id'] == $mhs['id']);
                                    $data = $absen ? reset($absen) : null;
                                    $status = $data['status'] ?? 'belum_diisi';

                                    switch ($status) {
                                        case 'hadir':
                                            $hadir++;
                                            break;
                                        case 'sakit':
                                            $sakit++;
                                            break;
                                        case 'izin':
                                            $izin++;
                                            break;
                                        case 'alfa':
                                            $alfa++;
                                            break;
                                    }
                                }
                                ?>
                                <table class="table table-sm table-bordered">
                                    <tr class="table-success">
                                        <th>Hadir</th>
                                        <td><?= $hadir ?> orang</td>
                                        <td><?= $total_mahasiswa > 0 ? round(($hadir / $total_mahasiswa) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <th>Sakit</th>
                                        <td><?= $sakit ?> orang</td>
                                        <td><?= $total_mahasiswa > 0 ? round(($sakit / $total_mahasiswa) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                    <tr class="table-info">
                                        <th>Izin</th>
                                        <td><?= $izin ?> orang</td>
                                        <td><?= $total_mahasiswa > 0 ? round(($izin / $total_mahasiswa) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                    <tr class="table-danger">
                                        <th>Alfa</th>
                                        <td><?= $alfa ?> orang</td>
                                        <td><?= $total_mahasiswa > 0 ? round(($alfa / $total_mahasiswa) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th>Total</th>
                                        <td colspan="2"><strong><?= $total_mahasiswa ?> orang</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

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
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
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
                                        <tr>
                                            <td class="text-center"><?= $i + 1 ?></td>
                                            <td><span class="fw-semibold"><?= htmlspecialchars($mhs['nim']) ?></span></td>
                                            <td><?= htmlspecialchars($mhs['nama']) ?></td>
                                            <td>
                                                <!-- Mode Input -->
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

                                                <!-- Mode Tampil (Rekap) -->
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
                                                        <span class="badge <?= $class ?>"><?= ucfirst($status) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Belum Absen</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Mode Input -->
                                                <div class="absensi-input">
                                                   <input type="text"
                                                        name="absensi[<?= $mhs['id'] ?>][keterangan]"
                                                        class="form-control form-control-sm"
                                                        value="<?= htmlspecialchars($data['keterangan'] ?? '') ?>"
                                                        placeholder="Opsional"
                                                        <?= (($showRekap || $absensi_terisi)) ? 'disabled' : '' ?>>
                                                </div>

                                                <!-- Mode Tampil (Rekap) -->
                                                <div class="absensi-tampil">
                                                    <small><?= htmlspecialchars($data['keterangan'] ?? '-') ?></small>
                                                </div>
                                            </td>
                                            <?php if ($showRekap): ?>
                                                <td class="text-center">
                                                    <?php if ($hasAbsensi): ?>
                                                        <span class="text-success"><i class="fas fa-check"></i></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="?page=absensi" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>

                            <!-- Tombol Reset -->
                            <button type="button" class="btn btn-warning" onclick="resetForm()">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>

                            <?php if (!$showRekap && !$absensi_terisi): ?>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Simpan Absensi Group <?= $asprak_group ?>
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
        <?php elseif ($validKode && empty($mahasiswa)): ?>
            <div class="alert alert-warning text-center py-4">
                <i class="fas fa-users-slash fa-3x text-warning mb-3"></i>
                <h4 class="alert-heading">Tidak Ada Data Mahasiswa</h4>
                <p class="mb-0">
                    <?php if ($asprak_group): ?>
                        Tidak ada mahasiswa di <strong>Group <?= $asprak_group ?></strong> untuk praktikum ini.
                    <?php else: ?>
                        Tidak ada mahasiswa yang terdaftar pada praktikum ini.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang belum disimpan akan hilang.')) {
                // Reset form filter
                document.getElementById('filterForm').reset();

                // Redirect ke halaman absensi kosong
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

        // Auto-submit jika kode random kadaluarsa
        <?php if ($kode_warning && strpos($kode_warning, 'kadaluarsa') !== false): ?>
            setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>