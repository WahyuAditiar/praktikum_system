<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AbsensiController.php';

$database = new Database();
$db = $database->getConnection();

if (!in_array($_SESSION['role'] ?? '', ['staff_lab', 'admin', 'asisten_praktikum'])) {
    header("Location: ../../index.php");
    exit;
}

$controller = new AbsensiController($db, $_SESSION['role']);

// Get filter parameters
$selected_praktikum = $_POST['praktikum_id'] ?? $_GET['praktikum_id'] ?? '';
$selected_kelas = $_POST['kelas'] ?? $_GET['kelas'] ?? '';
$selected_jadwal = $_POST['jadwal_praktikum_id'] ?? '';
$selected_pertemuan = $_POST['pertemuan'] ?? '';

// Get data for filters
$praktikum_list = $controller->getPraktikumFromMahasiswa();
$jadwal_praktikum = $controller->getJadwalPraktikum();

// Initialize data
$rekap_data = [];
$statistik = [];
$rekap_pertemuan = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['praktikum_id'])) {
    if ($selected_praktikum) {
        // Get rekap data
        $rekap_data = $controller->getRekapPerPraktikumKelas($selected_praktikum, $selected_kelas);
        $statistik = $controller->getStatistikKehadiran($selected_praktikum, $selected_kelas);
        
        // Jika ada jadwal dan pertemuan yang dipilih, get rekap per pertemuan
        if ($selected_jadwal && $selected_pertemuan) {
            $rekap_pertemuan = $controller->getRekapPerPertemuan($selected_jadwal, $selected_pertemuan);
        }
    } else {
        $error = "Harap pilih praktikum terlebih dahulu.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Praktikum</title>
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
        .stat-card {
            border-left: 4px solid #007bff;
        }
        .stat-card.hadir { border-left-color: #28a745; }
        .stat-card.sakit { border-left-color: #ffc107; }
        .stat-card.izin { border-left-color: #17a2b8; }
        .stat-card.alfa { border-left-color: #dc3545; }
        .badge-hadir { background-color: #28a745; }
        .badge-sakit { background-color: #ffc107; color: #000; }
        .badge-izin { background-color: #17a2b8; }
        .badge-alfa { background-color: #dc3545; }
        .progress { height: 8px; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-primary mb-1">
                    <i class="fas fa-chart-bar me-2"></i>Rekap Absensi Praktikum
                </h2>
                <p class="text-muted">Monitoring Kehadiran Praktikan per Kelas</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Data</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" id="filterForm">
                    <div class="row g-3">
                        <!-- Pilih Praktikum -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pilih Praktikum</label>
                            <select class="form-select" name="praktikum_id" id="praktikumSelect" required>
                                <option value="">-- Pilih Praktikum --</option>
                                <?php foreach ($praktikum_list as $praktikum): ?>
                                    <option value="<?= $praktikum['id'] ?>" 
                                        <?= $selected_praktikum == $praktikum['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($praktikum['display_name'] ?? $praktikum['nama_praktikum']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Pilih Kelas -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select class="form-select" name="kelas" id="kelasSelect">
                                <option value="">-- Semua Kelas --</option>
                                <option value="A" <?= $selected_kelas == 'A' ? 'selected' : '' ?>>Kelas A</option>
                                <option value="B" <?= $selected_kelas == 'B' ? 'selected' : '' ?>>Kelas B</option>
                                <option value="C" <?= $selected_kelas == 'C' ? 'selected' : '' ?>>Kelas C</option>
                                <option value="D" <?= $selected_kelas == 'D' ? 'selected' : '' ?>>Kelas D</option>
                            </select>
                        </div>

                        <!-- Pilih Jadwal (Opsional untuk rekap per pertemuan) -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Jadwal Praktikum</label>
                            <select class="form-select" name="jadwal_praktikum_id" id="jadwalSelect">
                                <option value="">-- Pilih Jadwal --</option>
                                <?php foreach ($jadwal_praktikum as $jadwal): ?>
                                    <option value="<?= $jadwal['id'] ?>" 
                                        <?= $selected_jadwal == $jadwal['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($jadwal['nama_praktikum']) ?> - <?= $jadwal['kelas'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Pilih Pertemuan -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Pertemuan</label>
                            <select class="form-select" name="pertemuan" id="pertemuanSelect">
                                <option value="">-- Semua --</option>
                                <?php for ($i = 1; $i <= 16; $i++): ?>
                                    <option value="<?= $i ?>" <?= $selected_pertemuan == $i ? 'selected' : '' ?>>
                                        Pertemuan <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Tombol -->
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Tampilkan Rekap
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel me-1"></i>Export Excel
                                </button>
                                <button type="button" class="btn btn-danger" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i>Print
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($rekap_data)): ?>
            <!-- Statistik Overview -->
            <div class="row mb-4">
                <?php foreach ($statistik as $stat): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="card-title"><?= $stat['nama_praktikum'] ?> - <?= $stat['kelas'] ?></h6>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <small class="text-success">Hadir</small>
                                        <div class="h6 mb-0"><?= $stat['total_hadir'] ?></div>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-warning">Sakit</small>
                                        <div class="h6 mb-0"><?= $stat['total_sakit'] ?></div>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-info">Izin</small>
                                        <div class="h6 mb-0"><?= $stat['total_izin'] ?></div>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-danger">Alfa</small>
                                        <div class="h6 mb-0"><?= $stat['total_alfa'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Rekap Detail per Mahasiswa -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Rekap Detail Kehadiran
                        <?php if ($selected_kelas): ?> - Kelas <?= $selected_kelas ?><?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="rekapTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Kelas</th>
                                    <th>Hadir</th>
                                    <th>Sakit</th>
                                    <th>Izin</th>
                                    <th>Alfa</th>
                                    <th>Total</th>
                                    <th>Persentase</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rekap_data as $i => $data): ?>
                                    <tr>
                                        <td class="text-center"><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($data['nim']) ?></td>
                                        <td><?= htmlspecialchars($data['nama']) ?></td>
                                        <td class="text-center"><?= $data['kelas'] ?></td>
                                        <td class="text-center text-success"><?= $data['total_hadir'] ?></td>
                                        <td class="text-center text-warning"><?= $data['total_sakit'] ?></td>
                                        <td class="text-center text-info"><?= $data['total_izin'] ?></td>
                                        <td class="text-center text-danger"><?= $data['total_alfa'] ?></td>
                                        <td class="text-center fw-bold"><?= $data['total_pertemuan'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2">
                                                    <div class="progress-bar 
                                                        <?= $data['persentase_hadir'] >= 80 ? 'bg-success' : 
                                                           ($data['persentase_hadir'] >= 60 ? 'bg-warning' : 'bg-danger') ?>"
                                                        style="width: <?= $data['persentase_hadir'] ?>%">
                                                    </div>
                                                </div>
                                                <span class="small"><?= $data['persentase_hadir'] ?>%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($data['persentase_hadir'] >= 80): ?>
                                                <span class="badge badge-hadir">Baik</span>
                                            <?php elseif ($data['persentase_hadir'] >= 60): ?>
                                                <span class="badge badge-sakit">Cukup</span>
                                            <?php else: ?>
                                                <span class="badge badge-alfa">Kurang</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rekap Per Pertemuan (jika dipilih) -->
            <?php if (!empty($rekap_pertemuan)): ?>
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-day me-2"></i>
                            Rekap Pertemuan <?= $selected_pertemuan ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rekap_pertemuan as $i => $data): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= $data['nim'] ?></td>
                                            <td><?= $data['nama'] ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= $data['status'] == 'hadir' ? 'badge-hadir' : 
                                                       ($data['status'] == 'sakit' ? 'badge-sakit' : 
                                                       ($data['status'] == 'izin' ? 'badge-izin' : 'badge-alfa')) ?>">
                                                    <?= ucfirst($data['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= $data['keterangan'] ?: '-' ?></td>
                                            <td><?= date('d/m/Y', strtotime($data['tanggal'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-warning text-center py-4">
                <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                <h4>Tidak Ada Data Rekap</h4>
                <p>Tidak ditemukan data absensi untuk filter yang dipilih.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportToExcel() {
            // Simple Excel export
            let table = document.getElementById('rekapTable');
            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + escape(html);
            let link = document.createElement('a');
            link.href = url;
            link.download = 'rekap_absensi_<?= date('Y-m-d') ?>.xls';
            link.click();
        }

        // Auto update kelas options based on selected praktikum
        document.getElementById('praktikumSelect').addEventListener('change', function() {
            // You can implement AJAX to get available classes for selected praktikum
            // For now, we'll just reset the kelas filter
            document.getElementById('kelasSelect').value = '';
        });
    </script>
</body>
</html>