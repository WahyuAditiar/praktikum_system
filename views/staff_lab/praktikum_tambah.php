<?php
require_once '../../config/config.php';

// koneksi database
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mata_kuliah_id = $_POST['mata_kuliah_id'];
    $nama_praktikum = $_POST['nama_praktikum'];
    $semester       = $_POST['semester'];
    $tahun_ajaran   = $_POST['tahun_ajaran'];
    $status         = $_POST['status'];

    try {
        // Jika user memilih buat MK baru
        if ($mata_kuliah_id === "new" && !empty($_POST['kode_mk_baru']) && !empty($_POST['nama_mk_baru'])) {
            $stmt = $db->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, status) VALUES (?, ?, 'active')");
            $stmt->execute([$_POST['kode_mk_baru'], $_POST['nama_mk_baru']]);
            $mata_kuliah_id = $db->lastInsertId();
        }

        // Insert praktikum
        $stmt = $db->prepare("INSERT INTO praktikum 
    (mata_kuliah_id, nama_praktikum, semester, tahun_ajaran, status, created_at, updated_at) 
    VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->execute([
    $mata_kuliah_id,   // harus valid dari mata_kuliah.id
    $nama_praktikum,
    $semester,
    $tahun_ajaran,
    $status
]);

        header("Location: praktikum.php?success=1");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

var_dump($mata_kuliah_id);
exit;
?>
