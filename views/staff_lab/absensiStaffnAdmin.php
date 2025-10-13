<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/absensiController.php';

$database = new Database();
$db = $database->getConnection();

if (!in_array($_SESSION['role'] ?? '', ['staff_lab', 'admin'])) {
    header("Location: ../../index.php");
    exit;
}

$controller = new AbsensiController($db, $_SESSION['role']);
$jadwal_praktikum = $controller->getJadwalPraktikum();

$mahasiswa = [];
$absensi_existing = [];
$selected_jadwal = $_POST['jadwal_praktikum_id'] ?? '';
$selected_pertemuan = $_POST['pertemuan'] ?? '';
$error = '';
$showRekap = false;
$absensi_terisi = false;
$detail_jadwal = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CEK JIKA INI SIMPAN ABSENSI (EDIT OLEH STAFF/ADMIN)
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

    

    if ($selected_jadwal && $selected_pertemuan) {
        $mahasiswa = $controller->getMahasiswaByJadwal($selected_jadwal);
       $absensi_existing = $controller->getAbsensi($selected_jadwal, $selected_pertemuan);
        $detail_jadwal = $controller->getDetailJadwal($selected_jadwal);

        // CEK APAKAH ABSENSI SUDAH DIISI
        $absensi_terisi = !empty($absensi_existing);
    } else {
        $error = "Harap pilih jadwal dan pertemuan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Absensi - Staff/Admin</title>
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

        .absensi-terisi-alert {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
        }

        .staff-edit-badge {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-primary mb-1"><i class="fas fa-users-cog me-2"></i>Monitoring Absensi Praktikan</h2>
                <p class="text-muted">Staff Lab & Admin - View & Edit Absensi</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
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

        <!-- Notifikasi Staff/Admin Mode -->
        <div class="alert staff-edit-badge alert-dismissible fade show">
            <i class="fas fa-user-shield me-2"></i>
            <strong>Staff/Admin Mode</strong> - Anda dapat melihat dan mengedit semua data absensi
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <?php if ($absensi_terisi && !$showRekap): ?>
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-edit me-2"></i>
                <strong>Edit Mode</strong> - Anda sedang mengedit absensi yang sudah tersimpan
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Input Data -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Data</h5>
                    <?php if (!empty($mahasiswa)): ?>
                        <div>
                            <?php if ($showRekap): ?>
                                <button type="button" class="btn btn-warning btn-sm" onclick="toggleRekap()">
                                    <i class="fas fa-edit me-1"></i>Edit Absensi
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-info btn-sm" onclick="toggleRekap()">
                                    <i class="fas fa-chart-bar me-1"></i>Lihat Rekap
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" id="filterForm">
                    <div class="row g-3">
                        <!-- Pilih Praktikum -->
                        <div class="col-md-6">
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

                       <!-- Pilih Pertemuan -->
<div class="col-md-4">
    <label class="form-label fw-semibold">Pertemuan</label>
    <select class="form-select" name="pertemuan" required>
        <option value="">-- Pilih Pertemuan --</option>
        <?php for ($i = 1; $i <= 16; $i++): ?>
            <option value="<?= $i ?>" <?= $selected_pertemuan == $i ? 'selected' : '' ?>>Pertemuan <?= $i ?></option>
        <?php endfor; ?>
    </select>
</div>
                        <!-- Tombol Submit -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($mahasiswa)): ?>
            <div class="card <?= $showRekap ? 'rekap-mode' : '' ?>" id="absensiCard">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-3">
                                <i class="fas fa-list-check me-2"></i>
                                <?= $showRekap ? 'Rekap Absensi' : 'Form Absensi' ?> - Pertemuan <?= $selected_pertemuan ?>
                            </h5>
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
                                <span class="badge bg-warning">Tersimpan</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Statistik Kehadiran -->
                    <?php if ($showRekap): ?>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Statistik Kehadiran</h5>
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
                                        <th>hadir</th>
                                        <td><?= $hadir ?> orang</td>
                                        <td><?= $total_mahasiswa > 0 ? round(($hadir / $total_mahasiswa) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <th>sakit</th>
                                        <td><?= $sakit ?> orang</td>
                                        <td><?= $total_mahasiswa > 0 ? round(($sakit / $total_mahasiswa) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                    <tr class="table-info">
                                        <th>izin</th>
                                        <td><?= $izin ?> orang</td>
                                        <td><?= $total_mahasiswa > 0 ? round(($izin / $total_mahasiswa) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                    <tr class="table-danger">
                                        <th>alfa</th>
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
                        <?php if (!$showRekap): ?>
                            <input type="hidden" name="action" value="simpan">
                        <?php else: ?>
                            <input type="hidden" name="action" value="lihat_rekap">
                        <?php endif; ?>
                        <input type="hidden" name="jadwal_praktikum_id" value="<?= $selected_jadwal ?>">
                        <input type="hidden" name="pertemuan" value="<?= $selected_pertemuan ?>">
                        <input type="hidden" name="tanggal" value="<?= date('Y-m-d') ?>">

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
                                                        <?= $showRekap ? 'disabled' : '' ?>>
                                                        <option value="hadir" <?= ($data['status'] ?? '') == 'hadir' ? 'selected' : '' ?>>hadir</option>
                                                        <option value="sakit" <?= ($data['status'] ?? '') == 'sakit' ? 'selected' : '' ?>>sakit</option>
                                                        <option value="izin" <?= ($data['status'] ?? '') == 'izin' ? 'selected' : '' ?>>izin</option>
                                                        <option value="alfa" <?= ($data['status'] ?? '') == 'alfa' ? 'selected' : '' ?>>alfa</option>
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
                                                        <?= $showRekap ? 'disabled' : '' ?>>
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

                            <button type="button" class="btn btn-warning" onclick="resetForm()">
                                <i class="fas fa-redo me-1"></i>Reset
                            </button>

                            <a href="?page=dashboard" class="btn btn-outline-primary">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>

                            <?php if (!$showRekap): ?>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Simpan Perubahan
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
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-warning text-center py-4">
                <i class="fas fa-users-slash fa-3x text-warning mb-3"></i>
                <h4 class="alert-heading">Tidak Ada Data Mahasiswa</h4>
                <p class="mb-0">Tidak ada mahasiswa yang terdaftar pada praktikum ini.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleRekap() {
            const form = document.getElementById('filterForm');
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = '<?= $showRekap ? "" : "lihat_rekap" ?>';
            form.appendChild(actionInput);
            form.submit();
        }

        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form?')) {
                document.getElementById('filterForm').reset();
                document.getElementById('absensiForm').reset();
            }
        }
    </script>
</body>
</html>