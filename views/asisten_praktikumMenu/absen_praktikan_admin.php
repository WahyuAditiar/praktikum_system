<?php
// views/asisten_praktikumMenu/absen_praktikan_admin.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /unauthorized.php');
    exit;
}

include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
    <h3>Data Absen Praktikan (Admin)</h3>
    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Absen</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Kode Jadwal</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach ($absenList as $a): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($a['nim']) ?></td>
                <td><?= htmlspecialchars($a['nama']) ?></td>
                <td><?= htmlspecialchars($a['kode_jadwal']) ?></td>
                <td><?= htmlspecialchars($a['status']) ?></td>
                <td><?= htmlspecialchars($a['keterangan']) ?></td>
                <td><?= htmlspecialchars($a['created_at']) ?></td>
                <td>
                    <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $a['id'] ?>">Edit</a>
                    <a href="?delete=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus absen ini?')">Delete</a>
                </td>
            </tr>
            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?= $a['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <form method="post">
                  <input type="hidden" name="edit_id" value="<?= $a['id'] ?>">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Absen</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-2">
                        <label>Status</label>
                        <select name="edit_status" class="form-control" required>
                          <option value="hadir" <?= $a['status']==='hadir'?'selected':'' ?>>Hadir</option>
                          <option value="izin" <?= $a['status']==='izin'?'selected':'' ?>>Izin</option>
                          <option value="sakit" <?= $a['status']==='sakit'?'selected':'' ?>>Sakit</option>
                          <option value="alpa" <?= $a['status']==='alpa'?'selected':'' ?>>Alpa</option>
                        </select>
                      </div>
                      <div class="mb-2">
                        <label>Keterangan</label>
                        <input type="text" name="edit_keterangan" class="form-control" value="<?= htmlspecialchars($a['keterangan']) ?>">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
      <div class="modal-dialog">
        <form method="post">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Absen Praktikan</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-2">
                <label>NIM</label>
                <input type="text" name="add_nim" class="form-control" required>
              </div>
              <div class="mb-2">
                <label>Kode Jadwal</label>
                <input type="text" name="add_kode_jadwal" class="form-control" required>
              </div>
              <div class="mb-2">
                <label>Status</label>
                <select name="add_status" class="form-control" required>
                  <option value="hadir">Hadir</option>
                  <option value="izin">Izin</option>
                  <option value="sakit">Sakit</option>
                  <option value="alpa">Alpa</option>
                </select>
              </div>
              <div class="mb-2">
                <label>Keterangan</label>
                <input type="text" name="add_keterangan" class="form-control">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Tambah</button>
            </div>
          </div>
        </form>
      </div>
    </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
