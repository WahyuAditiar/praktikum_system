<?php
// views/asisten_praktikumMenu/filter_absen_praktikan.php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['asprak', 'admin'])) {
    header('Location: /unauthorized.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/JadwalPraktikumModel.php';

$db = (new Database())->getConnection();
$jadwalModel = new JadwalPraktikumModel($db);

// Ambil data dropdown praktikum, tahun akademik, pertemuan
$jadwalList = $jadwalModel->getAllJadwal();
$tahunList = [];
foreach ($jadwalList as $j) {
    $tahun = substr($j['tahun_akademik'] ?? '', 0, 9);
    if ($tahun && !in_array($tahun, $tahunList)) $tahunList[] = $tahun;
}
$praktikumList = [];
foreach ($jadwalList as $j) {
    if (!isset($praktikumList[$j['praktikum_id']])) {
        $praktikumList[$j['praktikum_id']] = $j['nama_praktikum'];
    }
}

include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
    <h3>Input Kehadiran Praktikan</h3>
    <form method="get" action="absen_praktikan.php" class="card p-4 shadow-sm" style="max-width:600px;margin:auto;">
        <div class="mb-3">
            <label class="form-label">Pilih Praktikum</label>
            <select name="praktikum_id" class="form-select" required>
                <option value="">Pilih Praktikum</option>
                <?php foreach ($praktikumList as $id => $nama): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($nama) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tahun Akademik</label>
            <select name="tahun_akademik" class="form-select" required>
                <option value="">Pilih Tahun</option>
                <?php foreach ($tahunList as $tahun): ?>
                    <option value="<?= htmlspecialchars($tahun) ?>"><?= htmlspecialchars($tahun) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Pertemuan</label>
            <select name="pertemuan" class="form-select" required>
                <option value="">Pilih Pertemuan</option>
                <?php for ($i=1; $i<=16; $i++): ?>
                    <option value="<?= $i ?>">Pertemuan Ke-<?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Kode Kunci (Kode Absen)</label>
            <input type="text" name="kode" class="form-control" placeholder="Masukkan kode absen" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Entri Absen</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>
