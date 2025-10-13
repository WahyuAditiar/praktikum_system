<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/JadwalModel.php';
require_once __DIR__ . '/../../controllers/JadwalController.php';

checkAuth();
checkRole(['staff_prodi', 'admin']);

$database = new Database();
$db = $database->getConnection();
$jadwalController = new JadwalController($db);

$hari = $_GET['hari'] ?? '';
$jadwalData = $hari ? $jadwalController->getJadwalByHari($hari) : [];

date_default_timezone_set("Asia/Jakarta");
?>

<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="content-wrapper" style="margin-left:0;">
  <!-- Header -->
  <div class="content-header text-white p-4 rounded-bottom shadow-lg"
    style="background: linear-gradient(135deg, #007bff, #0056b3);">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <!-- Judul -->
      <div>
        <h2 class="m-0 fw-bold">
          <i class="fas fa-calendar-day me-2"></i>
          Jadwal Hari: <?= htmlspecialchars($hari) ?>
        </h2>
      </div>

      <!-- Tanggal & Jam -->
      <div id="datetime" class="text-end mt-2 mt-md-0 fs-5 fw-bold"></div>
    </div>

    <!-- Tombol kembali -->
    <div class="mt-3">

    </div>
  </div>

  <!-- Konten -->
  <section class="content mt-3">
    <div class="container-fluid">
      <div class="row">
        <!-- Panel Jadwal -->
        <div class="col-md-8">
          <div class="card shadow-sm">
            <div class="card-header bg-info text-white fw-bold">
              <i class="fas fa-list"></i> Daftar Jadwal <?= htmlspecialchars($hari) ?>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Jam</th>
                    <th>Mata Kuliah</th>
                    <th>Dosen</th>
                    <th>Ruangan</th>
                    <th>Kelas</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($jadwalData)): ?>
                    <tr>
                      <td colspan="7" class="text-center">Tidak ada jadwal</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($jadwalData as $i => $jadwal): ?>
                      <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?></td>
                        <td><?= htmlspecialchars($jadwal['kode_mk']) . ' - ' . htmlspecialchars($jadwal['nama_mk']) ?></td>
                        <td><?= htmlspecialchars($jadwal['nama_dosen']) ?></td>
                        <td><?= htmlspecialchars($jadwal['kode_ruangan']) ?></td>
                        <td><?= htmlspecialchars($jadwal['kelas']) ?></td>
                        <td>
                          <?php if ($jadwal['status'] == 'active'): ?>
                            <span class="badge bg-success">Aktif</span>
                          <?php elseif ($jadwal['status'] == 'canceled'): ?>
                            <span class="badge bg-danger">Batal</span>
                          <?php else: ?>
                            <span class="badge bg-secondary">Selesai</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Panel Info Kampus -->
        <div class="col-md-4">
          <div class="mb-2 text-start">
            <a href="jadwal.php" class="btn btn-warning btn-sm fw-bold shadow-sm">
              <i class="fas fa-arrow-right"></i> Kembali
            </a>
          </div>

          <div class="card shadow-sm mb-3">
            <div class="card-header bg-success text-white fw-bold">
              <i class="fas fa-bullhorn"></i> Info Kampus
            </div>
            <div class="card-body text-center">
              <h6 class="mb-2">Instagram</h6>
              <div style="max-width:100%; margin:0 auto;">
                <blockquote class="instagram-media"
                  data-instgrm-permalink="https://www.instagram.com/informatika.up/"
                  data-instgrm-version="12"
                  style="background:#FFF; border:0; border-radius:3px;
                  box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15);
                  margin:1px auto; max-width:100% !important; width:100% !important; min-width:auto !important;
                  height:300px; overflow:hidden;">
                </blockquote>
              </div>
              <script async src="https://www.instagram.com/embed.js"></script>
            </div>

            <div class="card-body text-center border-top">
              <h6 class="mb-2">YouTube</h6>

              <!-- YouTube Embed Start -->
              <div class="iframely-embed">
                <div class="iframely-responsive" style="height: 140px; padding-bottom: 0;">
                  <a href="https://www.youtube.com/channel/UC1sWpqyWeyYyigwtUuY9OiA" data-iframely-url="//iframely.net/DV5Sb6tH?theme=light"></a>
                </div>
              </div>
              <script async src="//iframely.net/embed.js"></script>
              <!-- YouTube Embed End -->
            </div>
          </div>
        </div>
      </div>

      <!-- Teks Berjalan -->
      <div class="mt-3">
        <marquee behavior="scroll" direction="left" class="fw-bold text-primary">
          Selamat datang di Sistem Kolaborasi Prodi & Laboratorium Teknik Informatika Fakultas Teknik Universitas Pancasila
          | Tetap Semangat Belajar! | https://teknik.univpancasila.ac.id/informatika || <?= date('Y'); ?>
        </marquee>
      </div>
    </div>
  </section>
</div>

<script>
  function updateDateTime() {
    const now = new Date();
    const optionsDate = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };
    const tanggal = now.toLocaleDateString('id-ID', optionsDate);
    const jam = now.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });

    document.getElementById("datetime").innerHTML = `
      <span class="fs-4 fw-bold"><i class="fas fa-calendar"></i> ${tanggal}</span> 
      <br> 
      <span class="badge bg-dark fs-5 p-2"><i class="fas fa-clock"></i> ${jam} WIB</span>
    `;
  }
  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>