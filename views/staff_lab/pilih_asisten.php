<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/PraktikumModel.php';
session_start();

$conn = (new Database())->getConnection();
$model = new PraktikumModel($conn);

$nimAsisten = $_SESSION['nim'] ?? null;
$listPraktikumAsisten = $model->getByAsisten($nimAsisten);
?>

<h3>Pilih Praktikum untuk Absen</h3>
<div class="row">
    <?php foreach ($listPraktikumAsisten as $prak): ?>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5><?= htmlspecialchars($prak['nama']) ?></h5>
                    <p>Kelas: <?= htmlspecialchars($prak['kelas']) ?></p>
                    <a href="absen_asistenpraktikum.php?prak_id=<?= $prak['id'] ?>"
                       class="btn btn-primary">Absen di sini</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
