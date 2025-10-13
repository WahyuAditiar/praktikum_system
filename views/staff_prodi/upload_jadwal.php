<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/JadwalModel.php';
require_once __DIR__ . '/../../controllers/JadwalController.php';
require_once __DIR__ . '/../../libraries/SimpleXLSX.php';

$database = new Database();
$db = $database->getConnection();
$jadwalController = new JadwalController($db);

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']['tmp_name'])) {
    if ($xlsx = \Shuchkin\SimpleXLSX::parse($_FILES['file']['tmp_name'])) {
        foreach ($xlsx->rows() as $index => $row) {
            if ($index == 0) continue; // skip header

            $kode_jadwal = trim($row[0]);
            $nama_mk     = trim($row[1]); // ubah jadi nama mata kuliah
            $hari        = trim($row[2]);
            $jam_mulai   = trim($row[3]);
            $jam_selesai = trim($row[4]);
            $nama_ruangan= trim($row[5]); // ubah jadi nama ruangan
            $nama_dosen  = trim($row[6]); // ubah jadi nama dosen
            $kelas       = trim($row[7]);
            $semester    = trim($row[8]);

            // Debug print
            echo "<pre>";
            echo "Row $index:\n";
            echo "Mata Kuliah: $nama_mk\n";
            echo "Dosen: $nama_dosen\n";
            echo "Ruangan: $nama_ruangan\n";

            // Cari ID mata kuliah berdasarkan nama
            $stmt = $db->prepare("SELECT id FROM mata_kuliah WHERE nama_mk = :nama");
            $stmt->execute([':nama' => $nama_mk]);
            $mk = $stmt->fetch();
            $mata_kuliah_id = $mk['id'] ?? null;
            echo "Mata Kuliah ID: " . ($mata_kuliah_id ?? 'NULL') . "\n";

            // Cari ID dosen berdasarkan nama
            $stmt = $db->prepare("SELECT id FROM dosen WHERE nama = :nama");
            $stmt->execute([':nama' => $nama_dosen]);
            $d = $stmt->fetch();
            $dosen_id = $d['id'] ?? null;
            echo "Dosen ID: " . ($dosen_id ?? 'NULL') . "\n";

            // Cari ID ruangan berdasarkan nama
            $stmt = $db->prepare("SELECT id FROM ruangan WHERE nama_ruangan = :nama");
            $stmt->execute([':nama' => $nama_ruangan]);
            $r = $stmt->fetch();
            $ruangan_id = $r['id'] ?? null;
            echo "Ruangan ID: " . ($ruangan_id ?? 'NULL') . "\n";
            echo "</pre>";

            // Simpan ke database
            $jadwalController->createJadwal([
                'kode_jadwal'    => $kode_jadwal,
                'mata_kuliah_id' => $mata_kuliah_id,
                'dosen_id'       => $dosen_id,
                'ruangan_id'     => $ruangan_id,
                'hari'           => $hari,
                'jam_mulai'      => $jam_mulai,
                'jam_selesai'    => $jam_selesai,
                'kelas'          => $kelas,
                'semester'       => $semester,
                'status'         => 'active'
            ]);
        }

        // redirect ke halaman jadwal
        header("Location: jadwal.php?upload=success");
        exit;
    } else {
        die("Error membaca file: " . \Shuchkin\SimpleXLSX::parseError());
    }
} else {
    die("File tidak ditemukan!");
}
