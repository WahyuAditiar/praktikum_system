<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten_praktikum') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Praktikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Rekap Absensi Praktikan
                </h4>
                <a href="?page=absensi" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            
            <?php if (isset($detail_jadwal)): ?>
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Detail Praktikum</h5>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th width="40%">Mata Kuliah</th>
                            <td><?= htmlspecialchars($detail_jadwal['nama_praktikum'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td><?= htmlspecialchars($detail_jadwal['kelas'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th>Pertemuan</th>
                            <td><?= htmlspecialchars($selected_pertemuan) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Statistik Kehadiran</h5>
                    <?php
                    $total_mahasiswa = count($mahasiswa);
                    $hadir = 0; $sakit = 0; $izin = 0; $alfa = 0;
                    
                    foreach ($mahasiswa as $mhs) {
                        $absensi_mhs = array_filter($absensi, function($item) use ($mhs) {
                            return $item['mahasiswa_id'] == $mhs['mahasiswa_id'];
                        });
                        $status = !empty($absensi_mhs) ? reset($absensi_mhs)['status'] : 'belum_diisi';
                        
                        switch ($status) {
                            case 'hadir': $hadir++; break;
                            case 'sakit': $sakit++; break;
                            case 'izin': $izin++; break;
                            case 'alfa': $alfa++; break;
                        }
                    }
                    ?>
                    <table class="table table-sm table-bordered">
                        <tr class="table-success">
                            <th>Hadir</th>
                            <td><?= $hadir ?> orang</td>
                            <td><?= $total_mahasiswa > 0 ? round(($hadir/$total_mahasiswa)*100, 1) : 0 ?>%</td>
                        </tr>
                        <tr class="table-warning">
                            <th>Sakit</th>
                            <td><?= $sakit ?> orang</td>
                            <td><?= $total_mahasiswa > 0 ? round(($sakit/$total_mahasiswa)*100, 1) : 0 ?>%</td>
                        </tr>
                        <tr class="table-info">
                            <th>Izin</th>
                            <td><?= $izin ?> orang</td>
                            <td><?= $total_mahasiswa > 0 ? round(($izin/$total_mahasiswa)*100, 1) : 0 ?>%</td>
                        </tr>
                        <tr class="table-danger">
                            <th>Alfa</th>
                            <td><?= $alfa ?> orang</td>
                            <td><?= $total_mahasiswa > 0 ? round(($alfa/$total_mahasiswa)*100, 1) : 0 ?>%</td>
                        </tr>
                        <tr class="table-secondary">
                            <th>Total</th>
                            <td colspan="2"><strong><?= $total_mahasiswa ?> orang</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <table class="table table-bordered">
    <thead>
        <tr>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Praktikum</th>
            <th>Kelas</th>
            <th>Status</th>
            <th>Keterangan</th>
        </tr>
    </thead>
                    <tbody>
                        <?php foreach ($mahasiswa as $index => $mhs): 
                            $absensi_mhs = array_filter($absensi, function($item) use ($mhs) {
                                return $item['mahasiswa_id'] == $mhs['mahasiswa_id'];
                            });
                            $absensi_data = !empty($absensi_mhs) ? reset($absensi_mhs) : null;
                            
                            $badge_class = [
                                'hadir' => 'bg-success',
                                'sakit' => 'bg-warning',
                                'izin' => 'bg-info', 
                                'alfa' => 'bg-danger',
                                'belum_diisi' => 'bg-secondary'
                            ];
                            $status = $absensi_data['status'] ?? 'belum_diisi';
                            $class = $badge_class[$status] ?? 'bg-secondary';
                        ?>
                        <tr>
                <td><?= $absen['nim'] ?></td>
                <td><?= $absen['nama_mahasiswa'] ?></td>
                <td><?= $absen['nama_praktikum'] ?></td>
                <td><?= $absen['kelas'] ?></td>
                <td><?= $absen['status'] ?></td>
                <td><?= $absen['keterangan'] ?></td>
            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <button onclick="window.print()" class="btn btn-outline-primary">
                    <i class="fas fa-print me-1"></i>Print
                </button>
                <a href="?page=absensi" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Input Absensi
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>