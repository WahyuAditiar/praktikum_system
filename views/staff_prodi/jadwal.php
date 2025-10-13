<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/JadwalModel.php';
require_once __DIR__ . '/../../controllers/JadwalController.php';


checkAuth();
checkRole(['staff_prodi', 'admin']);

$database = new Database();
$db = $database->getConnection();
$jadwalController = new JadwalController($db);

$page_title = "Manajemen Jadwal Kuliah";
$error = '';
$success = '';



// Di bagian atas file, tambahkan handling untuk POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = $_POST['hapus'];
    $result = $jadwalController->deleteJadwal($id);

    if ($result['success']) {
        $_SESSION['success_message'] = $result['message'];
    } else {
        $_SESSION['error_message'] = implode('<br>', $result['errors']);
    }

    header("Location: jadwal.php");
    exit();
}

// Handle session messages
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}



// Handle form actions
if ($_POST) {
    if (isset($_POST['tambah_jadwal'])) {
        $result = $jadwalController->createJadwal($_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = implode('<br>', $result['errors']);
        }
    } elseif (isset($_POST['edit_jadwal'])) {
        $id = $_POST['id'];
        $result = $jadwalController->updateJadwal($id, $_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = implode('<br>', $result['errors']);
        }
    }
}

// Handle delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Debug logging
    error_log("=== DELETE JADWAL ATTEMPT ===");
    error_log("Jadwal ID: " . $id);
    error_log("User: " . ($_SESSION['username'] ?? 'Unknown'));
    error_log("Time: " . date('Y-m-d H:i:s'));

    $result = $jadwalController->deleteJadwal($id);

    if ($result['success']) {
        $success = $result['message'];
        error_log("DELETE SUCCESS: " . $success);

        // Redirect untuk avoid resubmission
        header("Location: jadwal.php?success=" . urlencode($success));
        exit();
    } else {
        $error = implode('<br>', $result['errors']);
        error_log("DELETE FAILED: " . $error);

        // Tampilkan error detail untuk debugging
        $error .= "<br><small>ID: " . $id . "</small>";
    }
}

// Handle success message dari redirect
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Get all data
$jadwalData = $jadwalController->getAllJadwal();
$filterHari = $_GET['hari'] ?? null;

if ($filterHari) {
    $jadwalData = $jadwalController->getJadwalByHari($filterHari);
} else {
    $jadwalData = $jadwalController->getAllJadwal();
}
$statistics = $jadwalController->getStatistics();
$hariStats = $jadwalController->getStatisticsByHari();
$dropdownData = $jadwalController->getDropdownData();

// Get data for edit
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $jadwalController->getJadwalById($_GET['edit']);
}

// Daftar hari
$daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

// Daftar kelas
$daftarKelas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

// Time slots

$jamMulai = [
    '08:00:00',
    '08:50:00',
    '09:40:00',
    '10:30:00',
    '11:20:00',
    '12:10:00',
    '13:00:00',
    '13:50:00',
    '14:40:00',
    '15:30:00',
    '16:20:00',
    '17:10:00'
];

$jamSelesai = [
    '08:50:00',
    '09:40:00',
    '10:30:00',
    '11:20:00',
    '12:10:00',
    '13:00:00',
    '13:50:00',
    '14:40:00',
    '15:30:00',
    '16:20:00',
    '17:10:00',
    '18:00:00'
];
?>


<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Jadwal Kuliah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Jadwal</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Notifications -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fas fa-ban"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fas fa-check"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row">
                <?php
                $statsMap = [];
                foreach ($statistics as $stat) {
                    $statsMap[$stat['status']] = $stat['total'];
                }

                $hariMap = [];
                foreach ($hariStats as $stat) {
                    $hariMap[$stat['hari']] = $stat['total'];
                }
                ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo array_sum($statsMap); ?></h3>
                            <p>Total Jadwal</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $statsMap['active'] ?? 0; ?></h3>
                            <p>Jadwal Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo count($dropdownData['mata_kuliah']); ?></h3>
                            <p>Mata Kuliah Tersedia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?php echo count($dropdownData['dosen']); ?></h3>
                            <p>Dosen Tersedia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Hari Statistics -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Distribusi Jadwal per Hari
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($daftarHari as $hari):
                                    $count = $hariMap[$hari] ?? 0;
                                ?>
                                    <div class="col-md-2 col-4 text-center mb-3">
                                        <a href="jadwal_per_hari.php?hari=<?php echo urlencode($hari); ?>" class="text-decoration-none">
                                            <div class="info-box bg-light">
                                                <div class="info-box-content">
                                                    <span class="info-box-text"><?php echo $hari; ?></span>
                                                    <span class="info-box-number"><?php echo $count; ?> Jadwal</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-primary"
                                                            style="width: <?php echo min(100, ($count / max(1, array_sum($hariMap))) * 100); ?>%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload & Download -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary shadow-sm">
                        <!-- <div class="card-header">
        <h3 class="card-title"><i class="fas fa-upload mr-1"></i> Upload Jadwal Excel</h3>
      </div>
      <div class="card-body">
<form id="uploadForm" enctype="multipart/form-data">
  <div class="form-group">
    <label for="file">Pilih File (.xlsx)</label>
    <div class="custom-file">
      <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx" required>
      <label class="custom-file-label" for="file">Pilih file...</label>
    </div>
  </div>
  <button type="submit" class="btn btn-success btn-block">
    <i class="fas fa-upload"></i> Upload
  </button>
</form>-->

                        <!-- Progress bar -->
                        <div class="progress mt-3" style="height: 20px; display:none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                role="progressbar" style="width: 0%">0%</div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-secondary shadow-sm">
                    <!--<div class="card-header">
        <h3 class="card-title"><i class="fas fa-download mr-1"></i> Download Template</h3>
      </div>
      <div class="card-body text-center">
       <a href="../../controllers/export_jadwal.php" class="btn btn-success">Download Template</a>
            <i class="fas fa-file-download mr-1"></i> Download Template
        </a>
      </div>
    </div>-->
                </div>
            </div>



            <div class="row">
                <div class="col-md-4">
                    <!-- Form Jadwal -->
                    <div class="card <?php echo $editData ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-<?php echo $editData ? 'edit' : 'plus'; ?> mr-1"></i>
                                <?php echo $editData ? 'Edit Jadwal' : 'Tambah Jadwal Baru'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="jadwalForm">
                                <?php if ($editData): ?>
                                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                                    <input type="hidden" name="edit_jadwal" value="1">
                                <?php else: ?>
                                    <input type="hidden" name="tambah_jadwal" value="1">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="mata_kuliah_id">Mata Kuliah <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="mata_kuliah_id" name="mata_kuliah_id" required
                                        data-placeholder="Cari atau pilih mata kuliah">
                                        <option value=""></option>
                                        <?php if (!empty($dropdownData['mata_kuliah'])): ?>
                                            <?php foreach ($dropdownData['mata_kuliah'] as $matkul):
                                                if (($matkul['status'] ?? '') == 'active'): ?>
                                                    <option value="<?php echo $matkul['id']; ?>"
                                                        <?php echo ($editData && $editData['mata_kuliah_id'] == $matkul['id']) ? 'selected' : ''; ?>
                                                        data-kode="<?php echo htmlspecialchars($matkul['kode_mk'] ?? ''); ?>"
                                                        data-sks="<?php echo htmlspecialchars($matkul['sks'] ?? ''); ?>">
                                                        <?php
                                                        $kode_mk = $matkul['kode_mk'] ?? 'Kode tidak tersedia';
                                                        $nama_mk = $matkul['nama_mk'] ?? 'Nama tidak tersedia';
                                                        $sks = $matkul['sks'] ?? '0';
                                                        echo htmlspecialchars($kode_mk . ' - ' . $nama_mk . ' (' . $sks . ' SKS)');
                                                        ?>
                                                    </option>
                                            <?php endif;
                                            endforeach; ?>
                                        <?php else: ?>
                                            <option value="">Tidak ada mata kuliah tersedia</option>
                                        <?php endif; ?>
                                    </select>
                                </div>



                                <div class="form-group">
                                    <label for="dosen_id">Dosen Pengajar <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="dosen_id" name="dosen_id" required
                                        data-placeholder="Cari nama atau NIDN dosen">
                                        <option value=""></option>
                                        <?php if (!empty($dropdownData['dosen'])): ?>
                                            <?php foreach ($dropdownData['dosen'] as $dosen): ?>
                                                <option value="<?php echo $dosen['id']; ?>"
                                                    <?php echo ($editData && $editData['dosen_id'] == $dosen['id']) ? 'selected' : ''; ?>
                                                    data-nidn="<?php echo htmlspecialchars($dosen['nidn'] ?? ''); ?>"
                                                    data-status="<?php echo htmlspecialchars($dosen['status'] ?? ''); ?>">
                                                    <?php
                                                    $nidn = $dosen['nidn'] ?? 'NIDN tidak tersedia';
                                                    $nama = $dosen['nama'] ?? 'Nama tidak tersedia';
                                                    $status = $dosen['status'] ?? 'tidak diketahui';
                                                    $status_text = ucfirst(str_replace('_', ' ', $status));
                                                    echo htmlspecialchars($nidn . ' - ' . $nama . ' (' . $status_text . ')');
                                                    ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">Tidak ada dosen tersedia</option>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (empty($dropdownData['dosen'])): ?>
                                        <small class="text-danger">Tidak ada data dosen. Pastikan sudah menambahkan data dosen terlebih dahulu.</small>
                                    <?php endif; ?>
                                </div>


                                <div class="form-group">
                                    <label for="ruangan_id">Ruangan <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="ruangan_id" name="ruangan_id" required
                                        data-placeholder="Cari kode atau nama ruangan">
                                        <option value=""></option>
                                        <?php if (!empty($dropdownData['ruangan'])): ?>
                                            <?php foreach ($dropdownData['ruangan'] as $ruangan):
                                                if (($ruangan['status'] ?? '') == 'active'): ?>
                                                    <option value="<?php echo $ruangan['id']; ?>"
                                                        <?php echo ($editData && $editData['ruangan_id'] == $ruangan['id']) ? 'selected' : ''; ?>
                                                        data-kapasitas="<?php echo htmlspecialchars($ruangan['kapasitas'] ?? ''); ?>"
                                                        data-lokasi="<?php echo htmlspecialchars($ruangan['lokasi'] ?? ''); ?>">
                                                        <?php
                                                        $kode_ruangan = $ruangan['kode_ruangan'] ?? 'Kode tidak tersedia';
                                                        $nama_ruangan = $ruangan['nama_ruangan'] ?? 'Nama tidak tersedia';
                                                        $kapasitas = $ruangan['kapasitas'] ?? '0';
                                                        echo htmlspecialchars($kode_ruangan . ' - ' . $nama_ruangan . ' (Kap: ' . $kapasitas . ')');
                                                        ?>
                                                    </option>
                                            <?php endif;
                                            endforeach; ?>
                                        <?php else: ?>
                                            <option value="">Tidak ada ruangan tersedia</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="hari">Hari <span class="text-danger">*</span></label>
                                    <select class="form-control" id="hari" name="hari" required>
                                        <option value="">Pilih Hari</option>
                                        <?php foreach ($daftarHari as $hari): ?>
                                            <option value="<?php echo $hari; ?>"
                                                <?php echo ($editData && $editData['hari'] == $hari) ? 'selected' : ''; ?>>
                                                <?php echo $hari; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
                                            <select name="jam_mulai" class="form-control">
                                                <?php foreach ($jamMulai as $jm): ?>
                                                    <option value="<?= $jm ?>"><?= substr($jm, 0, 5) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>
                                            <select name="jam_selesai" class="form-control">
                                                <?php foreach ($jamSelesai as $js): ?>
                                                    <option value="<?= $js ?>"><?= substr($js, 0, 5) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="kelas">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-control" id="kelas" name="kelas" required>
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($daftarKelas as $kelas): ?>
                                            <option value="<?php echo $kelas; ?>"
                                                <?php echo ($editData && $editData['kelas'] == $kelas) ? 'selected' : ''; ?>>
                                                Kelas <?php echo $kelas; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="active" <?php echo ($editData && $editData['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="canceled" <?php echo ($editData && $editData['status'] == 'canceled') ? 'selected' : ''; ?>>Dibatalkan</option>
                                        <option value="completed" <?php echo ($editData && $editData['status'] == 'completed') ? 'selected' : ''; ?>>Selesai</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn <?php echo $editData ? 'btn-warning' : 'btn-primary'; ?> btn-block">
                                        <i class="fas fa-<?php echo $editData ? 'save' : 'plus'; ?> mr-1"></i>
                                        <?php echo $editData ? 'Update Jadwal' : 'Tambah Jadwal'; ?>
                                    </button>

                                    <?php if ($editData): ?>
                                        <a href="jadwal.php" class="btn btn-secondary btn-block">
                                            <i class="fas fa-times mr-1"></i> Batal Edit
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <?php if ($filterHari): ?>
                    <div class="alert alert-info">
                        Menampilkan jadwal untuk hari: <strong><?php echo htmlspecialchars($filterHari); ?></strong>
                        <a href="jadwal.php" class="btn btn-sm btn-secondary ml-2">Tampilkan Semua</a>
                    </div>
                <?php endif; ?>





                <div class="col-md-8">
                    <!-- Tabel Jadwal -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Jadwal Kuliah</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                   <!-- <input type="text" id="searchInput" class="form-control float-right" placeholder="Cari jadwal...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>-->
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table id="jadwalTable"     class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Ruangan</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($jadwalData)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Belum ada data</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($jadwalData as $i => $jadwal): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= htmlspecialchars($jadwal['hari']) ?></td>
                                                <td><?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?></td>
                                                <td><?= htmlspecialchars($jadwal['kode_mk']) . ' - ' . htmlspecialchars($jadwal['nama_mk']) ?></td>
                                                <td><?= htmlspecialchars($jadwal['nama_dosen']) ?></td>
                                                <td><?= htmlspecialchars($jadwal['kode_ruangan']) ?></td>
                                                <td><?= htmlspecialchars($jadwal['kelas']) ?></td>
                                                <td>
                                                    <?php if ($jadwal['status'] == 'active'): ?>
                                                        <button class="btn btn-sm btn-success btn-status" data-id="<?= $jadwal['id'] ?>" data-status="completed">
                                                            KELAS SUDAH MULAI
                                                        </button>
                                                    <?php elseif ($jadwal['status'] == 'completed'): ?>
                                                        <span class="badge badge-secondary">Selesai</span>
                                                    <?php elseif ($jadwal['status'] == 'canceled'): ?>
                                                        <span class="badge badge-danger">Batal</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <!-- Tombol Edit -->
                                                    <a href="jadwal.php?edit=<?php echo $jadwal['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Tombol Hapus -->
                                                    <form method="POST" action="jadwal.php" style="display:inline-block;"
                                                        onsubmit="return confirmDelete('<?php echo htmlspecialchars($jadwal['nama_mk']); ?>')">
                                                        <input type="hidden" name="hapus" value="<?php echo $jadwal['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Tombol View -->
                                                    <button type="button" class="btn btn-sm btn-info" title="Lihat Detail"
                                                        data-toggle="modal" data-target="#viewJadwal<?php echo $jadwal['id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>

                            </table>


                            <!-- Script AJAX -->
                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                            <script>
                                $(document).on("click", ".toggle-status", function() {
                                    let id = $(this).data("id");
                                    let status = $(this).data("status");

                                    $.post("controllers/JadwalController.php", {
                                        action: "updateStatus",
                                        id: id,
                                        status: status
                                    }, function(response) {
                                        let res = JSON.parse(response);
                                        if (res.success) {
                                            alert(res.message);
                                            location.reload();
                                        } else {
                                            alert(res.errors);
                                        }
                                    });
                                });
                            </script>


                        </div>
                        <div class="card-footer">
                            <?php foreach ($jadwalData as $jadwal): ?>
                                <div class="modal fade" id="viewJadwal<?php echo $jadwal['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="viewJadwalLabel<?php echo $jadwal['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title" id="viewJadwalLabel<?php echo $jadwal['id']; ?>">Detail Jadwal Kuliah</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="list-group">
                                                    <li class="list-group-item"><strong>Hari:</strong> <?php echo htmlspecialchars($jadwal['hari']); ?></li>
                                                    <li class="list-group-item"><strong>Jam:</strong> <?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?></li>
                                                    <li class="list-group-item"><strong>Mata Kuliah:</strong> <?php echo htmlspecialchars($jadwal['kode_mk']) . ' - ' . htmlspecialchars($jadwal['nama_mk']); ?></li>
                                                    <li class="list-group-item"><strong>Dosen:</strong> <?php echo htmlspecialchars($jadwal['nama_dosen']); ?></li>
                                                    <li class="list-group-item"><strong>Ruangan:</strong> <?php echo htmlspecialchars($jadwal['kode_ruangan']); ?></li>
                                                    <li class="list-group-item"><strong>Kelas:</strong> <?php echo htmlspecialchars($jadwal['kelas']); ?></li>
                                                    <li class="list-group-item"><strong>Status:</strong>
                                                        <?php if (($jadwal['status'] ?? '') == 'active'): ?>
                                                            <span class="badge badge-success">Aktif</span>
                                                        <?php elseif (($jadwal['status'] ?? '') == 'canceled'): ?>
                                                            <span class="badge badge-danger">Batal</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Selesai</span>
                                                        <?php endif; ?>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            Total: <?= count($jadwalData) ?> Jadwal



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (kalau belum ada) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Select2 Bootstrap 5 Theme -->
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />




<script>
    // Validasi jam
    document.getElementById('jadwalForm').addEventListener('submit', function(e) {
        const start = document.getElementById('jam_mulai').value;
        const end = document.getElementById('jam_selesai').value;
        if (start && end && start >= end) {
            e.preventDefault();
            alert("Jam selesai harus lebih besar dari jam mulai!");
        }
    });
</script>

<script>
    $(document).on("click", ".btn-status", function() {
        let id = $(this).data("id");
        let status = $(this).data("status");

        $.ajax({
            url: "../../controllers/JadwalController.php",
            type: "POST",
            data: {
                action: "updateStatus",
                id: id,
                status: status
            },
            success: function(res) {
                let result = JSON.parse(res);
                if (result.success) {
                    alert(result.message);
                    location.reload(); // refresh halaman supaya update terlihat
                } else {
                    alert("Gagal: " + result.errors);
                }
            }
        });
    });
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>



<script>
    $(document).ready(function() {
        // Semua select yang punya class select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: function() {
                return $(this).data('placeholder');
            },
            allowClear: true,
            width: '100%'
        });
    });
</script>

<!-- CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#mata_kuliah_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Cari atau pilih mata kuliah",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script>
    setTimeout(function() {
        $(".alert-success").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);

    setTimeout(function() {
        $(".alert-danger").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);
</script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: function() {
                return $(this).data('placeholder');
            },
            allowClear: true,
            width: '100%'
        }).on('select2:open', function() {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            }, 100);
        });
    });
</script>

<script>
document.getElementById("searchInput").addEventListener("keyup", function () {
    let input = this.value.toLowerCase();
    let rows = document.querySelectorAll("#jadwalTable tbody tr");

    rows.forEach(function(row) {
        let text = row.innerText.toLowerCase();
        if (text.indexOf(input) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>