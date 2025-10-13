<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pilih Jadwal Praktikum</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daftar Jadwal Praktikum</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($data['jadwal_list'])): ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Tidak ada jadwal praktikum aktif.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($data['jadwal_list'] as $jadwal): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($jadwal['nama_praktikum']) ?></h5>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Kelas: <?= $jadwal['kelas'] ?? '-' ?><br>
                                                    Semester: <?= $jadwal['semester'] ?? '-' ?>
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <a href="?page=group&action=config&jadwal_id=<?= $jadwal['id'] ?>" 
                                               class="btn btn-primary btn-block">
                                                <i class="fas fa-cog mr-2"></i>Kelola Group
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-3">
                <a href="?page=dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </section>
</div>