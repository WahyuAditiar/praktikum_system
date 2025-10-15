<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/absensiController.php';

if (!in_array($_SESSION['role'] ?? '', ['staff_lab', 'admin'])) {
    die('Access denied');
}

$jadwal_id = $_GET['jadwal_id'] ?? $_POST['jadwal_id'] ?? '';
$praktikum = $_GET['praktikum'] ?? $_POST['praktikum'] ?? '';
$kelas = $_GET['kelas'] ?? $_POST['kelas'] ?? '';
$semester = $_GET['semester'] ?? $_POST['semester'] ?? 'Ganjil'; // Default value
$tahun_ajaran = $_GET['tahun_ajaran'] ?? $_POST['tahun_ajaran'] ?? date('Y') . '/' . (date('Y') + 1); // Default value

if (!$jadwal_id) {
    die('Parameter tidak lengkap. Jadwal ID diperlukan.');
}

// Get data dari database
$database = new Database();
$db = $database->getConnection();
$controller = new AbsensiController($db, $_SESSION['role']);

$mahasiswa = $controller->getMahasiswaByJadwal($jadwal_id);
$detail_jadwal = $controller->getDetailJadwal($jadwal_id);

// Ambil data semester dan tahun_ajaran dari detail_jadwal jika ada
if ($detail_jadwal) {
    $semester = $detail_jadwal['semester'] ?? $semester;
    $tahun_ajaran = $detail_jadwal['tahun_ajaran'] ?? $tahun_ajaran;
}

// Ambil data absensi untuk semua pertemuan (1-14)
$absensi_all_pertemuan = [];
for ($pertemuan = 1; $pertemuan <= 14; $pertemuan++) {
    $absensi = $controller->getAbsensi($jadwal_id, $pertemuan);
    $absensi_all_pertemuan[$pertemuan] = $absensi;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Praktikum - <?= htmlspecialchars($praktikum) ?> Kelas <?= $kelas ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 10px; }
            .container { max-width: 100% !important; }
            .table { font-size: 10px; }
            .header { margin-bottom: 10px !important; }
            .page-break { page-break-after: always; }
        }
        @media screen {
            body { padding: 20px; background-color: #f8f9fa; }
            .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        }
        body { font-family: 'Arial', sans-serif; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; font-weight: bold; margin: 0; text-align: center; }
        .header h2 { font-size: 14px; color: #666; margin: 3px 0; text-align: center; }
        .header h3 { font-size: 12px; color: #888; margin: 0; text-align: center; }
        .table th { background-color: #f8f9fa; font-weight: bold; border: 1px solid #dee2e6; text-align: center; padding: 4px; }
        .table td { border: 1px solid #dee2e6; padding: 4px; }
        .table .text-start { text-align: left; }
        .table .text-center { text-align: center; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 10px; color: #666; }
        .status-h { background-color: #d4edda; color: #155724; font-weight: bold; }
        .status-i { background-color: #fff3cd; color: #856404; font-weight: bold; }
        .status-s { background-color: #d1ecf1; color: #0c5460; font-weight: bold; }
        .status-a { background-color: #f8d7da; color: #721c24; font-weight: bold; }
        .ttd-section { margin-top: 30px; }
        .ttd-line { border-top: 1px solid #000; width: 200px; margin-top: 60px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>DAFTAR HADIR PRAKTIKUM</h1>
            <h2>Semester: <?= htmlspecialchars($semester) ?> - Tahun Ajaran: <?= htmlspecialchars($tahun_ajaran) ?></h2>
            <h3>Mata Praktikum: <?= htmlspecialchars($praktikum) ?> <?= htmlspecialchars($kelas) ?></h3>
        </div>

        <!-- Tabel Absensi -->
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th width="4%" class="text-center">No.</th>
                        <th width="12%">NIM</th>
                        <th width="25%">Nama Lengkap</th>
                        <?php for ($i = 1; $i <= 14; $i++): ?>
                            <th width="3%" class="text-center"><?= $i ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($mahasiswa as $mhs): ?>
                    <tr>
                        <td class="text-center"><?= $no ?></td>
                        <td><strong><?= htmlspecialchars($mhs['nim']) ?></strong></td>
                        <td class="text-start"><?= htmlspecialchars($mhs['nama']) ?></td>
                        <?php for ($pertemuan = 1; $pertemuan <= 14; $pertemuan++): 
                            $absen = array_filter($absensi_all_pertemuan[$pertemuan], fn($a) => $a['mahasiswa_id'] == $mhs['id']);
                            $data = $absen ? reset($absen) : null;
                            $status = $data['status'] ?? '';
                            
                            // Tentukan kode dan class untuk status
                            $kode_status = '';
                            $status_class = '';
                            
                            switch($status) {
                                case 'hadir':
                                    $kode_status = 'H';
                                    $status_class = 'status-h';
                                    break;
                                case 'sakit':
                                    $kode_status = 'S';
                                    $status_class = 'status-s';
                                    break;
                                case 'izin':
                                    $kode_status = 'I';
                                    $status_class = 'status-i';
                                    break;
                                case 'alfa':
                                    $kode_status = 'A';
                                    $status_class = 'status-a';
                                    break;
                                default:
                                    $kode_status = '';
                                    $status_class = '';
                                    break;
                            }
                        ?>
                        <td class="text-center <?= $status_class ?>">
                            <?= $kode_status ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php $no++; endforeach; ?>
                    
                    <!-- Tambahan baris kosong jika diperlukan -->
                    <?php for ($i = count($mahasiswa) + 1; $i <= count($mahasiswa) + 5; $i++): ?>
                    <tr>
                        <td class="text-center"><?= $i ?></td>
                        <td></td>
                        <td></td>
                        <?php for ($j = 1; $j <= 14; $j++): ?>
                        <td></td>
                        <?php endfor; ?>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- Keterangan -->
        <div class="mt-3" style="font-size: 11px;">
            <strong>Keterangan:</strong><br>
            <strong>H</strong> : Hadir &nbsp;&nbsp; 
            <strong>S</strong> : Sakit &nbsp;&nbsp; 
            <strong>I</strong> : Izin &nbsp;&nbsp; 
            <strong>A</strong> : Alfa
        </div>

        <!-- Tanda Tangan Dosen -->
        <div class="ttd-section no-print">
            <div class="row justify-content-end">
                <div class="col-md-4 text-center">
                    <div style="margin-bottom: 80px;">
                        <div>.........................................</div>
                        <div><strong>Dosen Pengampu</strong></div>
                        <div style="margin-top: 50px;">(...................................................)</div>
                        <div>NIDN: ...................................</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="row">
                <div class="col-md-6">
                    <strong>Total Mahasiswa:</strong> <?= count($mahasiswa) ?> orang
                </div>
                <div class="col-md-6 text-end">
                    <small>Dicetak pada: <?= date('d/m/Y H:i:s') ?></small><br>
                    <small>Oleh: <?= $_SESSION['nama'] ?? 'Staff Lab' ?></small>
                </div>
            </div>
        </div>

        <!-- Tombol Print -->
        <div class="no-print text-center mt-4">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print me-2"></i>Print / Save as PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm ms-2">
                <i class="fas fa-times me-2"></i>Tutup
            </button>
            <p class="text-muted mt-2" style="font-size: 12px;">
                Gunakan fitur "Print" di browser dan pilih "Save as PDF" untuk menyimpan sebagai file PDF
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>