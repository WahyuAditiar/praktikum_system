<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['praktikum_id'])) {
    echo json_encode(['success' => false, 'message' => 'praktikum_id tidak dikirim']);
    exit;
}

$praktikum_id = intval($_GET['praktikum_id']);

try {
    // Ambil kelas terakhir dari tabel praktikum
    $stmt = $pdo->prepare("SELECT kelas FROM praktikum WHERE id = :id");
    $stmt->execute(['id' => $praktikum_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && !empty($result['kelas'])) {
        $currentKelas = strtoupper(trim($result['kelas']));
        // Hitung kelas berikutnya (A → B → C ...)
        $nextKelas = chr(ord($currentKelas) + 1);
        echo json_encode(['success' => true, 'kelas' => $nextKelas]);
    } else {
        echo json_encode(['success' => false, 'kelas' => 'A']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
