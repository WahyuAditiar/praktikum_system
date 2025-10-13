<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/AsistenPraktikumModel.php';
require_once __DIR__ . '/../../controllers/AsistenPraktikumController.php';
require_once __DIR__ . '/../../models/PraktikumModel.php';

checkAuth();
checkRole(['staff_lab', 'admin']);

$database = new Database();
$db = $database->getConnection();
$asistenController = new AsistenPraktikumController($db);

$praktikumModel = new PraktikumModel();
$page_title = "Manajemen Asisten Praktikum";
$error = '';
$success = '';

// Handle form actions
if ($_POST) {
    if (isset($_POST['tambah_asisten'])) {
        $result = $asistenController->createAsisten($_POST);
        if ($result['success']) {
            $success = $result['message'];
            echo '<script>window.location.href = "asisten_praktikum.php?status=success&message=' . urlencode($result['message']) . '";</script>';
            exit;
        } else {
            $error = is_array($result['errors']) ? implode('<br>', $result['errors']) : $result['message'];
        }
    } elseif (isset($_POST['edit_asisten'])) {
        $id = $_POST['id'];
        $result = $asistenController->updateAsisten($id, $_POST);
        if ($result['success']) {
            $success = $result['message'];
            echo '<script>window.location.href = "asisten_praktikum.php?status=success&message=' . urlencode($result['message']) . '";</script>';
            exit;
        } else {
            $error = is_array($result['errors']) ? implode('<br>', $result['errors']) : $result['message'];
        }
    }
}

// Handle delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $result = $asistenController->deleteAsisten($id);
    if ($result['success']) {
        $success = $result['message'];
        echo '<script>window.location.href = "asisten_praktikum.php?status=success&message=' . urlencode($result['message']) . '";</script>';
        exit;
    } else {
        $error = is_array($result['errors']) ? implode('<br>', $result['errors']) : $result['message'];
    }
}

// Handle status from redirect
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $success = $_GET['message'] ?? 'Operasi berhasil';
    } elseif ($_GET['status'] === 'error') {
        $error = $_GET['message'] ?? 'Terjadi kesalahan';
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

// Get praktikum list for dropdown (all tahun ajaran)
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

                                <!-- TAHUN AJARAN -->
                                <div class="form-group">
                                    <label for="tahun_ajaran">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran"
                                        value="<?php echo $editData ? htmlspecialchars($editData['tahun_ajaran']) : ''; ?>"
                                        placeholder="Contoh: 2023/2024" required>
                                </div>

                                <!-- PRAKTIKUM -->
                                <div class="form-group">
                                    <label for="praktikum_id">Praktikum <span class="text-danger">*</span></label>
                                    <select id="praktikum_id" name="praktikum_id" class="form-control select2" required>
                                        <option value="">-- Pilih Praktikum --</option>
                                        <?php if (!empty($praktikumList)): ?>
                                            <?php foreach ($praktikumList as $p): 
                                                // Data dari model
                                                $id_praktikum   = $p['praktikum_id'] ?? $p['id'] ?? '';
                                                $nama_praktikum = $p['nama_praktikum'] ?? 'Tidak diketahui';
                                                $kelas          = $p['kelas'] ?? 'A';
                                                $tahun_ajaran   = $p['tahun_ajaran'] ?? '-';

                                                // Tandai jika sedang mode edit
                                                $selected = (!empty($editData) && isset($editData['praktikum_id']) && $editData['praktikum_id'] == $id_praktikum) ? 'selected' : '';
                                            ?>
                                                <option 
                                                    value="<?= htmlspecialchars($id_praktikum) ?>"
                                                    data-nama-praktikum="<?= htmlspecialchars($nama_praktikum) ?>"
                                                    data-kelas="<?= htmlspecialchars($kelas) ?>"
                                                    data-tahun-ajaran="<?= htmlspecialchars($tahun_ajaran) ?>"
                                                    <?= $selected ?>
                                                >
                                                    <?= htmlspecialchars($nama_praktikum) ?> — <?= htmlspecialchars($tahun_ajaran) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">❌ Tidak ada data praktikum</option>
                                        <?php endif; ?>
                                    </select>

                                    <!-- Hidden input untuk nama_praktikum -->
                                    <input type="hidden" id="nama_praktikum" name="nama_praktikum"
                                        value="<?php echo $editData ? htmlspecialchars($editData['nama_praktikum']) : ''; ?>">
                                </div>

                               <!-- KELAS - AKAN TERISI OTOMATIS -->
<div class="form-group">
    <label for="kelas">Kelas <span class="text-danger">*</span></label>
    <input type="text" 
           class="form-control" 
           id="kelas" 
           name="kelas"
           value="<?php echo $editData ? htmlspecialchars($editData['kelas']) : ''; ?>"
           placeholder="Kelas akan terisi otomatis"
           required>
    <small class="form-text text-muted">
        Pilih praktikum terlebih dahulu, kelas akan terisi otomatis.
    </small>
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
                        
                        <!-- DEBUG BUTTON -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6">
                                    <button type="button" class="btn btn-sm btn-outline-info btn-block" onclick="testAutoFill()">
                                        <i class="fas fa-bug mr-1"></i> Test Auto Fill
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-block" onclick="console.log(document.getElementById('praktikum_id').options)">
                                        <i class="fas fa-code mr-1"></i> Console Log
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">Debug tools - bisa dihapus setelah fix</small>
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
                                        <th>Tahun Ajaran</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($asistenData)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">Belum ada data asisten praktikum</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($asistenData as $index => $asisten): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['nim']); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['nama']); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['kode_mk']); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['nama_praktikum']); ?></strong></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['kelas']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($asisten['semester']); ?></td>
                                                <td><strong><?php echo htmlspecialchars($asisten['tahun_ajaran']); ?></strong></td>
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

<!-- Include Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        height: calc(2.25rem + 2px) !important;
    }
    
    .select2-container--default .select2-selection--single:focus {
        border-color: #80bdff !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
</style>

<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Element references
    const praktikumSelect = document.getElementById('praktikum_id');
    const namaPraktikumInput = document.getElementById('nama_praktikum');
    const kelasInput = document.getElementById('kelas');
    const tahunAjaranInput = document.getElementById('tahun_ajaran');

    // Simple notification function
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        $('.auto-notification').remove();
        
        const alertClass = {
            'success': 'alert-success',
            'warning': 'alert-warning', 
            'info': 'alert-info',
            'danger': 'alert-danger'
        }[type] || 'alert-info';

        const notification = $(`
            <div class="alert ${alertClass} auto-notification alert-dismissible" 
                 style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-info-circle mr-2"></i>${message}
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            notification.alert('close');
        }, 4000);
    }

    // ✅ FIXED: Auto-fill function - SIMPLE & WORKING
    function autoFillPraktikumData() {
        const selectedOption = praktikumSelect.options[praktikumSelect.selectedIndex];
        
        console.log('🔄 Auto-fill triggered');
        console.log('Selected option:', selectedOption);

        if (!selectedOption || selectedOption.value === '') {
            // Reset if no selection
            namaPraktikumInput.value = '';
            kelasInput.value = '';
            return;
        }

        // Get data from data attributes - FIXED METHOD
        const namaPraktikum = selectedOption.getAttribute('data-nama-praktikum') || '';
        const kelas = selectedOption.getAttribute('data-kelas') || 'A';
        const tahunAjaran = selectedOption.getAttribute('data-tahun-ajaran') || '';

        console.log('📊 Data from option:', { namaPraktikum, kelas, tahunAjaran });

        // Fill the fields
        namaPraktikumInput.value = namaPraktikum;
        kelasInput.value = kelas;
        tahunAjaranInput.value = tahunAjaran;

        // Show success message
        showNotification(
            `✅ ${namaPraktikum} - Kelas ${kelas} - ${tahunAjaran}`,
            'success'
        );
    }

    // ✅ FIXED: Event listeners - MULTIPLE METHODS
    // Method 1: Direct event listener
    praktikumSelect.addEventListener('change', autoFillPraktikumData);
    
    // Method 2: jQuery event listener for Select2
    $(praktikumSelect).on('change', autoFillPraktikumData);
    
    // Method 3: Also listen for Select2 change
    $(praktikumSelect).on('select2:select', autoFillPraktikumData);

    // Suggest kelas function
    window.suggestKelas = function() {
        if (!praktikumSelect.value) {
            showNotification('Pilih praktikum terlebih dahulu!', 'warning');
            return;
        }

        const currentKelas = kelasInput.value;
        const kelasOptions = ['A', 'B', 'C', 'D', 'E', 'F'];
        
        if (!currentKelas) {
            kelasInput.value = 'A';
        } else {
            const currentIndex = kelasOptions.indexOf(currentKelas);
            const nextIndex = (currentIndex + 1) % kelasOptions.length;
            kelasInput.value = kelasOptions[nextIndex];
        }
        
        showNotification(`Kelas ${kelasInput.value} disarankan`, 'info');
    };

    // ✅ FIXED: Edit mode auto-fill
    <?php if (!empty($editData)): ?>
    setTimeout(() => {
        console.log('📝 Edit mode - Auto filling data');
        autoFillPraktikumData();
    }, 500);
    <?php endif; ?>

    // Form validation
    $('#asistenForm').on('submit', function(e) {
        if (!praktikumSelect.value) {
            e.preventDefault();
            showNotification('Pilih praktikum terlebih dahulu!', 'danger');
            return;
        }
        
        if (!kelasInput.value.trim()) {
            e.preventDefault();
            showNotification('Kelas harus diisi!', 'danger');
            return;
        }

        showNotification('Menyimpan data asisten...', 'info');
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // ✅ DEBUG: Test function
    window.testAutoFill = function() {
        console.log('🧪 Testing auto-fill function');
        console.log('Available options:', praktikumSelect.options.length);
        
        if (praktikumSelect.options.length > 1) {
            praktikumSelect.selectedIndex = 1;
            autoFillPraktikumData();
        } else {
            showNotification('Tidak ada opsi praktikum untuk testing', 'warning');
        }
    }

    // Initial debug info
    console.log('🚀 JavaScript loaded successfully');
    console.log('Praktikum options count:', praktikumSelect.options.length);
    console.log('Current praktikum value:', praktikumSelect.value);
});

// Global functions
function confirmDelete(nama) {
    return confirm(`Apakah Anda yakin ingin menghapus asisten:\n${nama}?`);
}

function exportToExcel() {
    alert('Fitur export Excel akan segera hadir!');
}
</script>