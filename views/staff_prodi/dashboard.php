<?php
require_once __DIR__ . '/../../config/config.php';
checkAuth();
checkRole(['staff_prodi', 'admin']);

$page_title = "Dashboard Staff Program Studi";
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #clock {
        font-size: 1.2rem;
        font-weight: bold;
        color: #007bff;
    }
    #date {
        font-size: 0.9rem;
        color: #555;
        display: block;
    }
    .small-box {
        border-radius: 12px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        transition: transform 0.2s;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .list-group-item {
        border: none;
        border-bottom: 1px solid #f1f1f1;
    }
    /* Marquee */
    .marquee {
        background: #007bff;
        color: #fff;
        padding: 10px;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
    }
    .marquee span {
        display: inline-block;
        padding-left: 100%;
        animation: marquee 15s linear infinite;
    }
    @keyframes marquee {
        0% { transform: translate(0, 0); }
        100% { transform: translate(-100%, 0); }
    }
</style>

<div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 dashboard-header">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard Staff Program Studi</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <span id="date"></span>
                    <span id="clock"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info Box -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>25</h3>
                            <p>Dosen</p>
                        </div>
                        <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <a href="dosen.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>10</h3>
                            <p>Ruangan</p>
                        </div>
                        <div class="icon"><i class="fas fa-door-open"></i></div>
                        <a href="ruangan.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>50</h3>
                            <p>Mata Kuliah</p>
                        </div>
                        <div class="icon"><i class="fas fa-book"></i></div>
                        <a href="matakuliah.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>100</h3>
                            <p>Jadwal Kuliah</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="jadwal.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title"><i class="fas fa-th mr-2"></i> Menu Staff Program Studi</h3>
                        </div>
                        <div class="card-body text-center">
                            <a href="dosen.php" class="btn btn-info btn-lg m-2">
                                <i class="fas fa-chalkboard-teacher mr-2"></i> Dosen
                            </a>
                            <a href="ruangan.php" class="btn btn-success btn-lg m-2">
                                <i class="fas fa-door-open mr-2"></i> Ruangan
                            </a>
                            <a href="matakuliah.php" class="btn btn-warning btn-lg m-2">
                                <i class="fas fa-book mr-2"></i> Mata Kuliah
                            </a>
                            <a href="jadwal.php" class="btn btn-danger btn-lg m-2">
                                <i class="fas fa-calendar-alt mr-2"></i> Jadwal
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Statistik -->
            <div class="row mt-4">
                <!-- Aktivitas -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title"><i class="fas fa-history mr-2"></i> Aktivitas Terbaru</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item"><i class="fas fa-plus-circle text-primary mr-2"></i> Jadwal baru ditambahkan - Matematika Diskrit</li>
                                <li class="list-group-item"><i class="fas fa-user-edit text-success mr-2"></i> Data dosen diperbarui - Dr. Ahmad</li>
                                <li class="list-group-item"><i class="fas fa-tools text-warning mr-2"></i> Ruangan Lab 301 sedang maintenance</li>
                                <li class="list-group-item"><i class="fas fa-book-open text-info mr-2"></i> Mata kuliah baru - Pemrograman Web</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Statistik -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i> Statistik</h3>
                        </div>
                        <div class="card-body">
                            <div class="progress-group">
                                Dosen Aktif
                                <span class="float-right"><b>25</b>/25</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            </div>
                            <div class="progress-group mt-3">
                                Ruangan Tersedia
                                <span class="float-right"><b>8</b>/10</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: 80%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marquee Selamat Datang -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="marquee">
                        <span>✨ Selamat datang di Sistem Praktikum - Dashboard Staff Program Studi ✨</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Jam & Tanggal realtime
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('clock').textContent = time;
        document.getElementById('date').textContent = date;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>
