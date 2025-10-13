<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/DosenModel.php';
require_once __DIR__ . '/../../controllers/DosenController.php';

checkAuth();
checkRole(['staff_lab', 'admin']);

$database = new Database();
$db = $database->getConnection();
$dosenController = new DosenController($db);

$page_title = "Manajemen Dosen";
$error = '';
$success = '';

// Handle form actions
if ($_POST) {
    if (isset($_POST['tambah_dosen'])) {
        $result = $dosenController->createDosen($_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = implode('<br>', $result['errors']);
        }
    } elseif (isset($_POST['edit_dosen'])) {
        $id = $_POST['id'];
        $result = $dosenController->updateDosen($id, $_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = implode('<br>', $result['errors']);
        }
    }
}


// Cek role
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'staff_lab' && $_SESSION['role'] != 'admin')) {
    header("Location: ../../index.php");
    exit;
}

// Handle delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $result = $dosenController->deleteDosen($id);
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = implode('<br>', $result['errors']);
    }
}

// Get all dosen data
$dosenData = $dosenController->getAllDosen();
$statistics = $dosenController->getStatistics();

// Get data for edit
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $dosenController->getDosenById($_GET['edit']);
}


?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Dosen</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Dosen</li>
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
                            <p>Total Dosen</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                           <h3><?php echo isset($statsMap['tetap']) ? $statsMap['tetap'] : 0; ?></h3>

                            <p>Dosen Tetap</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                          <h3><?php echo isset($statsMap['tidak_tetap']) ? $statsMap['tidak_tetap'] : 0; ?></h3>
                            <p>Dosen Tidak Tetap</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                           <h3><?php echo isset($statsMap['inactive']) ? $statsMap['inactive'] : 0; ?></h3>
                            <p>Non-Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <!-- Form Dosen -->
                    <div class="card <?php echo $editData ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-<?php echo $editData ? 'edit' : 'plus'; ?> mr-1"></i>
                                <?php echo $editData ? 'Edit Data Dosen' : 'Tambah Dosen Baru'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="dosenForm">
                                <?php if ($editData): ?>
                                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                                    <input type="hidden" name="edit_dosen" value="1">
                                <?php else: ?>
                                    <input type="hidden" name="tambah_dosen" value="1">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="nidn">NIDN Dosen <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nidn" name="nidn" 
                                           value="<?php echo $editData ? htmlspecialchars($editData['nidn']) : ''; ?>" 
                                           placeholder="Masukkan 10 digit NIDN" required maxlength="10">
                                    <small class="form-text text-muted">Contoh: 0012345601</small>
                                </div>

                                <div class="form-group">
                                    <label for="nama">Nama Dosen <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?php echo $editData ? htmlspecialchars($editData['nama']) : ''; ?>" 
                                           placeholder="Masukkan nama lengkap" required>
                                </div>

                                <div class="form-group">
                                    <label for="hp">Nomor HP/Telepon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="hp" name="hp" 
                                           value="<?php echo $editData ? htmlspecialchars($editData['no_hp']) : ''; ?>" 
                                           placeholder="Contoh: 081234567890" required>
                                </div>

                                <div class="form-group">
    <label for="status">Status Dosen <span class="text-danger">*</span></label>
    <select class="form-control" id="status" name="status" required>
        <option value="">Pilih Status</option>
        <option value="tetap" <?php echo ($editData && $editData['status'] == 'tetap') ? 'selected' : ''; ?>>Dosen Tetap</option>
        <option value="tidak_tetap" <?php echo ($editData && $editData['status'] == 'tidak_tetap') ? 'selected' : ''; ?>>Dosen Tidak Tetap</option>
        <option value="inactive" <?php echo ($editData && $editData['status'] == 'inactive') ? 'selected' : ''; ?>>Non-Aktif</option>
    </select>
</div>

                                <div class="form-group">
                                    <button type="submit" class="btn <?php echo $editData ? 'btn-warning' : 'btn-primary'; ?> btn-block">
                                        <i class="fas fa-<?php echo $editData ? 'save' : 'plus'; ?> mr-1"></i>
                                        <?php echo $editData ? 'Update Data' : 'Tambah Dosen'; ?>
                                    </button>
                                    
                                    <?php if ($editData): ?>
                                        <a href="dosen.php" class="btn btn-secondary btn-block">
                                            <i class="fas fa-times mr-1"></i> Batal Edit
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Tabel Dosen -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Daftar Dosen
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="searchInput" class="form-control float-right" placeholder="Cari dosen...">
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
                                        <th>NIDN</th>
                                        <th>Nama Dosen</th>
                                        <th>HP/Telepon</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dosenData)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada data dosen</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dosenData as $index => $dosen): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($dosen['nidn']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($dosen['nama']); ?></td>
                                                <td><?php echo htmlspecialchars($dosen['no_hp']); ?></td>
                                                <td>
    <?php if ($dosen['status'] == 'tetap'): ?>
        <span class="badge badge-success">Tetap</span>
    <?php elseif ($dosen['status'] == 'tidak_tetap'): ?>
        <span class="badge badge-warning">Tidak Tetap</span>
    <?php else: ?>
        <span class="badge badge-secondary">Non-Aktif</span>
    <?php endif; ?>
</td>
                                                <td>
                                                    <a href="dosen.php?edit=<?php echo $dosen['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="dosen.php?hapus=<?php echo $dosen['id']; ?>" class="btn btn-sm btn-danger" 
                                                       title="Hapus" onclick="return confirm('Yakin hapus data dosen <?php echo htmlspecialchars($dosen['nama']); ?>?')">
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
                                <strong>Total: <?php echo count($dosenData); ?> Dosen</strong>
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
document.getElementById('dosenForm').addEventListener('submit', function(e) {
    const nidn = document.getElementById('nidn').value;
    const hp = document.getElementById('hp').value;
    
    // Validate NIDN (10 digits)
    if (!/^\d{10}$/.test(nidn)) {
        e.preventDefault();
        alert('NIDN harus terdiri dari 10 digit angka');
        return false;
    }
    
    // Validate phone number
    if (!/^\d{10,15}$/.test(hp.replace(/\D/g, ''))) {
        e.preventDefault();
        alert('Nomor HP harus terdiri dari 10-15 digit angka');
        return false;
    }
});

function exportToExcel() {
    // Simple Excel export (could be enhanced with proper library)
    let table = document.querySelector('table');
    let html = table.outerHTML;
    let url = 'data:application/vnd.ms-excel,' + escape(html);
    let link = document.createElement('a');
    link.href = url;
    link.download = 'data_dosen.xls';
    link.click();
}

// Auto-format NIDN input
document.getElementById('nidn').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
});

// Auto-format HP input
document.getElementById('hp').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '').slice(0, 15);
});

function confirmDelete(nama) {
    return confirm('PERINGATAN: Data akan dihapus secara PERMANEN dan tidak dapat dikembalikan!\n\nYakin hapus ' + nama + '?');
}

</script>