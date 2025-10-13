<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/DosenModel.php';
require_once __DIR__ . '/../../controllers/DosenController.php';

checkAuth();
checkRole(['staff_prodi', 'admin']);

$database = new Database();
$db = $database->getConnection();
$dosenController = new DosenController($db);

$page_title = "Manajemen Dosen";
$error = '';
$success = '';

// Handle tambah
if (isset($_POST['tambah_dosen'])) {
    $result = $dosenController->createDosen($_POST);
    $success = $result['success'] ? $result['message'] : '';
    $error = !$result['success'] ? implode('<br>', $result['errors']) : '';
}

// Handle edit
if (isset($_POST['edit_dosen'])) {
    $result = $dosenController->updateDosen($_POST['id'], $_POST);
    $success = $result['success'] ? $result['message'] : '';
    $error = !$result['success'] ? implode('<br>', $result['errors']) : '';
}

// Handle hapus
if (isset($_GET['hapus'])) {
    $result = $dosenController->deleteDosen($_GET['hapus']);
    $success = $result['success'] ? $result['message'] : '';
    $error = !$result['success'] ? implode('<br>', $result['errors']) : '';
}

// Data
$dosenData = $dosenController->getAllDosen();
$statistics = $dosenController->getStatistics();
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h3><i class="fas fa-chalkboard-teacher mr-2"></i> Manajemen Dosen</h3>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success; ?></div>
            <?php endif; ?>

            <!-- Statistik -->
            <div class="row mb-3">
                <?php 
                    $map = [];
                    foreach ($statistics as $s) $map[$s['status']] = $s['total'];
                ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner"><h3><?= array_sum($map) ?></h3><p>Total Dosen</p></div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner"><h3><?= $map['tetap'] ?? 0 ?></h3><p>Dosen Tetap</p></div>
                        <div class="icon"><i class="fas fa-user-check"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner"><h3><?= $map['tidak_tetap'] ?? 0 ?></h3><p>Dosen Tidak Tetap</p></div>
                        <div class="icon"><i class="fas fa-user-clock"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner"><h3><?= $map['inactive'] ?? 0 ?></h3><p>Non-Aktif</p></div>
                        <div class="icon"><i class="fas fa-user-slash"></i></div>
                    </div>
                </div>
            </div>

            <!-- Card Tabel Dosen -->
         <div class="card-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-list mr-1"></i> Daftar Dosen
        </h3>
        <button class="btn btn-sm btn-primary ml-3" data-toggle="modal" data-target="#modalTambah">
            <i class="fas fa-plus mr-1"></i> Tambah Dosen
        </button>
    </div>
                        <!-- Search ke pojok kanan -->
    <div class="input-group input-group-sm ml-auto" style="width:300px;">
        <input type="text" id="searchInput" class="form-control" placeholder="Cari dosen...">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
        </div>
    </div>
</div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped table-bordered mb-0">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>NIDN</th>
                                <th>Nama</th>
                                <th>HP</th>
                                <th>Status</th>
                                <th style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$dosenData): ?>
                                <tr><td colspan="6" class="text-center text-muted">Belum ada data</td></tr>
                            <?php else: foreach ($dosenData as $i=>$d): ?>
                                <tr>
                                    <td class="text-center"><?= $i+1 ?></td>
                                    <td><strong><?= htmlspecialchars($d['nidn']) ?></strong></td>
                                    <td><?= htmlspecialchars($d['nama']) ?></td>
                                    <td><?= htmlspecialchars($d['no_hp']) ?></td>
                                    <td class="text-center">
                                        <?php if ($d['status']=='tetap'): ?>
                                            <span class="badge badge-success">Tetap</span>
                                        <?php elseif ($d['status']=='tidak_tetap'): ?>
                                            <span class="badge badge-warning">Tidak Tetap</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning editBtn"
                                                data-id="<?= $d['id'] ?>"
                                                data-nidn="<?= $d['nidn'] ?>"
                                                data-nama="<?= htmlspecialchars($d['nama']) ?>"
                                                data-hp="<?= $d['no_hp'] ?>"
                                                data-status="<?= $d['status'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?hapus=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus dosen ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Dosen</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="tambah_dosen" value="1">
                <div class="form-group">
                    <label>NIDN</label>
                    <input type="text" name="nidn" class="form-control" maxlength="10" required>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>HP</label>
                    <input type="text" name="hp" class="form-control" maxlength="15" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="">Pilih Status</option>
                        <option value="tetap">Tetap</option>
                        <option value="tidak_tetap">Tidak Tetap</option>
                        <option value="inactive">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content shadow-lg">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Dosen</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="edit_dosen" value="1">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>NIDN</label>
                    <input type="text" name="nidn" id="edit_nidn" class="form-control" maxlength="10" required>
                </div>
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>HP</label>
                    <input type="text" name="hp" id="edit_hp" class="form-control" maxlength="15" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="tetap">Tetap</option>
                        <option value="tidak_tetap">Tidak Tetap</option>
                        <option value="inactive">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<script>
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function(){
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_nidn').value = this.dataset.nidn;
        document.getElementById('edit_nama').value = this.dataset.nama;
        document.getElementById('edit_hp').value = this.dataset.hp;
        document.getElementById('edit_status').value = this.dataset.status;
        $('#modalEdit').modal('show');
    });
});

// Search filter
document.getElementById('searchInput').addEventListener('keyup', function(){
    let filter = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(row=>{
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>
