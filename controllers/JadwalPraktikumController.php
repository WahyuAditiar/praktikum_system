<?php
// controllers/JadwalPraktikumController.php
require_once __DIR__ . '/../models/JadwalPraktikumModel.php';
require_once __DIR__ . '/../models/PraktikumModel.php';
require_once __DIR__ . '/../models/DosenModel.php';
require_once __DIR__ . '/../models/RuanganModel.php';
require_once __DIR__ . '/../config/database.php';

// Handler AJAX generate kode random (harus sebelum deklarasi class)
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) && $_POST['action'] === 'generate_kode' &&
    isset($_POST['jadwal_id'], $_POST['kode_random'])
) {
    $db = (new Database())->getConnection();
    $model = new JadwalPraktikumModel($db);
    $id = (int)$_POST['jadwal_id'];
    $kode = $_POST['kode_random'];
    $result = $model->updateKodeRandom($id, $kode, 60); // 60 menit
    if ($result) {
        echo json_encode(['success' => true, 'kode_random' => $kode]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Gagal update kode random']);
    }
    exit;
}

class JadwalPraktikumController {
    private $model;
    private $praktikumModel;
    private $dosenModel;
    private $ruanganModel;

    public function __construct(PDO $db) {
        $this->model = new JadwalPraktikumModel($db);
        $this->praktikumModel = new PraktikumModel($db);
        $this->dosenModel = new DosenModel($db);
        $this->ruanganModel = new RuanganModel($db);
    }

    public function createJadwal($data) {
        $errors = [];
        if (empty($data['praktikum_id'])) $errors[] = "Praktikum harus dipilih";
        if (empty($data['dosen_id'])) $errors[] = "Dosen harus dipilih";
        if (empty($data['ruangan_id'])) $errors[] = "Ruangan harus dipilih";
        if (empty($data['hari'])) $errors[] = "Hari harus dipilih";
        if (empty($data['jam_mulai'])) $errors[] = "Jam mulai harus diisi";
        if (empty($data['jam_selesai'])) $errors[] = "Jam selesai harus diisi";
        if (empty($data['kelas'])) $errors[] = "Kelas harus diisi";

        if (!empty($data['jam_mulai']) && !empty($data['jam_selesai']) && $data['jam_mulai'] >= $data['jam_selesai']) {
            $errors[] = "Jam selesai harus setelah jam mulai";
        }

        //if ($this->model->checkScheduleConflict($data['ruangan_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'])) {
          //  $errors[] = "Ruangan sudah digunakan pada jam tersebut";
        //}

        //if ($this->model->checkDosenAvailability($data['dosen_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'])) {
          //  $errors[] = "Dosen sudah memiliki jadwal pada jam tersebut";
        //}

        if ($errors) return ['success' => false, 'errors' => $errors];

        $data['kode_random'] = $data['kode_random'] ?? null;
        $data['status'] = $data['status'] ?? 'active';

        if ($this->model->createJadwal($data)) {
            return ['success' => true, 'message' => 'Jadwal praktikum berhasil ditambahkan'];
        }
        return ['success' => false, 'errors' => ['Gagal menambahkan jadwal praktikum']];
    }

    public function updateJadwal($id, $data) {
        $errors = [];
        if (empty($data['praktikum_id'])) $errors[] = "Praktikum harus dipilih";
        if (empty($data['dosen_id'])) $errors[] = "Dosen harus dipilih";
        if (empty($data['ruangan_id'])) $errors[] = "Ruangan harus dipilih";
        if (empty($data['hari'])) $errors[] = "Hari harus dipilih";
        if (empty($data['jam_mulai'])) $errors[] = "Jam mulai harus diisi";
        if (empty($data['jam_selesai'])) $errors[] = "Jam selesai harus diisi";
        if (empty($data['kelas'])) $errors[] = "Kelas harus diisi";

        if (!empty($data['jam_mulai']) && !empty($data['jam_selesai']) && $data['jam_mulai'] >= $data['jam_selesai']) {
            $errors[] = "Jam selesai harus setelah jam mulai";
        }

        if ($this->model->checkScheduleConflict($data['ruangan_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $id)) {
            $errors[] = "Ruangan sudah digunakan pada jam tersebut";
        }

        if ($this->model->checkDosenAvailability($data['dosen_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $id)) {
            $errors[] = "Dosen sudah memiliki jadwal pada jam tersebut";
        }

        if ($errors) return ['success' => false, 'errors' => $errors];

        $data['kode_random'] = $data['kode_random'] ?? null;
        $data['status'] = $data['status'] ?? 'active';

        if ($this->model->updateJadwal($id, $data)) {
            return ['success' => true, 'message' => 'Jadwal praktikum berhasil diperbarui'];
        }
        return ['success' => false, 'errors' => ['Gagal memperbarui jadwal praktikum']];
    }

    public function deleteJadwal($id) {
        if ($this->model->deleteJadwal($id)) return ['success' => true, 'message' => 'Jadwal berhasil dihapus'];
        return ['success' => false, 'errors' => ['Gagal menghapus jadwal']];
    }

    public function getAllJadwal() {
        return $this->model->getAllJadwal();
    }

    public function getJadwalById($id) {
        return $this->model->getJadwalById($id);
    }

    public function getDropdownData() {
        return [
            'praktikum' => $this->praktikumModel->getAllPraktikum(),
            'dosen'     => $this->dosenModel->getAllDosen(),
            'ruangan'   => $this->ruanganModel->getAllRuangan()
        ];
    }

    public function generateKodeForJadwal($id, $minutes = 30) {
        $kode = bin2hex(random_bytes(4)); // 8 chars hex
        $ok = $this->model->updateKodeRandom($id, $kode, $minutes);
        return $ok ? $kode : false;
    }

    public function updateStatus($id, $status) {
        $allowed = ['active', 'canceled', 'completed'];
        if (!in_array($status, $allowed)) {
            return ['success' => false, 'errors' => ['Status tidak valid']];
        }
        $ok = $this->model->updateStatus($id, $status);
        return $ok ? ['success' => true, 'message' => "Status diubah ke $status"] : ['success' => false, 'errors' => ['Gagal update status']];
    }

    public function getStatistics() {
        return $this->model->getCountByStatus();
    }

    public function getStatisticsByHari() {
        return $this->model->getCountByHari();
    }
}

/*
 Handler bottom: hanya jalan jika file ini dipanggil langsung (form action -> controllers/JadwalPraktikumController.php)
 Jika file ini di-include dari view, handler TIDAK dieksekusi.
*/
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    session_start();
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    $controller = new JadwalPraktikumController($db);

    $action = $_POST['action'] ?? $_GET['action'] ?? null;

    if ($action === 'create') {
        $result = $controller->createJadwal($_POST);
        if ($result['success']) $_SESSION['success_message'] = $result['message'];
        else $_SESSION['error_message'] = implode(', ', $result['errors']);
        header("Location: ../views/staff_lab/JadwalPraktikum.php");
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? null;
        $result = $controller->updateJadwal($id, $_POST);
        if ($result['success']) $_SESSION['success_message'] = $result['message'];
        else $_SESSION['error_message'] = implode(', ', $result['errors']);
        header("Location: ../views/staff_lab/JadwalPraktikum.php");
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        $result = $controller->deleteJadwal($id);
        if ($result['success']) $_SESSION['success_message'] = $result['message'];
        else $_SESSION['error_message'] = implode(', ', $result['errors']);
        header("Location: ../views/staff_lab/JadwalPraktikum.php");
        exit;
    }

    if ($action === 'generate_kode') {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error_message'] = 'ID jadwal tidak ditemukan';
            header("Location: ../views/staff_lab/JadwalPraktikum.php");
            exit;
        }
        $kode = $controller->generateKodeForJadwal($id, 30);
        if ($kode) {
            $_SESSION['success_message'] = "Kode berhasil dibuat: {$kode} (kadaluarsa 30 menit jika ada kolom absen_open_until)";
        } else {
            $_SESSION['error_message'] = "Gagal membuat kode";
        }
        header("Location: ../views/staff_lab/JadwalPraktikum.php");
        exit;
    }

    // AJAX updateStatus (kembalikan json)
    if ($action === 'updateStatus') {
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        header('Content-Type: application/json');
        echo json_encode($controller->updateStatus($id, $status));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE jadwal_praktikum SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo "success";
    exit;
}
}
?>
