<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/MahasiswaModel.php';
require_once __DIR__ . '/../../controllers/MahasiswaController.php';

checkAuth();
checkRole(['staff_lab', 'admin']);

$database = new Database();
$db = $database->getConnection();
$mahasiswaController = new MahasiswaController($db);

$page_title = "Manajemen Mahasiswa";
$error = '';
$success = '';

// --- PRG: cek hasil redirect ---
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $success = $_GET['message'] ?? 'Berhasil memproses data';
    } elseif ($_GET['status'] === 'error') {
        $error = $_GET['message'] ?? 'Terjadi kesalahan';
    }
}

// --- Handle form actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_mahasiswa'])) {
        $result = $mahasiswaController->createMahasiswa($_POST);
    } elseif (isset($_POST['edit_mahasiswa'])) {
        $id = $_POST['id'];
        $result = $mahasiswaController->updateMahasiswa($id, $_POST);
    }

    // Redirect agar tidak double insert saat refresh
    if ($result['success']) {
        header("Location: mahasiswa.php?status=success&message=" . urlencode($result['message']));
    } else {
        header("Location: mahasiswa.php?status=error&message=" . urlencode(implode(', ', $result['errors'])));
    }
    exit;
}

// --- Handle delete ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $result = $mahasiswaController->deleteMahasiswa($id);

    if ($result['success']) {
        header("Location: mahasiswa.php?status=success&message=" . urlencode($result['message']));
    } else {
        header("Location: mahasiswa.php?status=error&message=" . urlencode(implode(', ', $result['errors'])));
    }
    exit;
}

// --- Get data untuk tampilan ---
$mahasiswaData = $mahasiswaController->getAllMahasiswa();
$editData = isset($_GET['edit']) ? $mahasiswaController->getMahasiswaById($_GET['edit']) : null;
$praktikumList = $mahasiswaController->getAllPraktikum();
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Mahasiswa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mahasiswa</li>
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
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="icon fas fa-ban"></i> <?= $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <i class="icon fas fa-check"></i> <?= $success; ?>
                </div>
            <?php endif; ?>

            <!-- Form Mahasiswa (full width, di atas tabel) -->
            <div class="card <?= $editData ? 'card-warning' : 'card-primary'; ?> mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-<?= $editData ? 'edit' : 'plus'; ?> mr-1"></i>
                        <?= $editData ? 'Edit Data Mahasiswa' : 'Tambah Mahasiswa Baru'; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($editData): ?>
                            <input type="hidden" name="id" value="<?= $editData['id']; ?>">
                            <input type="hidden" name="edit_mahasiswa" value="1">
                        <?php else: ?>
                            <input type="hidden" name="tambah_mahasiswa" value="1">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nim">NIM *</label>
                                <input type="text" class="form-control" id="nim" name="nim"
                                       value="<?= $editData ? htmlspecialchars($editData['nim']) : ''; ?>"
                                       placeholder="Contoh: 2100000001" required maxlength="15">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nama">Nama *</label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                       value="<?= $editData ? htmlspecialchars($editData['nama']) : ''; ?>"
                                       placeholder="Nama lengkap mahasiswa" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="kelas">Kelas *</label>
                                <select class="form-control" id="kelas" name="kelas" required>
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach (range('A','H') as $kelas): ?>
                                        <option value="<?= $kelas; ?>" <?= ($editData && $editData['kelas'] === $kelas) ? 'selected' : ''; ?>>
                                            <?= $kelas; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= $editData ? htmlspecialchars($editData['email']) : ''; ?>"
                                       placeholder="Email mahasiswa" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="praktikum_id">Praktikum *</label>
                                <select class="form-control" id="praktikum_id" name="praktikum_id" required>
                                    <option value="">Pilih Praktikum</option>
                                    <?php foreach ($praktikumList as $praktikum): ?>
                                        <option value="<?= $praktikum['id']; ?>"
                                            <?= ($editData && $editData['praktikum_id'] == $praktikum['id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($praktikum['kode_mk'] . ' - ' . $praktikum['nama_mk'] . ' (' . $praktikum['nama_praktikum'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="semester">Semester *</label>
                                <select class="form-control" id="semester" name="semester" required>
                                    <option value="">Pilih Semester</option>
                                    <option value="Gasal" <?= ($editData && $editData['semester'] == 'Gasal') ? 'selected' : ''; ?>>Gasal</option>
                                    <option value="Genap" <?= ($editData && $editData['semester'] == 'Genap') ? 'selected' : ''; ?>>Genap</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tahun_akademik">Tahun Akademik *</label>
                                <select class="form-control" id="tahun_akademik" name="tahun_akademik" required>
                                    <option value="">Pilih Tahun Akademik</option>
                                    <?php
                                        $currentYear = date("Y");
                                        for ($i = -1; $i <= 4; $i++) {
                                            $start = $currentYear + $i;
                                            $end   = $start + 1;
                                            $tahunAkademik = $start . "/" . $end;
                                            $selected = ($editData && $editData['tahun_akademik'] == $tahunAkademik) ? 'selected' : '';
                                            echo "<option value='$tahunAkademik' $selected>$tahunAkademik</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prodi">Program Studi *</label>
                                <select class="form-control" id="prodi" name="prodi" required>
                                    <option value="">Pilih Prodi</option>
                                    <option value="Teknik Informatika" <?= ($editData && $editData['prodi'] == 'Teknik Informatika') ? 'selected' : ''; ?>>Teknik Informatika</option>
                                    <option value="Sistem Informasi" <?= ($editData && $editData['prodi'] == 'Sistem Informasi') ? 'selected' : ''; ?>>Sistem Informasi</option>
                                    <option value="Teknik Komputer" <?= ($editData && $editData['prodi'] == 'Teknik Komputer') ? 'selected' : ''; ?>>Teknik Komputer</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn <?= $editData ? 'btn-warning' : 'btn-primary'; ?>">
                                <i class="fas fa-<?= $editData ? 'save' : 'plus'; ?> mr-1"></i>
                                <?= $editData ? 'Update Data' : 'Tambah Mahasiswa'; ?>
                            </button>
                            <?php if ($editData): ?>
                                <a href="mahasiswa.php" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Batal Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Mahasiswa -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-1"></i> Daftar Mahasiswa</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="mahasiswaTable"
                               class="table table-striped table-bordered table-hover align-middle nowrap"
                               style="width:100%">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Email</th>
                                    <th>Praktikum</th>
                                    <th>Semester</th>
                                    <th>Tahun Akademik</th>
                                    <th>Prodi</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mahasiswaData)): ?>
                                    <tr>
                                        <td colspan="13" class="text-center text-muted">Belum ada data mahasiswa</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mahasiswaData as $index => $mhs): ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><strong><?= htmlspecialchars($mhs['nim']); ?></strong></td>
                                            <td><?= htmlspecialchars($mhs['nama']); ?></td>
                                            <td><?= htmlspecialchars($mhs['kelas']); ?></td>
                                            <td><?= htmlspecialchars($mhs['email']); ?></td>
                                            <td>
                                                <small><?= htmlspecialchars($mhs['kode_mk']); ?></small><br>
                                                <strong><?= htmlspecialchars($mhs['nama_praktikum']); ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($mhs['semester']); ?></td>
                                            <td><?= htmlspecialchars($mhs['tahun_akademik']); ?></td>
                                            <td><?= htmlspecialchars($mhs['prodi']); ?></td>
                                            <td><?= htmlspecialchars($mhs['created_by'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($mhs['created_at'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($mhs['updated_at'] ?? '-'); ?></td>
                                            <td class="text-center">
                                                <a href="mahasiswa.php?edit=<?= $mhs['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="mahasiswa.php?hapus=<?= $mhs['id']; ?>" class="btn btn-sm btn-danger"
                                                   onclick="return confirmDelete('<?= htmlspecialchars($mhs['nama']); ?>')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-left">
                        <strong>Total: <?= count($mahasiswaData); ?> Mahasiswa</strong>
                    </div>
                    <div class="float-right">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                            <i class="fas fa-download mr-1"></i> Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    $('#mahasiswaTable').DataTable({
        responsive: true,
        fixedHeader: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya"
            }
        }
    });
});

function exportToExcel() {
    let table = document.querySelector('#mahasiswaTable');
    let html = table.outerHTML;
    let url = 'data:application/vnd.ms-excel,' + escape(html);
    let link = document.createElement('a');
    link.href = url;
    link.download = 'data_mahasiswa.xls';
    link.click();
}

function confirmDelete(nama) {
    return confirm('PERINGATAN: Data mahasiswa akan dihapus secara PERMANEN!\n\nYakin hapus mahasiswa ' + nama + '?');
}
</script>
