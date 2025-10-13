<?php
require_once "../../config/database.php";
$database = new Database();
$db = $database->getConnection();

$hari = $_GET['hari'] ?? '';
if (!$hari) {
    header("Location: JadwalPraktikum.php");
    exit;
}

$query = "SELECT jp.*, p.nama_praktikum, d.nama as nama_dosen, r.kode_ruangan
          FROM jadwal_praktikum jp
          JOIN praktikum p ON jp.praktikum_id=p.id
          JOIN dosen d ON jp.dosen_id=d.id
          JOIN ruangan r ON jp.ruangan_id=r.id
          WHERE jp.hari=? 
          ORDER BY jp.jam_mulai";
$stmt = $db->prepare($query);
$stmt->execute([$hari]);
$jadwalHari = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Praktikum Hari <?= htmlspecialchars($hari) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5>📅 Jadwal Praktikum Hari <?= htmlspecialchars($hari) ?></h5>
            <a href="JadwalPraktikum.php" class="btn btn-light btn-sm">⬅ Kembali</a>
        </div>
        <div class="card-body">
            <table id="jadwalHariTable" class="table table-striped table-bordered nowrap" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Jam</th>
                        <th>Praktikum</th>
                        <th>Dosen</th>
                        <th>Ruangan</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Kode Absen</th>
                        <th>Waktu Tersisa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwalHari as $i => $row): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= htmlspecialchars($row['jam_mulai']) ?> - <?= htmlspecialchars($row['jam_selesai']) ?></td>
                            <td><?= htmlspecialchars($row['nama_praktikum']) ?></td>
                            <td><?= htmlspecialchars($row['nama_dosen']) ?></td>
                            <td><?= htmlspecialchars($row['kode_ruangan']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td>
                                <span class="badge <?= $row['status']=='active' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($row['kode_random'])): ?>
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($row['kode_random']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark countdown" 
                                      data-end="<?= htmlspecialchars($row['jam_selesai']) ?>"></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    $('#jadwalHariTable').DataTable({
        responsive: true
    });

    // countdown waktu tersisa
    function updateCountdown() {
        $(".countdown").each(function(){
            const end = $(this).data("end");
            if(!end) return;
            const now = new Date();
            const endTime = new Date();
            const [h,m,s] = end.split(":");
            endTime.setHours(h, m, s);
            const diff = endTime - now;
            if(diff > 0) {
                const hours = Math.floor(diff/1000/60/60);
                const minutes = Math.floor((diff/1000/60)%60);
                const seconds = Math.floor((diff/1000)%60);
                $(this).text(`${hours}j ${minutes}m ${seconds}s`);
            } else {
                $(this).text("Expired").removeClass("bg-warning").addClass("bg-danger text-white");
            }
        });
    }
    setInterval(updateCountdown, 1000);
});
</script>

<script>
$(document).ready(function(){
    $(".status-dropdown").change(function(){
        var id = $(this).data("id");
        var status = $(this).val();

        $.ajax({
            url: "../../controllers/JadwalPraktikumController.php",
            type: "POST",
            data: {
                action: "update_status",
                id: id,
                status: status
            },
            success: function(res){
                alert("Status berhasil diperbarui!");
            },
            error: function(){
                alert("Gagal update status!");
            }
        });
    });
});
</script>


</body>
</html>
