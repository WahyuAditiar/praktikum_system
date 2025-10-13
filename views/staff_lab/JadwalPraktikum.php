<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/JadwalPraktikumController.php';
require_once __DIR__ . '/../../helpers/auth.php';

checkAuth(['admin','staff_lab','staff_prodi']);



$database = new Database();
$db = $database->getConnection();
$controller = new JadwalPraktikumController($db);



// bila ada edit request
$editJadwal = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editJadwal = $controller->getJadwalById((int)$_GET['edit']);
}


// Handler untuk update status via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $res = $controller->updateStatus($id, $status);
    echo json_encode($res);
    exit; // penting, biar gak lanjut render HTML
}




// ambil data untuk list + dropdown + statistik
$jadwalAll  = $controller->getAllJadwal();
$statStatus = $controller->getStatistics();
$statHari   = $controller->getStatisticsByHari();
$dropdown   = $controller->getDropdownData();
?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>




<div class="content-wrapper">
    <div class="container-fluid mt-3">
    <h4 class="mb-3">Manajemen Jadwal Praktikum</h4>

        <!-- FLASH MESSAGES -->
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Statistik singkat -->
         <div class="row mb-3">
        <div class="col-6 col-md-3">
            <div class="card bg-info text-white p-3 mb-2">
                <h6>Total Jadwal</h6>
                <h3><?= count($jadwalAll) ?></h3>
            </div>
        </div>
           <div class="col-6 col-md-3">
            <div class="card bg-success text-white p-3 mb-2">
                <h5>Jadwal Aktif</h5>
                    <?php $aktif = 0; foreach($statStatus as $s){ if(($s['status'] ?? '')==='active') $aktif += (int)$s['total']; } ?>
                    <h3><?= $aktif ?></h3>
            </div>
        </div>
            <div class="col-6 col-md-3">
            <div class="card bg-warning text-white p-3 mb-2">
                <h6>Praktikum Tersedia</h6>
                <h3><?= count($dropdown['praktikum'] ?? []) ?></h3>
            </div>
        </div>

            <div class="col-6 col-md-3">
            <div class="card bg-dark text-white p-3 mb-2">
                <h6>Dosen</h6>
                <h3><?= count($dropdown['dosen'] ?? []) ?></h3>
            </div>
        </div>
    </div>

    <div class="row">
    <?php 
    $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $warna = ['primary','success','warning','danger','info','dark'];
    foreach ($hariList as $i => $hari): ?>
        <div class="col-md-2 col-6 mb-3">
            <a href="jadwal_perhari_praktikum.php?hari=<?= urlencode($hari) ?>" class="text-decoration-none">
                <div class="card text-white bg-<?= $warna[$i] ?> text-center p-3 shadow-sm" style="cursor:pointer;">
                    <h5><?= $hari ?></h5>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

        <div class="row">
            <!-- kolom form -->
            <div class="row">
        <!-- Kolom form -->
        <div class="col-12 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header <?= $editJadwal ? 'bg-warning' : 'bg-primary' ?> text-white">
                    <?= $editJadwal ? 'Edit Jadwal' : 'Tambah Jadwal Baru' ?>
                </div>
                <div class="card-body">
                        <form method="POST" action="../../controllers/JadwalPraktikumController.php">
                            <?php if ($editJadwal): ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($editJadwal['id']) ?>">
                            <?php else: ?>
                                <input type="hidden" name="action" value="create">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label>Praktikum *</label>
                                <select name="praktikum_id" class="form-control" required>
                                    <option value="">Pilih Praktikum</option>
                                    <?php foreach($dropdown['praktikum'] as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= $editJadwal && $editJadwal['praktikum_id']==$p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['nama_praktikum']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Dosen Pengajar *</label>
                                <select name="dosen_id" class="form-control" required>
                                    <option value="">Pilih Dosen</option>
                                    <?php foreach($dropdown['dosen'] as $d): ?>
                                        <option value="<?= $d['id'] ?>" <?= $editJadwal && $editJadwal['dosen_id']==$d['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($d['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Ruangan *</label>
                                <select name="ruangan_id" class="form-control" required>
                                    <option value="">Pilih Ruangan</option>
                                    <?php foreach($dropdown['ruangan'] as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= $editJadwal && $editJadwal['ruangan_id']==$r['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($r['kode_ruangan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Hari *</label>
                                <?php $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']; ?>
                                <select name="hari" class="form-control" required>
                                    <option value="">Pilih Hari</option>
                                    <?php foreach($hariList as $h): ?>
                                        <option value="<?= $h ?>" <?= $editJadwal && $editJadwal['hari']==$h ? 'selected' : '' ?>><?= $h ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
    <label>Jam Mulai *</label>
    <select name="jam_mulai" class="form-control" required>
        <option value="">Pilih Jam Mulai</option>
        <option value="08:00:00">08.00</option>
        <option value="08:50:00">08.50</option>
        <option value="09:40:00">09.40</option>
        <option value="10:30:00">10.30</option>
        <option value="11:20:00">11.20</option>
        <option value="12:10:00">12.10</option>
        <option value="13:00:00">13.00</option>
        <option value="13:50:00">13.50</option>
        <option value="14:40:00">14.40</option>
        <option value="15:30:00">15.30</option>
        <option value="16:20:00">16.20</option>
        <option value="17:10:00">17.10</option>
    </select>
</div>

<div class="mb-3">
    <label>Jam Selesai *</label>
    <select name="jam_selesai" class="form-control" required>
        <option value="">Pilih Jam Selesai</option>
        <option value="08:50:00">08.50</option>
        <option value="09:40:00">09.40</option>
        <option value="10:30:00">10.30</option>
        <option value="11:20:00">11.20</option>
        <option value="12:10:00">12.10</option>
        <option value="13:00:00">13.00</option>
        <option value="13:50:00">13.50</option>
        <option value="14:40:00">14.40</option>
        <option value="15:30:00">15.30</option>
        <option value="16:20:00">16.20</option>
        <option value="17:10:00">17.10</option>
        <option value="18:00:00">18.00</option>
    </select>
</div>

                            <div class="mb-3">
    <label>Kelas *</label>
    <select name="kelas" class="form-control" required>
        <option value="">Pilih Kelas</option>
        <option value="A">Kelas A</option>
        <option value="B">Kelas B</option>
        <option value="C">Kelas C</option>
        <option value="D">Kelas D</option>
        <option value="E">Kelas E</option>
    </select>
</div>

                            <div class="d-grid">
                                <button class="btn <?= $editJadwal ? 'btn-warning' : 'btn-success' ?>" type="submit">
                                    <?= $editJadwal ? 'Update Jadwal' : 'Simpan' ?>
                                </button>
                                <?php if ($editJadwal): ?>
                                    <a href="JadwalPraktikum.php" class="btn btn-secondary mt-2">Batal</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- kolom tabel -->
            <div class="col-md-8">
    <div class="card">
        <div class="card-header">Daftar Jadwal Praktikum</div>
        <div class="card-body">
            <!-- Pesan sukses/error -->
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <button id="exportPdfBtn" class="btn btn-danger mb-2">📄 Export PDF</button>

            <!-- Tambah wrapper table-responsive -->
            <div class="table-responsive">
                 <table id="jadwalTable" class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                                    <th>#</th>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Praktikum</th>
                                    <th>Dosen</th>
                                    <th>Ruangan</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Kode Absen</th>
                                    <th>Waktu Tersisa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($jadwalAll)): $no=1; foreach($jadwalAll as $j): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($j['hari']) ?></td>
                                    <td><?= htmlspecialchars($j['jam_mulai'].' - '.$j['jam_selesai']) ?></td>
                                    <td><?= htmlspecialchars($j['nama_praktikum']) ?></td>
                                    <td><?= htmlspecialchars($j['nama_dosen']) ?></td>
                                    <td><?= htmlspecialchars($j['kode_ruangan']) ?></td>
                                    <td><?= htmlspecialchars($j['kelas']) ?></td>
                                    <td class="status-cell" data-id="<?= $j['id'] ?>" data-status="<?= $j['status'] ?>">
                                    <span class="status-text"><?= ucfirst($j['status']) ?></span>
                                    </td>
                                    <td>
    <?php if (!empty($j['kode_random'])): ?>
        <span class="badge bg-info" id="kode_<?= $j['id'] ?>"><?= htmlspecialchars($j['kode_random']) ?></span>
    <?php else: ?>
        <span class="text-muted" id="kode_<?= $j['id'] ?>">Belum dibuat</span>
    <?php endif; ?>
</td>
<td>
    <span class="badge bg-success" id="waktu_<?= $j['id'] ?>">
        <?php
        if (!empty($j['absen_open_until'])) {
            $now = new DateTime();
            $until = new DateTime($j['absen_open_until']);
            $diff = $until->getTimestamp() - $now->getTimestamp();
            if ($diff > 0) {
                $menit = floor($diff / 60);
                $detik = $diff % 60;
                echo $menit . 'm ' . $detik . 's';
            } else {
                echo '-';
            }
        } else {
            echo '-';
        }
        ?>
    </span>
</td>
                                    <td>
    <div class="btn-group" role="group">
        <!-- Tombol edit -->
        <a href="?edit=<?= $j['id'] ?>" class="btn btn-sm btn-warning" title="Edit Jadwal"><i class="fas fa-pencil-alt"></i></a>
        <!-- Tombol hapus -->
        <a href="?delete=<?= $j['id'] ?>" class="btn btn-sm btn-danger" title="Hapus Jadwal" onclick="return confirm('Hapus jadwal ini?')"><i class="fas fa-trash"></i></a>
        <!-- Tombol generate kode random (ikon kunci) -->
        <button class="btn btn-sm btn-info" title="Generate Kode Absen" onclick="generateKodeAbsen(<?= $j['id'] ?>)"><i class="fas fa-key"></i></button>
    </div>
</td>
                                    </td>
                                    <td>
    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="9" class="text-center">Belum ada jadwal</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<?php include __DIR__ . '/../templates/footer.php'; ?>


<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on("click", ".status-cell", function() {
    let td = $(this);
    if (td.find("select").length) return; // kalau sudah select, jangan render lagi

    let currentStatus = td.data("status");
    let id = td.data("id");

    let select = `
      <select class="form-select form-select-sm status-select">
        <option value="active" ${currentStatus=='active'?'selected':''}>Active</option>
        <option value="canceled" ${currentStatus=='canceled'?'selected':''}>Canceled</option>
        <option value="completed" ${currentStatus=='completed'?'selected':''}>Completed</option>
      </select>
    `;
    td.html(select);

    let sel = td.find(".status-select").focus();

    // jika user ganti pilihan
    sel.change(function(){
        let newStatus = $(this).val();
        $.post("", {update_status: 1, id: id, status: newStatus}, function(res){
            let json = {};
            try { json = JSON.parse(res); } catch(e) {}
            if (json.success) {
                td.html(`<span class="status-text">${newStatus.charAt(0).toUpperCase()+newStatus.slice(1)}</span>`);
                td.data("status", newStatus);
            } else {
                alert("Gagal update status");
                td.html(`<span class="status-text">${currentStatus}</span>`);
            }
        });
    });

    // jika user tekan ESC -> batal
    sel.keyup(function(e){
        if (e.key === "Escape") {
            td.html(`<span class="status-text">${currentStatus}</span>`);
        }
    });
});
</script>

<script>
function startCountdown() {
    const elements = document.querySelectorAll('.countdown');
    elements.forEach(el => {
        const targetTime = new Date(el.dataset.time).getTime();

        function update() {
            const now = new Date().getTime();
            const distance = targetTime - now;

            if (distance <= 0) {
                el.innerHTML = "<span class='badge bg-danger'>Expired</span>";
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            el.innerHTML = `<span class="badge bg-success">${minutes}m ${seconds}s</span>`;
            setTimeout(update, 1000);
        }

        update();
    });
}
document.addEventListener("DOMContentLoaded", startCountdown);
</script>

<script>
$(document).ready(function() {
    $('#jadwalTable').DataTable({
        responsive: true,
        pageLength: 10,
        ordering: true,
        language: {
            search: "🔍 Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                next: "›",
                previous: "‹"
            }
        }
    });
});
</script>


<script>
    // Tombol Export PDF
    $("#exportPdfBtn").click(function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.text("Daftar Jadwal Praktikum", 14, 15);
        doc.autoTable({
            startY: 20,
            html: '#jadwalTable',
            theme: 'grid',
            styles: { fontSize: 8 }
        });

        doc.save("jadwal_praktikum.pdf");
    });

</script>

<script>
$(document).ready(function() {
    $('#jadwalTable').DataTable({
        responsive: true,
        scrollX: true, // biar bisa geser kalau kolom terlalu panjang
        autoWidth: false
    });
});
</script>

<style>
#jadwalTable {
    font-size: 0.95rem;
    background: #fff;
}
#jadwalTable th, #jadwalTable td {
    vertical-align: middle !important;
    text-align: center;
    padding: 8px 6px;
    white-space: nowrap;
}
#jadwalTable th {
    background: #e3f0ff;
    position: sticky;
    top: 0;
    z-index: 2;
}
#jadwalTable td {
    background: #f8f9fa;
}
#jadwalTable .btn-group .btn {
    margin-right: 2px;
}
#jadwalTable .badge {
    font-size: 0.95em;
    padding: 6px 10px;
}
.table-responsive {
    overflow-x: auto;
}
</style>

<script>
function generateKodeAbsen(jadwalId) {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let kode = '';
    for (let i = 0; i < 6; i++) {
        kode += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    // Kirim AJAX ke backend untuk update kode_random
    fetch('../../controllers/JadwalPraktikumController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=generate_kode&jadwal_id=' + encodeURIComponent(jadwalId) + '&kode_random=' + encodeURIComponent(kode)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('kode_' + jadwalId).outerHTML = '<span class="badge bg-info" id="kode_' + jadwalId + '">' + kode + '</span>';
            // Update waktu tersisa ke 60m 0s
            document.getElementById('waktu_' + jadwalId).innerHTML = '60m 0s';
        } else {
            alert('Gagal generate kode: ' + (data.error || 'Unknown error'));
        }
    });
}
</script>

