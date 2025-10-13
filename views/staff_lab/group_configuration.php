<?php
// Cek session dan role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
    header("Location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfigurasi Group Praktikum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Konfigurasi Group Praktikum</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- FORM FILTER PRAKTIKUM & KELAS -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Pilih Praktikum & Kelas</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <input type="hidden" name="page" value="group">
                    <input type="hidden" name="action" value="config">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nama Praktikum:</label>
                            <select name="praktikum_id" class="form-select" required onchange="this.form.submit()">
                                <option value="">-- Pilih Praktikum --</option>
                                <?php foreach ($data['all_praktikum'] as $praktikum): ?>
                                    <option value="<?= $praktikum['id'] ?>" 
                                        <?= (isset($_GET['praktikum_id']) && $_GET['praktikum_id'] == $praktikum['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($praktikum['nama_praktikum']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Kelas:</label>
                            <select name="kelas" class="form-select" required onchange="this.form.submit()">
                                <option value="">-- Pilih Kelas --</option>
                                <?php if (isset($_GET['praktikum_id'])): ?>
                                    <?php 
                                    $kelas_list = ['A', 'B', 'C', 'D', 'E'];
                                    foreach ($kelas_list as $kelas): ?>
                                        <option value="<?= $kelas ?>" 
                                            <?= (isset($_GET['kelas']) && $_GET['kelas'] == $kelas) ? 'selected' : '' ?>>
                                            Kelas <?= $kelas ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (isset($data['jadwal']) && !empty($data['jadwal'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5>
                        <?= htmlspecialchars($data['jadwal']['nama_praktikum'] ?? 'Nama Praktikum Tidak Ditemukan') ?>
                        - Kelas <?= htmlspecialchars($data['jadwal']['kelas'] ?? '-') ?>
                    </h5>
                    <p class="mb-0">
                        Total Praktikan: <?= count($data['mahasiswa']) ?> orang | 
                        Total Asprak: <?= count($data['aspraks']) ?> orang
                    </p>
                </div>
                <div class="card-body">
                    <!-- FORM KONFIGURASI GROUP -->
                    <form method="POST" action="?page=group&action=updateConfig">
                        <input type="hidden" name="jadwal_id" value="<?= $data['jadwal']['id'] ?>">
                        <input type="hidden" name="praktikum_id" value="<?= $_GET['praktikum_id'] ?? '' ?>">
                        <input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?? '' ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah Group:</label>
                                <select name="total_groups" class="form-select" required>
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?= $i ?>" <?= ($data['group_config']['total_groups'] ?? 1) == $i ? 'selected' : '' ?>>
                                            <?= $i ?> Group
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Maks. Praktikan per Group:</label>
                                <input type="number" name="max_mahasiswa" class="form-control" 
                                       value="<?= $data['group_config']['max_mahasiswa_per_group'] ?? 10 ?>" min="1" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Update Konfigurasi
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- TOMBOL BAGI OTOMATIS -->
                    <form method="POST" action="?page=group&action=bagiOtomatis" class="mb-4">
                        <input type="hidden" name="jadwal_id" value="<?= $data['jadwal']['id'] ?>">
                        <input type="hidden" name="praktikum_id" value="<?= $_GET['praktikum_id'] ?? '' ?>">
                        <input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?? '' ?>">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-robot"></i> Bagi Otomatis
                        </button>
                        <small class="text-muted ms-2">Bagi praktikan dan asprak secara merata ke semua group</small>
                    </form>
                    
                    <!-- TOMBOL RESET -->
                    <form method="POST" action="?page=group&action=resetAssignments" class="mb-4" 
                          onsubmit="return confirm('Yakin ingin menghapus semua penugasan group?')">
                        <input type="hidden" name="jadwal_id" value="<?= $data['jadwal']['id'] ?>">
                        <input type="hidden" name="praktikum_id" value="<?= $_GET['praktikum_id'] ?? '' ?>">
                        <input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?? '' ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Reset Semua Penugasan
                        </button>
                        <small class="text-muted ms-2">Hapus semua penugasan praktikan dan asprak</small>
                    </form>
                    
                    <!-- INFO PER GROUP -->
                    <h5>Daftar Group</h5>
                    <div class="row">
                        <?php for ($group = 1; $group <= ($data['group_config']['total_groups'] ?? 1); $group++): ?>
                            <?php
                            $group_mahasiswa = $this->groupModel->getMahasiswaByGroup($data['jadwal']['id'], $group);
                            $group_aspraks = $this->groupModel->getAsprakByGroup($data['jadwal']['id'], $group);
                            ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Group <?= $group ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Praktikan:</strong> <?= count($group_mahasiswa) ?> orang</p>
                                        <p><strong>Asisten:</strong> <?= count($group_aspraks) ?> orang</p>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="?page=group&action=assignManual&jadwal_id=<?= $data['jadwal']['id'] ?>&group=<?= $group ?>&praktikum_id=<?= $_GET['praktikum_id'] ?? '' ?>&kelas=<?= $_GET['kelas'] ?? '' ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Atur Manual
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <!-- ✅ SECTION BARU: DETAIL ASSIGNMENT -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-bar"></i> Detail Assignment</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Ambil data summary dan debug
                            $groupSummary = $this->groupModel->getGroupSummary($data['jadwal']['id']);
                            $debugData = $this->groupModel->debugAssignment($data['jadwal']['id']);
                            ?>
                            
                            <h6>Summary per Group:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Group</th>
                                            <th>Total Mahasiswa</th>
                                            <th>Total Asisten</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupSummary as $summary): ?>
                                            <tr>
                                                <td><strong>Group <?= $summary['group_number'] ?></strong></td>
                                                <td><?= $summary['total_mahasiswa'] ?> orang</td>
                                                <td><?= $summary['total_asisten'] ?> orang</td>
                                                <td><strong><?= $summary['total_mahasiswa'] + $summary['total_asisten'] ?> orang</strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($groupSummary)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Belum ada assignment</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tombol Toggle Detail -->
                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#debugDetails">
                                <i class="fas fa-search"></i> Tampilkan Detail Lengkap
                            </button>
                            
                            <!-- Detail Assignment (Collapsible) -->
                            <div class="collapse mt-3" id="debugDetails">
                                <div class="card card-body">
                                    <h6>Detail Assignment:</h6>
                                    <?php if (!empty($debugData['assignments'])): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Group</th>
                                                        <th>Tipe</th>
                                                        <th>Nama</th>
                                                        <th>ID</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($debugData['assignments'] as $assignment): ?>
                                                        <tr>
                                                            <td>Group <?= $assignment['group_number'] ?></td>
                                                            <td>
                                                                <span class="badge <?= $assignment['entity_type'] == 'mahasiswa' ? 'bg-primary' : 'bg-success' ?>">
                                                                    <?= $assignment['entity_type'] ?>
                                                                </span>
                                                            </td>
                                                            <td><?= $assignment['nama_entity'] ?? 'N/A' ?></td>
                                                            <td><small><?= $assignment['entity_id'] ?></small></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">Tidak ada data assignment</p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($debugData['group_summary'])): ?>
                                        <h6 class="mt-3">Group Summary (Debug):</h6>
                                        <pre class="bg-light p-3 rounded"><?= json_encode($debugData['group_summary'], JSON_PRETTY_PRINT) ?></pre>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (isset($_GET['praktikum_id']) && isset($_GET['kelas'])): ?>
            <div class="alert alert-warning">
                Tidak ada jadwal praktikum ditemukan untuk pilihan ini.
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Silakan pilih Praktikum dan Kelas terlebih dahulu.
            </div>
        <?php endif; ?>
        
        <a href="?page=dashboard" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto submit form ketika praktikum dipilih
        document.querySelector('select[name="praktikum_id"]').addEventListener('change', function() {
            // Reset kelas selection ketika praktikum berubah
            document.querySelector('select[name="kelas"]').value = '';
        });
    </script>
</body>
</html>