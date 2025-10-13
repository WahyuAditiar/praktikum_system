<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/RuanganModel.php';
require_once __DIR__ . '/../../controllers/RuanganController.php';

checkAuth();
checkRole(['staff_prodi', 'admin']);

$database = new Database();
$db = $database->getConnection();
$ruanganController = new RuanganController($db);

$page_title = "Manajemen Ruangan";
$error = '';
$success = '';

// Handle form actions
if ($_POST) {
    if (isset($_POST['tambah_ruangan'])) {
        $result = $ruanganController->createRuangan($_POST);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = implode('<br>', $result['errors']);
        }
    } elseif (isset($_POST['edit_ruangan'])) {
        $id = $_POST['id'];
        $result = $ruanganController->updateRuangan($id, $_POST);
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
    $result = $ruanganController->deleteRuangan($id);
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = implode('<br>', $result['errors']);
    }
}

// Get all ruangan data
$ruanganData = $ruanganController->getAllRuangan();
$statistics = $ruanganController->getStatistics();
$totalKapasitas = $ruanganController->getTotalKapasitas();
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manajemen Ruangan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Ruangan</li>
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
                            <p>Total Ruangan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                           <h3><?php echo isset($statsMap['active']) ? $statsMap['active'] : 0; ?></h3>
                            <p>Ruangan Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo isset($statsMap['maintenance']) ? $statsMap['maintenance'] : 0; ?></h3>
                            <p>Dalam Perbaikan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?php echo isset($totalKapasitas['total_kapasitas']) ? $totalKapasitas['total_kapasitas'] : 0; ?></h3>
                            <p>Total Kapasitas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <!-- Form Ruangan -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Ruangan Baru
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="ruanganForm">
                                <input type="hidden" name="tambah_ruangan" value="1">

                                <div class="form-group">
                                    <label for="kode_ruangan">Kode Ruangan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kode_ruangan" name="kode_ruangan" required>
                                </div>

                                <div class="form-group">
                                    <label for="nama_ruangan">Nama Ruangan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" required>
                                </div>

                                <div class="form-group">
                                    <label for="kapasitas">Kapasitas <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="1" required>
                                </div>

                                <div class="form-group">
                                    <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                                </div>

                                <div class="form-group">
                                    <label for="fasilitas">Fasilitas</label>
                                    <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status Ruangan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active">Aktif</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="inactive">Non-Aktif</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus mr-1"></i> Tambah Ruangan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Tabel Ruangan -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Daftar Ruangan
                            </h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="searchInput" class="form-control float-right" placeholder="Cari ruangan...">
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
                                        <th>Nama Ruangan</th>
                                        <th>Kapasitas</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ruanganData)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Belum ada data ruangan</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ruanganData as $index => $ruangan): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($ruangan['kode_ruangan']); ?></strong></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($ruangan['nama_ruangan']); ?></strong>
                                                    <?php if (!empty($ruangan['fasilitas'])): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($ruangan['fasilitas']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($ruangan['kapasitas']); ?> orang</td>
                                                <td><?php echo htmlspecialchars($ruangan['lokasi']); ?></td>
                                                <td>
                                                    <?php if ($ruangan['status'] == 'active'): ?>
                                                        <span class="badge badge-success">Aktif</span>
                                                    <?php elseif ($ruangan['status'] == 'maintenance'): ?>
                                                        <span class="badge badge-warning">Maintenance</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Non-Aktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <!-- Tombol Edit Popout -->
                                                    <a href="#" 
                                                       class="btn btn-sm btn-warning btn-edit" 
                                                       data-id="<?php echo $ruangan['id']; ?>"
                                                       data-kode="<?php echo htmlspecialchars($ruangan['kode_ruangan']); ?>"
                                                       data-nama="<?php echo htmlspecialchars($ruangan['nama_ruangan']); ?>"
                                                       data-kapasitas="<?php echo htmlspecialchars($ruangan['kapasitas']); ?>"
                                                       data-lokasi="<?php echo htmlspecialchars($ruangan['lokasi']); ?>"
                                                       data-fasilitas="<?php echo htmlspecialchars($ruangan['fasilitas']); ?>"
                                                       data-status="<?php echo htmlspecialchars($ruangan['status']); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <a href="ruangan.php?hapus=<?php echo $ruangan['id']; ?>" class="btn btn-sm btn-danger" 
                                                       title="Hapus" onclick="return confirm('Yakin hapus data ruangan <?php echo htmlspecialchars($ruangan['nama_ruangan']); ?>?')">
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
                                <strong>Total: <?php echo count($ruanganData); ?> Ruangan</strong>
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

<!-- Modal Edit Ruangan -->
<div class="modal fade" id="editRuanganModal" tabindex="-1" role="dialog" aria-labelledby="editRuanganLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="POST" id="editRuanganForm">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="editRuanganLabel"><i class="fas fa-edit"></i> Edit Data Ruangan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <input type="hidden" name="edit_ruangan" value="1">

          <div class="form-group">
            <label for="edit_kode_ruangan">Kode Ruangan</label>
            <input type="text" class="form-control" id="edit_kode_ruangan" name="kode_ruangan" required>
          </div>

          <div class="form-group">
            <label for="edit_nama_ruangan">Nama Ruangan</label>
            <input type="text" class="form-control" id="edit_nama_ruangan" name="nama_ruangan" required>
          </div>

          <div class="form-group">
            <label for="edit_kapasitas">Kapasitas</label>
            <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="1" required>
          </div>

          <div class="form-group">
            <label for="edit_lokasi">Lokasi</label>
            <input type="text" class="form-control" id="edit_lokasi" name="lokasi" required>
          </div>

          <div class="form-group">
            <label for="edit_fasilitas">Fasilitas</label>
            <textarea class="form-control" id="edit_fasilitas" name="fasilitas" rows="3"></textarea>
          </div>

          <div class="form-group">
            <label for="edit_status">Status</label>
            <select class="form-control" id="edit_status" name="status" required>
              <option value="active">Aktif</option>
              <option value="maintenance">Maintenance</option>
              <option value="inactive">Non-Aktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Batal
          </button>
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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

// Form validation tambah ruangan
document.getElementById('ruanganForm').addEventListener('submit', function(e) {
    const kapasitas = document.getElementById('kapasitas').value;
    if (kapasitas <= 0) {
        e.preventDefault();
        alert('Kapasitas harus lebih dari 0');
        return false;
    }
});

// Auto-format kode ruangan to uppercase
document.getElementById('kode_ruangan').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});

// Export excel
function exportToExcel() {
    let table = document.querySelector('table');
    let html = table.outerHTML;
    let url = 'data:application/vnd.ms-excel,' + escape(html);
    let link = document.createElement('a');
    link.href = url;
    link.download = 'data_ruangan.xls';
    link.click();
}

// Modal Edit Handler
document.querySelectorAll('.btn-edit').forEach(button => {
  button.addEventListener('click', function() {
    document.getElementById('edit_id').value = this.dataset.id;
    document.getElementById('edit_kode_ruangan').value = this.dataset.kode;
    document.getElementById('edit_nama_ruangan').value = this.dataset.nama;
    document.getElementById('edit_kapasitas').value = this.dataset.kapasitas;
    document.getElementById('edit_lokasi').value = this.dataset.lokasi;
    document.getElementById('edit_fasilitas').value = this.dataset.fasilitas;
    document.getElementById('edit_status').value = this.dataset.status;
    $('#editRuanganModal').modal('show');
  });
});
</script>

<script>
setTimeout(function() {
    $(".alert-success").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 3000);

setTimeout(function() {
    $(".alert-danger").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 5000);
</script>