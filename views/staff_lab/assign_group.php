<?php
// ✅ Cek session dan role - versi benar
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
    <title>Assign Manual Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Assign Manual - Group <?= $data['group_number'] ?></h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5><?= htmlspecialchars($data['jadwal']['nama_praktikum']) ?> - Kelas <?= $data['jadwal']['kelas'] ?? '' ?></h5>
                <p class="mb-0">Group <?= $data['group_number'] ?></p>
            </div>
            <div class="card-body">
                
                <!-- ✅ TOMBOL ASSIGN SEMUA ASPRAK -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form method="POST" action="?page=group&action=assignAllAsprak" 
                              onsubmit="return confirm('Yakin ingin assign SEMUA asisten ke Group <?= $data['group_number'] ?>?')">
                            <input type="hidden" name="jadwal_id" value="<?= $data['jadwal']['id'] ?>">
                            <input type="hidden" name="group_number" value="<?= $data['group_number'] ?>">
                            <input type="hidden" name="praktikum_id" value="<?= $_GET['praktikum_id'] ?? '' ?>">
                            <input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?? '' ?>">
                            
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-users"></i> Assign Semua Asisten ke Group Ini
                            </button>
                            <small class="text-muted d-block mt-1">
                                Akan assign semua <?= count($data['aspraks']) ?> asisten ke Group <?= $data['group_number'] ?>
                            </small>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>Info:</strong> 
                            <br>✅ <?= count($data['assigned_mahasiswa']) ?> mahasiswa di group ini
                            <br>✅ <?= count($data['assigned_aspraks']) ?> asisten di group ini
                        </div>
                    </div>
                </div>

                <!-- ASSIGN MAHASISWA -->
                <h5><i class="fas fa-user-graduate"></i> Assign Mahasiswa</h5>
                <form method="POST" action="?page=group&action=updateAssignments">
                    <input type="hidden" name="jadwal_id" value="<?= $data['jadwal']['id'] ?>">
                    <input type="hidden" name="group_number" value="<?= $data['group_number'] ?>">
                    <input type="hidden" name="entity_type" value="mahasiswa">
                    <input type="hidden" name="praktikum_id" value="<?= $_GET['praktikum_id'] ?? '' ?>">
                    <input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?? '' ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllMahasiswa">
                                            <label class="form-check-label">#</label>
                                        </div>
                                    </th>
                                    <th width="15%">NIM</th>
                                    <th width="30%">Nama</th>
                                    <th width="15%">Kelas</th>
                                    <th width="15%">Assign ke Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['mahasiswa'] as $index => $mhs): ?>
                                    <?php 
                                    $is_assigned = false;
                                    foreach ($data['assigned_mahasiswa'] as $assigned) {
                                        if ($assigned['id'] == $mhs['id']) {
                                            $is_assigned = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input mahasiswa-checkbox" type="checkbox" 
                                                       name="assign_mahasiswa[]" 
                                                       value="<?= $mhs['id'] ?>"
                                                       <?= $is_assigned ? 'checked' : '' ?>>
                                                <label class="form-check-label">
                                                    <?= $index + 1 ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($mhs['nim']) ?></td>
                                        <td><?= htmlspecialchars($mhs['nama']) ?></td>
                                        <td><?= htmlspecialchars($mhs['kelas']) ?></td>
                                        <td>
                                            <?php if ($is_assigned): ?>
                                                <span class="badge bg-success">Group <?= $data['group_number'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Assign Mahasiswa
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetMahasiswaSelection()">
                            <i class="fas fa-undo"></i> Reset Selection
                        </button>
                    </div>
                </form>

                <hr>

                <!-- ASSIGN ASPRAK -->
                <h5><i class="fas fa-user-tie"></i> Assign Asisten Praktikum</h5>
                <form method="POST" action="?page=group&action=updateAssignments">
                    <input type="hidden" name="jadwal_id" value="<?= $data['jadwal']['id'] ?>">
                    <input type="hidden" name="group_number" value="<?= $data['group_number'] ?>">
                    <input type="hidden" name="entity_type" value="asisten">
                    <input type="hidden" name="praktikum_id" value="<?= $_GET['praktikum_id'] ?? '' ?>">
                    <input type="hidden" name="kelas" value="<?= $_GET['kelas'] ?? '' ?>">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllAsprak">
                                            <label class="form-check-label">#</label>
                                        </div>
                                    </th>
                                    <th width="15%">NIM</th>
                                    <th width="30%">Nama</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Assign ke Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['aspraks'] as $index => $asprak): ?>
                                    <?php 
                                    $is_assigned = false;
                                    foreach ($data['assigned_aspraks'] as $assigned) {
                                        if ($assigned['id'] == $asprak['id']) {
                                            $is_assigned = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input asprak-checkbox" type="checkbox" 
                                                       name="assign_asprak[]" 
                                                       value="<?= $asprak['id'] ?>"
                                                       <?= $is_assigned ? 'checked' : '' ?>>
                                                <label class="form-check-label">
                                                    <?= $index + 1 ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($asprak['nim']) ?></td>
                                        <td><?= htmlspecialchars($asprak['nama']) ?></td>
                                        <td>
                                            <span class="badge <?= $asprak['status'] == 'active' ? 'bg-success' : 'bg-warning' ?>">
                                                <?= $asprak['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($is_assigned): ?>
                                                <span class="badge bg-success">Group <?= $data['group_number'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Assign Asisten
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetAsprakSelection()">
                            <i class="fas fa-undo"></i> Reset Selection
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <div class="mt-3">
            <a href="?page=group&action=config&praktikum_id=<?= $_GET['praktikum_id'] ?? '' ?>&kelas=<?= $_GET['kelas'] ?? '' ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Konfigurasi
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Select All untuk Mahasiswa
        document.getElementById('selectAllMahasiswa').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.mahasiswa-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Select All untuk Asprak
        document.getElementById('selectAllAsprak').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.asprak-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Reset selection mahasiswa
        function resetMahasiswaSelection() {
            document.getElementById('selectAllMahasiswa').checked = false;
            const checkboxes = document.querySelectorAll('.mahasiswa-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        // Reset selection asprak
        function resetAsprakSelection() {
            document.getElementById('selectAllAsprak').checked = false;
            const checkboxes = document.querySelectorAll('.asprak-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
</body>
</html>