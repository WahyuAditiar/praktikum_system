<?php
require_once '../../config/config.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id'])) {
    header("Location: praktikum.php");
    exit;
}

$id = $_GET['id'];

// Ambil data
$stmt = $db->prepare("SELECT * FROM praktikum WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE praktikum 
        SET mata_kuliah_id=?, nama_praktikum=?, semester=?, tahun_ajaran=?, status=?, updated_at=NOW() 
        WHERE id=?");
    $stmt->execute([
        $_POST['mata_kuliah_id'],
        $_POST['nama_praktikum'],
        $_POST['semester'],
        $_POST['tahun_ajaran'],
        $_POST['status'],
        $id
    ]);
    header("Location: praktikum.php?updated=1");
    exit;
}

// Ambil daftar mata kuliah
$matakuliah = $db->query("SELECT * FROM mata_kuliah ORDER BY nama_mk ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="POST">
    <label>Mata Kuliah</label>
    <select name="mata_kuliah_id" class="form-control">
        <?php foreach ($matakuliah as $mk): ?>
            <option value="<?= $mk['id']; ?>" <?= $mk['id'] == $data['mata_kuliah_id'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($mk['kode_mk']) ?> - <?= htmlspecialchars($mk['nama_mk']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Nama Praktikum</label>
    <input type="text" name="nama_praktikum" class="form-control" value="<?= htmlspecialchars($data['nama_praktikum']); ?>">

    <label>Semester</label>
    <input type="text" name="semester" class="form-control" value="<?= htmlspecialchars($data['semester']); ?>">

    <label>Tahun Ajaran</label>
    <input type="text" name="tahun_ajaran" class="form-control" value="<?= htmlspecialchars($data['tahun_ajaran']); ?>">

    <label>Status</label>
    <select name="status" class="form-control">
        <option value="aktif" <?= $data['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
        <option value="nonaktif" <?= $data['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
    </select>

    <button type="submit" class="btn btn-primary mt-3">Update</button>
</form>
