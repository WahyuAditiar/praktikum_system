<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/MataKuliahModel.php';
require_once __DIR__ . '/../../controllers/MataKuliahController.php';
require_once __DIR__ . '/../../libraries/SimpleXLSX.php';

$database = new Database();
$db = $database->getConnection();
$mataKuliahController = new MataKuliahController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']['tmp_name'])) {
    if ($xlsx = \Shuchkin\SimpleXLSX::parse($_FILES['file']['tmp_name'])) {
        foreach ($xlsx->rows() as $index => $row) {
            if ($index == 0) continue; // skip header

            $kode       = trim($row[0]);
            $nama       = trim($row[1]);
            $sks        = trim($row[2]);
            $semester   = trim($row[3]);
            $jurusan    = trim($row[4]);
            $deskripsi  = trim($row[5]);
            $status     = trim($row[6]);

            // Simpan ke database
            $mataKuliahController->createMataKuliah([
                'kode_mk'   => $kode,
                'nama_mk'   => $nama,
                'sks'       => $sks,
                'semester'  => $semester,
                'jurusan'   => $jurusan,
                'deskripsi' => $deskripsi,
                'status'    => $status
            ]);
        }

        // Redirect balik ke halaman matakuliah.php dengan pesan sukses
        header("Location: matakuliah.php?upload=success");
        exit;
    } else {
        die("Error membaca file: " . SimpleXLSX::parseError());
    }
} else {
    die("File tidak ditemukan!");
}


exit;
?>
