<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if (isset($_GET['tahun_ajaran'])) {
    $tahunAjaran = $_GET['tahun_ajaran'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ✅ Query ambil nama praktikum dan kelas (tahun_ajaran ambil dari tabel `praktikum`)
        $sql = "
            SELECT 
                jp.id AS jadwal_id,
                jp.kelas AS kelas,
                p.id AS praktikum_id,
                p.nama_praktikum,
                p.tahun_ajaran
            FROM jadwal_praktikum jp
            INNER JOIN praktikum p ON p.id = jp.praktikum_id
            WHERE p.tahun_ajaran = :tahun_ajaran
            ORDER BY p.nama_praktikum ASC, jp.kelas ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tahun_ajaran', $tahunAjaran);
        $stmt->execute();

        $praktikumList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($praktikumList);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

echo json_encode([]);
