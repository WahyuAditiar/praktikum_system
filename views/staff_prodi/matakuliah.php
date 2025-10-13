<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/MataKuliahModel.php';
require_once __DIR__ . '/../../controllers/MataKuliahController.php';

checkAuth();
checkRole(['staff_prodi', 'admin']);

$database = new Database();
$db = $database->getConnection();
$mataKuliahController = new MataKuliahController($db);

$page_title = "Manajemen Mata Kuliah";
$error = '';
$success = '';

// Handle form actions
if ($_POST) {
    if (isset($_POST['tambah_matkul'])) {
        $result = $mataKuliahController->createMataKuliah($_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = implode('<br>', $result['errors']);
        }
    } elseif (isset($_POST['edit_matkul'])) {
        $id = $_POST['id'];
        $result = $mataKuliahController->updateMataKuliah($id, $_POST);
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
    $result = $mataKuliahController->deleteMataKuliah($id);
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = implode('<br>', $result['errors']);
    }
}




// Get all mata kuliah data
$mataKuliahData = $mataKuliahController->getAllMataKuliah();
$statistics = $mataKuliahController->getStatistics();
$semesterStats = $mataKuliahController->getStatisticsBySemester();
$totalSKS = $mataKuliahController->getTotalSKS();

// Get data for edit
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $mataKuliahController->getMataKuliahById($_GET['edit']);
}

// Daftar jurusan
$daftarJurusan = [
    'Teknik Informatika',
    'Sistem Informasi', 
    'Teknik Komputer',
    'Manajemen Informatika',
    'Teknologi Informasi'
];

// Daftar semester
$daftarSemester = [1, 2, 3, 4, 5, 6, 7, 8];
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Mata Kuliah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mata Kuliah</li>
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
                            <p>Total Mata Kuliah</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                           <h3><?php echo isset($statsMap['active']) ? $statsMap['active'] : 0; ?></h3>
                            <p>Mata Kuliah Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                           <h3><?php echo isset($totalSKS['total_sks']) ? $totalSKS['total_sks'] : 0; ?></h3>
                            <p>Total SKS</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                         <h3><?php echo isset($daftarSemester) ? count($daftarSemester) : 0; ?></h3>

                            <p>Semester Tersedia</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="alert alert-success">
        ✅ Data berhasil diupload!
    </div>
<?php endif; ?>


            <!-- Semester Statistics -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Distribusi Mata Kuliah per Semester
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($daftarSemester as $semester): 
                                    $count = 0;
                                    foreach ($semesterStats as $stat) {
                                        if ($stat['semester'] == $semester) {
                                            $count = $stat['total'];
                                            break;
                                        }
                                    }
                                ?>

                                
                                <div class="col-md-3 col-6 text-center mb-3">
                                    <div class="info-box bg-light">
                                        <span class="info-box-icon bg-primary"><strong><?php echo $semester; ?></strong></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Semester <?php echo $semester; ?></span>
                                            <span class="info-box-number"><?php echo $count; ?> Matkul</span>
                                        </div>
                                    </div>
                                </div>

                                
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<!-- Card Upload & Download -->
<div class="card mb-3 shadow-sm">
    <div class="card-body d-flex justify-content-center flex-wrap align-items-center">

        <!-- Upload Form -->
        <form action="upload_matakuliah.php" method="post" enctype="multipart/form-data" class="form-inline">
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="fileUpload" name="file" accept=".xlsx" required>
                    <label class="custom-file-label" for="fileUpload">Pilih file...</label>
                </div>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-info">
                        <i class="fas fa-upload mr-1"></i> Upload Data
                    </button>
                </div>
            </div>
        </form>

        <!-- Download Template -->
        <a href="template_matakuliah.php" class="btn btn-outline-success ml-3">
            <i class="fas fa-file-download mr-1"></i> Download Template
        </a>

    </div>
</div>







            <div class="row">
                <div class="col-md-4">
                    <!-- Form Mata Kuliah -->
                  
                    <div class="card <?php echo $editData ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-<?php echo $editData ? 'edit' : 'plus'; ?> mr-1"></i>
                                <?php echo $editData ? 'Edit Data Mata Kuliah' : 'Tambah Mata Kuliah Baru'; ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="matkulForm">
                                <?php if ($editData): ?>
                                    <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                                    <input type="hidden" name="edit_matkul" value="1">
                                <?php else: ?>
                                    <input type="hidden" name="tambah_matkul" value="1">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="kode_mk">Kode Mata Kuliah <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kode_mk" name="kode_mk" 
                                           value="<?php echo $editData ? htmlspecialchars($editData['kode_mk']) : ''; ?>" 
                                           placeholder="Contoh: TI101, SI201" required>
                                    <small class="form-text text-muted">Kode unik untuk mata kuliah</small>
                                </div>

                                <div class="form-group">
                                    <label for="nama_mk">Nama Mata Kuliah <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_mk" name="nama_mk" 
                                           value="<?php echo $editData ? htmlspecialchars($editData['nama_mk']) : ''; ?>" 
                                           placeholder="Contoh: Pemrograman Web" required>
                                </div>

                                <div class="form-group">
                                    <label for="sks">SKS <span class="text-danger">*</span></label>
                                    <select class="form-control" id="sks" name="sks" required>
                                        <option value="">Pilih SKS</option>
                                        <?php for ($i = 1; $i <= 6; $i++): ?>
                                            <option value="<?php echo $i; ?>" 
                                                <?php echo ($editData && $editData['sks'] == $i) ? 'selected' : ''; ?>>
                                                <?php echo $i; ?> SKS
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="semester">Semester <span class="text-danger">*</span></label>
                                    <select class="form-control" id="semester" name="semester" required>
                                        <option value="">Pilih Semester</option>
                                        <?php foreach ($daftarSemester as $semester): ?>
                                            <option value="<?php echo $semester; ?>" 
                                                <?php echo ($editData && $editData['semester'] == $semester) ? 'selected' : ''; ?>>
                                                Semester <?php echo $semester; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="jurusan">Jurusan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jurusan" name="jurusan" required>
                                        <option value="">Pilih Jurusan</option>
                                        <?php foreach ($daftarJurusan as $jurusan): ?>
                                            <option value="<?php echo $jurusan; ?>" 
                                                <?php echo ($editData && $editData['jurusan'] == $jurusan) ? 'selected' : ''; ?>>
                                                <?php echo $jurusan; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi Mata Kuliah</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" 
                                              placeholder="Deskripsi singkat tentang mata kuliah" 
                                              rows="3"><?php echo $editData ? htmlspecialchars($editData['deskripsi']) : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="active" <?php echo ($editData && $editData['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="inactive" <?php echo ($editData && $editData['status'] == 'inactive') ? 'selected' : ''; ?>>Non-Aktif</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn <?php echo $editData ? 'btn-warning' : 'btn-primary'; ?> btn-block">
                                        <i class="fas fa-<?php echo $editData ? 'save' : 'plus'; ?> mr-1"></i>
                                        <?php echo $editData ? 'Update Data' : 'Tambah Mata Kuliah'; ?>
                                    </button>
                                    
                                    <?php if ($editData): ?>
                                        <a href="matakuliah.php" class="btn btn-secondary btn-block">
                                            <i class="fas fa-times mr-1"></i> Batal Edit
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                
                <div class="col-md-8">
                    
                    <!-- Tabel Mata Kuliah -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Daftar Mata Kuliah
                            </h3>



                            
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="searchInput" class="form-control float-right" placeholder="Cari mata kuliah...">
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
                                        <th>Kode</th>
                                        <th>Mata Kuliah</th>
                                        <th>SKS</th>
                                        <th>Semester</th>
                                        <th>Jurusan</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($mataKuliahData)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">Belum ada data mata kuliah</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($mataKuliahData as $index => $matkul): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($matkul['kode_mk']); ?></strong></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($matkul['nama_mk']); ?></strong>
                                                    <?php if (!empty($matkul['deskripsi'])): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($matkul['deskripsi']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($matkul['sks']); ?> SKS</span>
                                                </td>
                                                <td>Semester <?php echo htmlspecialchars($matkul['semester']); ?></td>
                                                <td><?php echo htmlspecialchars($matkul['jurusan']); ?></td>
                                                <td>
                                                    <?php if ($matkul['status'] == 'active'): ?>
                                                        <span class="badge badge-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Non-Aktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="matakuliah.php?edit=<?php echo $matkul['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="matakuliah.php?hapus=<?php echo $matkul['id']; ?>" class="btn btn-sm btn-danger" 
                                                       title="Hapus" onclick="return confirm('Yakin hapus data mata kuliah <?php echo htmlspecialchars($matkul['nama_mk']); ?>?')">
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
                                <strong>Total: <?php echo count($mataKuliahData); ?> Mata Kuliah</strong>
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

function exportToExcel() {
    let table = document.querySelector('table');
    let html = table.outerHTML;
    let url = 'data:application/vnd.ms-excel,' + escape(html);
    let link = document.createElement('a');
    link.href = url;
    link.download = 'data_mata_kuliah.xls';
    link.click();
}

// Auto-format kode mata kuliah to uppercase
document.getElementById('kode_mk').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});

function confirmDelete(nama) {
    return confirm('PERINGATAN: Data akan dihapus secara PERMANEN dan tidak dapat dikembalikan!\n\nYakin hapus ' + nama + '?');
}


</script>



<!-- Script untuk tampilkan nama file -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('#fileUpload').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        e.target.nextElementSibling.innerText = fileName;
    });
});
</script>