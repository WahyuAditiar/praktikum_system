<?php
require_once '../../config/config.php';

$db = (new Database())->getConnection();

// Statistik
$total = $db->query("SELECT COUNT(*) AS jml FROM praktikum")->fetch(PDO::FETCH_ASSOC)['jml'];
$aktif = $db->query("SELECT COUNT(*) AS jml FROM praktikum WHERE status='aktif'")->fetch(PDO::FETCH_ASSOC)['jml'];
$nonaktif = $db->query("SELECT COUNT(*) AS jml FROM praktikum WHERE status='nonaktif'")->fetch(PDO::FETCH_ASSOC)['jml'];

// Dropdown mata kuliah (hanya yang aktif dan ada kata 'Prak.')
$matakuliah = $db->query("SELECT * FROM mata_kuliah WHERE status='active' AND nama_mk LIKE 'Prak.%' ORDER BY nama_mk ASC")->fetchAll(PDO::FETCH_ASSOC);

// Tambah praktikum
if (isset($_POST['tambah_praktikum'])) {
    $mata_kuliah_id = $_POST['mata_kuliah_id'];

    // Jika pilih "buat baru"
    if ($mata_kuliah_id === "new") {
        if (!empty($_POST['kode_mk_baru']) && !empty($_POST['nama_mk_baru'])) {
            $stmt = $db->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, status) VALUES (?, ?, 'active')");
            $stmt->execute([$_POST['kode_mk_baru'], $_POST['nama_mk_baru']]);
            $mata_kuliah_id = $db->lastInsertId();
        } else {
            die("Error: kode MK dan nama MK harus diisi jika menambah baru.");
        }
    }

    // Insert praktikum
    $stmt = $db->prepare("INSERT INTO praktikum 
        (mata_kuliah_id, nama_praktikum, semester, tahun_ajaran, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([
        $mata_kuliah_id, 
        $_POST['nama_praktikum'], 
        $_POST['semester'], 
        $_POST['tahun_ajaran'], 
        $_POST['status']
    ]);

    header("Location: praktikum.php?success=1");
    exit;
}

// Edit praktikum
if (isset($_POST['edit_praktikum'])) {
    $stmt = $db->prepare("UPDATE praktikum SET nama_praktikum=?, semester=?, tahun_ajaran=?, status=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$_POST['nama_praktikum'], $_POST['semester'], $_POST['tahun_ajaran'], $_POST['status'], $_POST['id']]);
    header("Location: praktikum.php?updated=1");
    exit;
}

// Hapus praktikum
if (isset($_POST['hapus_praktikum'])) {
    $stmt = $db->prepare("DELETE FROM praktikum WHERE id=?");
    $stmt->execute([$_POST['id']]);
    header("Location: praktikum.php?deleted=1");
    exit;
}

// Daftar praktikum
$praktikum = $db->query("
    SELECT p.*, m.kode_mk, m.nama_mk 
    FROM praktikum p 
    JOIN mata_kuliah m ON p.mata_kuliah_id = m.id 
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Manajemen Praktikum (Staff Lab)</h1>
    </section>

    <section class="content">
    <div class="row">
        <!-- Statistik -->
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3><?= $total ?></h3><p>Total Praktikum</p></div>
                <div class="icon"><i class="fas fa-flask"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3><?= $aktif ?></h3><p>Praktikum Aktif</p></div>
                <div class="icon"><i class="fas fa-check"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-secondary">
                <div class="inner"><h3><?= $nonaktif ?></h3><p>Praktikum Non-Aktif</p></div>
                <div class="icon"><i class="fas fa-times"></i></div>
            </div>
        </div>
    </div>

    <!-- Form & Tabel dalam 1 Row -->
<div class="row">
    <!-- Form Tambah Praktikum (Kiri) -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">+ Tambah Praktikum</h3></div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Mata Kuliah *</label>
                        <select id="mata_kuliah" name="mata_kuliah_id" class="form-control" required>
                            <option value="">Pilih Mata Kuliah</option>
                            <?php foreach ($matakuliah as $mk): ?>
                                <option value="<?= $mk['id']; ?>" data-nama="<?= $mk['nama_mk']; ?>">
                                    <?= $mk['kode_mk']; ?> - <?= $mk['nama_mk']; ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="new">+ Tambah Mata Kuliah Baru</option>
                        </select>
                    </div>

                    <!-- Field tambahan kalau pilih "new" -->
                    <div id="new_mk_fields" style="display:none;">
                        <div class="form-group">
                            <label>Kode Mata Kuliah Baru *</label>
                            <input type="text" name="kode_mk_baru" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Nama Mata Kuliah Baru *</label>
                            <input type="text" name="nama_mk_baru" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama_praktikum">Nama Praktikum *</label>
                        <input type="text" id="nama_praktikum" name="nama_praktikum" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Semester *</label>
                        <select name="semester" class="form-control" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun Ajaran *</label>
                        <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026" required>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                    <button type="submit" name="tambah_praktikum" class="btn btn-primary btn-block">
                        + Tambah Praktikum
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Praktikum (Kanan) -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Praktikum</h3></div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Kode MK</th>
                            <th>Nama MK dr Prodi</th>
                            <th>Nama Praktikum</th>
                            <th>Semester</th>
                            <th>Tahun Ajaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($praktikum) > 0): ?>
                            <?php foreach ($praktikum as $i => $p): ?>
                                <tr>
                                    <td><?= $i+1 ?></td>
                                    <td><?= htmlspecialchars($p['kode_mk']) ?></td>
                                    <td><?= htmlspecialchars($p['nama_mk']) ?></td>
                                    <td><?= htmlspecialchars($p['nama_praktikum']) ?></td>
                                    <td><?= htmlspecialchars($p['semester']) ?></td>
                                    <td><?= htmlspecialchars($p['tahun_ajaran']) ?></td>
                                    <td>
                                        <?php if ($p['status'] == 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm editBtn"
                                            data-id="<?= $p['id'] ?>"
                                            data-nama="<?= htmlspecialchars($p['nama_praktikum']) ?>"
                                            data-semester="<?= $p['semester'] ?>"
                                            data-tahun="<?= $p['tahun_ajaran'] ?>"
                                            data-status="<?= $p['status'] ?>"
                                            data-toggle="modal" data-target="#editModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm deleteBtn"
                                            data-id="<?= $p['id'] ?>"
                                            data-nama="<?= htmlspecialchars($p['nama_praktikum']) ?>"
                                            data-toggle="modal" data-target="#deleteModal">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center">Belum ada data praktikum</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>


<!-- Modal Edit -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Praktikum</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Nama Praktikum</label>
                <input type="text" id="edit_nama" name="nama_praktikum" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <select id="edit_semester" name="semester" class="form-control">
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tahun Ajaran</label>
                <input type="text" id="edit_tahun" name="tahun_ajaran" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select id="edit_status" name="status" class="form-control">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit_praktikum" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <input type="hidden" name="id" id="delete_id">
        <div class="modal-header">
          <h5 class="modal-title">Hapus Praktikum</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Yakin ingin menghapus <b id="delete_nama"></b> ?</p>
        </div>
        <div class="modal-footer">
          <button type="submit" name="hapus_praktikum" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>

<script>
document.getElementById('mata_kuliah').addEventListener('change', function() {
    let selectedOption = this.options[this.selectedIndex];
    let namaMK = selectedOption.getAttribute('data-nama');
    document.getElementById('nama_praktikum').value = namaMK ? namaMK : "";

});

// isi form edit
$('.editBtn').on('click', function() {
    $('#edit_id').val($(this).data('id'));
    $('#edit_nama').val($(this).data('nama'));
    $('#edit_semester').val($(this).data('semester'));
    $('#edit_tahun').val($(this).data('tahun'));
    $('#edit_status').val($(this).data('status'));
});

// isi form hapus
$('.deleteBtn').on('click', function() {
    $('#delete_id').val($(this).data('id'));
    $('#delete_nama').text($(this).data('nama'));
});
</script>

<script>
document.getElementById('mata_kuliah').addEventListener('change', function() {
    let selected = this.value;
    let newFields = document.getElementById('new_mk_fields');
    let namaPraktikum = document.getElementById('nama_praktikum');

    if (selected === "new") {
        newFields.style.display = "block";
        namaPraktikum.value = "";
    } else {
        newFields.style.display = "none";
        let option = this.options[this.selectedIndex];
        let namaMK = option.getAttribute('data-nama');
        if (namaMK) {
           namaPraktikum.value = namaMK;
        }
    }
});
</script>

<script>
document.getElementById('mata_kuliah').addEventListener('change', function() {
    let selected = this.value;
    let newFields = document.getElementById('new_mk_fields');
    let namaPraktikum = document.getElementById('nama_praktikum');

    if (selected === "new") {
        newFields.style.display = "block";
        namaPraktikum.value = "";
    } else {
        newFields.style.display = "none";
        let option = this.options[this.selectedIndex];
        let namaMK = option.getAttribute('data-nama');
        if (namaMK) {
            namaPraktikum.value = namaMK;
        }
    }
});
</script>

