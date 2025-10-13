<?php
// views/asisten_praktikumMenu/absen_praktikan.php
// Menu absen praktikan untuk role asprak & admin
// Fitur: asprak/admin mengisi absen praktikan berdasarkan kode random dari jadwal_praktikum
// Relasi: jadwal_praktikum, mahasiswa, absensi_praktikan

session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['asprak', 'admin'])) {
    header('Location: /unauthorized.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/MahasiswaModel.php';
require_once __DIR__ . '/../../models/JadwalPraktikumModel.php';

$db = (new Database())->getConnection();
$mahasiswaModel = new MahasiswaModel($db);
$jadwalModel = new JadwalPraktikumModel($db);

// Ambil filter dari GET
$praktikum_id = isset($_GET['praktikum_id']) ? $_GET['praktikum_id'] : '';
$tahun_akademik = isset($_GET['tahun_akademik']) ? $_GET['tahun_akademik'] : '';
$pertemuan = isset($_GET['pertemuan']) ? (int)$_GET['pertemuan'] : '';
$kode = isset($_GET['kode']) ? trim($_GET['kode']) : '';
$jadwal = null;
$mahasiswaList = [];
$kodeValid = false;
if ($praktikum_id && $tahun_akademik && $pertemuan && $kode) {
    // Cari jadwal berdasarkan praktikum_id, tahun_akademik, dan kode
    $jadwalList = $jadwalModel->getAllJadwal();
    foreach ($jadwalList as $j) {
        if ($j['praktikum_id'] == $praktikum_id && strpos($j['tahun_akademik'], $tahun_akademik) !== false && ($j['kode_random'] ?? $j['kode']) == $kode) {
            $jadwal = $j;
            $kodeValid = true;
            break;
        }
    }
    if ($jadwal) {
        $mahasiswaList = $mahasiswaModel->getMahasiswaByPraktikum($praktikum_id);
    }
}

// Proses submit absen
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jadwal_id']) && isset($_POST['pertemuan'])) {
    require_once __DIR__ . '/../../models/AbsensiPraktikanModel.php';
    $absenModel = new AbsensiPraktikanModel($db);
    $jadwal_id = $_POST['jadwal_id'];
    $pertemuan = (int)$_POST['pertemuan'];
    $nimList = array_keys($_POST['status']);
    $statusList = $_POST['status'];
    $keteranganList = isset($_POST['keterangan']) ? $_POST['keterangan'] : [];
    $created_by = $_SESSION['user_id'];
    $success = true;
    foreach ($nimList as $nim) {
        $status = $statusList[$nim];
        $keterangan = isset($keteranganList[$nim]) ? $keteranganList[$nim] : '';
        // Simpan absen dengan field pertemuan
        $result = $absenModel->createAbsenWithPertemuan($nim, $jadwal_id, $pertemuan, $status, $keterangan, $created_by);
        if (!$result) {
            $success = false;
            break;
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
    <h3>Absen Praktikan</h3>
    <?php if ($praktikum_id && $tahun_akademik && $pertemuan && $kode): ?>
        <?php if (!$kodeValid): ?>
            <div class="alert alert-danger">Kode kunci tidak valid atau tidak cocok dengan jadwal.</div>
        <?php elseif (!$jadwal): ?>
            <div class="alert alert-danger">Jadwal tidak ditemukan.</div>
        <?php else: ?>
            <div class="mb-3">
                <strong>Praktikum:</strong> <?= htmlspecialchars($jadwal['nama_praktikum']) ?> <br>
                <strong>Kode Kunci:</strong> <?= htmlspecialchars($jadwal['kode_random'] ?? $jadwal['kode'] ?? '-') ?> <br>
                <strong>Waktu:</strong> <?= htmlspecialchars(($jadwal['hari'] ?? '') . ' ' . ($jadwal['jam_mulai'] ?? '') . ' - ' . ($jadwal['jam_selesai'] ?? '')) ?> <br>
                <strong>Pertemuan:</strong> <?= htmlspecialchars($pertemuan) ?>
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success">Absen berhasil disimpan!</div>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="jadwal_id" value="<?= htmlspecialchars($jadwal['id']) ?>">
                <input type="hidden" name="pertemuan" value="<?= htmlspecialchars($pertemuan) ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach ($mahasiswaList as $m): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($m['nim']) ?></td>
                                <td><?= htmlspecialchars($m['nama']) ?></td>
                                <td>
                                    <select class="form-select" name="status[<?= $m['nim'] ?>]" required>
                                        <option value="" selected disabled>Pilih status</option>
                                        <option value="hadir">Hadir</option>
                                        <option value="izin">Izin</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="alpa">Alpa</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="keterangan[<?= $m['nim'] ?>]" placeholder="Keterangan (opsional)">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-success w-100 py-2 mt-2" style="font-weight:bold; font-size:1.1em;">Simpan Absen</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">Silakan pilih filter dan klik Entri Absen dari halaman sebelumnya.</div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
