<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/AsistenPraktikumModel.php';
require_once __DIR__ . '/../../controllers/AsistenPraktikumController.php';

checkAuth();
checkRole(['staff_lab', 'admin']);


$database = new Database();
$db = $database->getConnection();
$asistenController = new AsistenPraktikumController($db);

$page_title = "Manajemen Asisten Praktikum";
$error = '';
$success = '';

// Handle form actions
if ($_POST) {
    if (isset($_POST['tambah_asisten'])) {
        $result = $asistenController->createAsisten($_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = implode('<br>', $result['errors']);
        }
    } elseif (isset($_POST['edit_asisten'])) {
        $id = $_POST['id'];
        $result = $asistenController->updateAsisten($id, $_POST);
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
    $result = $asistenController->deleteAsisten($id);
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = implode('<br>', $result['errors']);
    }
}

// Get all asisten data
$asistenData = $asistenController->getAllAsisten();
$statistics = $asistenController->getStatistics();

// Get data for edit
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $asistenController->getAsistenById($_GET['edit']);
}

// Get praktikum list for dropdown
$praktikumList = $asistenController->getAllPraktikum();
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Asisten Praktikum</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Asisten Praktikum</li>
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
                ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo array_sum($statsMap); ?></h3>
                            <p>Total Asisten</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo isset($statsMap['active']) ? $statsMap['active'] : 0; ?></h3>
                            <p>Asisten Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo isset($statsMap['inactive']) ? $statsMap['inactive'] : 0; ?></h3>
                            <p>Asisten Non-Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?php echo count($praktikumList); ?></h3>
                            <p>Total Praktikum</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <!-- Form Asisten Praktikum -->
                    <div class="card <?php echo $editData ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-<?php echo $editData ? 'edit' : 'plus'; ?> mr-1"></i>
                                <?php echo $editData ? 'Edit Data Asisten' : 'Tambah Asisten Baru'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="asistenForm">
                                <?php if ($editData): ?>
                                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                                    <input type="hidden" name="edit_asisten" value="1">
                                <?php else: ?>
                                    <input type="hidden" name="tambah_asisten" value="1">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="nim">NIM <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nim" name="nim"
                                        value="<?php echo $editData ? htmlspecialchars($editData['nim']) : ''; ?>"
                                        placeholder="Contoh: 2100000001" required maxlength="15">
                                    <small class="form-text text-muted">Nomor Induk Mahasiswa</small>
                                </div>

                                <div class="form-group">
                                    <label for="nama">Nama Asisten <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="<?php echo $editData ? htmlspecialchars($editData['nama']) : ''; ?>"
                                        placeholder="Nama lengkap asisten" required>
                                </div>

                                <div class="form-group">
                                    <label for="praktikum_id">Praktikum <span class="text-danger">*</span></label>
                                    <select class="form-control" id="praktikum_id" name="praktikum_id" required>
                                        <option value="">Pilih Praktikum</option>
                                        <?php foreach ($praktikumList as $praktikum): ?>
                                            <option value="<?php echo $praktikum['id']; ?>"
                                                data-nama="<?php echo htmlspecialchars($praktikum['nama_praktikum']); ?>"
                                                data-tahun="<?php echo htmlspecialchars($praktikum['tahun_ajaran']); ?>"
                                                data-mk="<?php echo htmlspecialchars($praktikum['nama_mk']); ?>">
                                                <?php echo htmlspecialchars($praktikum['kode_mk'] . ' - ' . $praktikum['nama_mk'] . ' (' . $praktikum['tahun_ajaran'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="nama_praktikum">Nama Praktikum</label>
                                    <input type="text" class="form-control" id="nama_praktikum" name="nama_praktikum" readonly>
                                </div>


                                <div class="form-group">
                                    <label for="kelas">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-control" id="kelas" name="kelas" required>
                                        <option value="">Kelas</option>
                                        <option value="A" <?php echo ($editData && $editData['kelas'] == 'A') ? 'selected' : ''; ?>>A</option>
                                        <option value="B" <?php echo ($editData && $editData['kelas'] == 'B') ? 'selected' : ''; ?>>B</option>
                                        <option value="C" <?php echo ($editData && $editData['kelas'] == 'C') ? 'selected' : ''; ?>>C</option>
                                        <option value="D" <?php echo ($editData && $editData['kelas'] == 'D') ? 'selected' : ''; ?>>D</option>
                                        <option value="E" <?php echo ($editData && $editData['kelas'] == 'E') ? 'selected' : ''; ?>>E</option>
                                        <option value="F" <?php echo ($editData && $editData['kelas'] == 'F') ? 'selected' : ''; ?>>F</option>
                                        <option value="G" <?php echo ($editData && $editData['kelas'] == 'G') ? 'selected' : ''; ?>>G</option>
                                        <option value="H" <?php echo ($editData && $editData['kelas'] == 'H') ? 'selected' : ''; ?>>H</option>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label for="semester">Semester <span class="text-danger">*</span></label>
                                    <select class="form-control" id="semester" name="semester" required>
                                        <option value="">Pilih Semester</option>
                                        <option value="Ganjil" <?php echo ($editData && $editData['semester'] == 'Ganjil') ? 'selected' : ''; ?>>Ganjil</option>
                                        <option value="Genap" <?php echo ($editData && $editData['semester'] == 'Genap') ? 'selected' : ''; ?>>Genap</option>
                                        <option value="Pendek" <?php echo ($editData && $editData['semester'] == 'Pendek') ? 'selected' : ''; ?>>Pendek</option>
                                    </select>
                                </div>

                                <!-- TAMBAH FIELD TAHUN AJARAN (MANUAL INPUT) -->
                                <div class="form-group">
                                    <label for="tahun_ajaran">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran"
                                        value="<?php echo $editData ? htmlspecialchars($editData['tahun_ajaran']) : ''; ?>"
                                        placeholder="Contoh: 2023/2024" required>
                                    <small class="form-text text-muted">Format: Tahun/Tahun (contoh: 2023/2024)</small>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status Asisten <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="active" <?php echo ($editData && $editData['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="inactive" <?php echo ($editData && $editData['status'] == 'inactive') ? 'selected' : ''; ?>>Non-Aktif</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn <?php echo $editData ? 'btn-warning' : 'btn-primary'; ?> btn-block">
                                        <i class="fas fa-<?php echo $editData ? 'save' : 'plus'; ?> mr-1"></i>
                                        <?php echo $editData ? 'Update Data' : 'Tambah Asisten'; ?>
                                    </button>

                                    <?php if ($editData): ?>
                                        <a href="asisten_praktikum.php" class="btn btn-secondary btn-block">
                                            <i class="fas fa-times mr-1"></i> Batal Edit
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Tabel Asisten Praktikum -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Daftar Asisten Praktikum
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="searchInput" class="form-control float-right" placeholder="Cari asisten...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>NIM</th>
                                        <th>Nama Asisten</th>
                                        <th>Kode MK</th>
                                        <th>Praktikum</th>
                                        <th>Kelas</th>
                                        <th>Semester</th>
                                        <th>Tahun Ajaran</th> <!-- KOLOM BARU -->
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($asistenData)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Belum ada data asisten praktikum</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($asistenData as $index => $asisten): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['nim']); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['nama']); ?></strong></td>
                                                <td> <strong><?php echo htmlspecialchars($asisten['kode_mk']); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['nama_praktikum']); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['kelas']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($asisten['semester']); ?></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['tahun_ajaran']); ?></strong></td> <!-- DATA BARU -->
                                                <td>
                                                    <?php if ($asisten['status'] == 'active'): ?>
                                                        <span class="badge badge-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Non-Aktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="asisten_praktikum.php?edit=<?php echo $asisten['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="asisten_praktikum.php?hapus=<?php echo $asisten['id']; ?>" class="btn btn-sm btn-danger"
                                                        title="Hapus" onclick="return confirmDelete('<?php echo htmlspecialchars($asisten['nama']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="float-left">
                                <strong>Total: <?php echo count($asistenData); ?> Asisten</strong>
                            </div>
                            <div class="float-right">
                                <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                                    <i class="fas fa-download mr-1"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Form validation
    document.getElementById('asistenForm').addEventListener('submit', function(e) {
        const nim = document.getElementById('nim').value;

        // Validate NIM (only numbers)
        if (!/^\d+$/.test(nim)) {
            e.preventDefault();
            alert('NIM harus berupa angka saja');
            return false;
        }
    });

    function exportToExcel() {
        let table = document.querySelector('table');
        let html = table.outerHTML;
        let url = 'data:application/vnd.ms-excel,' + escape(html);
        let link = document.createElement('a');
        link.href = url;
        link.download = 'data_asisten_praktikum.xls';
        link.click();
    }

    // Auto-format NIM to numbers only
    document.getElementById('nim').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
    });

    function confirmDelete(nama) {
        return confirm('PERINGATAN: Data asisten akan dihapus secara PERMANEN dan tidak dapat dikembalikan!\n\nYakin hapus asisten ' + nama + '?');
    }
</script>

<script>
    document.getElementById('praktikum_id').addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        var namaPraktikum = selected.getAttribute('data-nama') || '';

        document.getElementById('nama_praktikum').value = namaPraktikum;

        // Opsional: Auto-fill tahun_ajaran berdasarkan pilihan praktikum, tapi tetap bisa diedit
        var tahunAjaran = selected.getAttribute('data-tahun') || '';
        if (tahunAjaran && !document.getElementById('tahun_ajaran').value) {
            document.getElementById('tahun_ajaran').value = tahunAjaran;
        }
    });

    // Validasi format tahun ajaran
    document.getElementById('asistenForm').addEventListener('submit', function(e) {
        const tahunAjaran = document.getElementById('tahun_ajaran').value;

        // Validasi format tahun ajaran (opsional)
        if (tahunAjaran && !/^\d{4}\/\d{4}$/.test(tahunAjaran)) {
            e.preventDefault();
            alert('Format tahun ajaran harus: Tahun/Tahun (contoh: 2023/2024)');
            document.getElementById('tahun_ajaran').focus();
            return false;
        }
    });
</script>