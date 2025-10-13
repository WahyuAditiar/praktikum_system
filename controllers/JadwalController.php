<?php
require_once __DIR__ . '/../models/JadwalModel.php';
require_once __DIR__ . '/../models/MataKuliahModel.php';
require_once __DIR__ . '/../models/DosenModel.php';
require_once __DIR__ . '/../models/RuanganModel.php';

class JadwalController {
    private $jadwalModel;
    private $mataKuliahModel;
    private $dosenModel;
    private $ruanganModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->jadwalModel = new JadwalModel($db);
        $this->mataKuliahModel = new MataKuliahModel($db);
        $this->dosenModel = new DosenModel($db);
        $this->ruanganModel = new RuanganModel($db);
    }

 public function createJadwal($data) {



    $errors = [];

    if (empty($data['mata_kuliah_id'])) $errors[] = "Mata kuliah harus dipilih";
    if (empty($data['dosen_id'])) $errors[] = "Dosen harus dipilih";
   if (!isset($data['ruangan_id']) || $data['ruangan_id'] === "") {
    $errors[] = "Ruangan harus dipilih";
}
    if (empty($data['hari'])) $errors[] = "Hari harus dipilih";
    if (empty($data['jam_mulai'])) $errors[] = "Jam mulai harus diisi";
    if (empty($data['jam_selesai'])) $errors[] = "Jam selesai harus diisi";
    if (empty($data['kelas'])) $errors[] = "Kelas harus diisi";

    if (!empty($data['jam_mulai']) && !empty($data['jam_selesai'])) {
        if ($data['jam_mulai'] >= $data['jam_selesai']) {
            $errors[] = "Jam selesai harus setelah jam mulai";
        }
    }

    if (count($errors) > 0) {
        return ['success' => false, 'errors' => $errors];
    }

    // cek bentrok
    if ($this->jadwalModel->checkScheduleConflict($data['ruangan_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'])) {
        $errors[] = "Ruangan sudah digunakan pada jam tersebut";
    }
    //if ($this->jadwalModel->checkDosenAvailability($data['dosen_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'])) {
        //$errors[] = "Dosen sudah memiliki jadwal pada jam tersebut";
   // }

    if (count($errors) > 0) {
        return ['success' => false, 'errors' => $errors];
    }

    // panggil model
    if ($this->jadwalModel->createJadwal(
        $data['mata_kuliah_id'],
        $data['dosen_id'],
        $data['ruangan_id'],
        $data['hari'],
        $data['jam_mulai'],
        $data['jam_selesai'],
        $data['kelas'],
        $data['status'] ?? 'active'
    )) {
        return ['success' => true, 'message' => 'Jadwal berhasil ditambahkan'];
    } else {
        return ['success' => false, 'errors' => ['Gagal menambahkan jadwal']];
    }
 
}


    // Handle update jadwal
    public function updateJadwal($id, $data) {
        $errors = [];

        // Validasi input
        if (empty($data['mata_kuliah_id'])) {
            $errors[] = "Mata kuliah harus dipilih";
        }

        if (empty($data['dosen_id'])) {
            $errors[] = "Dosen harus dipilih";
        }

        if (empty($data['ruangan_id'])) {
            $errors[] = "Ruangan harus dipilih";
        }

        if (empty($data['hari'])) {
            $errors[] = "Hari harus dipilih";
        }

        if (empty($data['jam_mulai'])) {
            $errors[] = "Jam mulai harus diisi";
        }

        if (empty($data['jam_selesai'])) {
            $errors[] = "Jam selesai harus diisi";
        }

        if (empty($data['kelas'])) {
            $errors[] = "Kelas harus diisi";
        }

        // Validasi jam
        if (!empty($data['jam_mulai']) && !empty($data['jam_selesai'])) {
            if ($data['jam_mulai'] >= $data['jam_selesai']) {
                $errors[] = "Jam selesai harus setelah jam mulai";
            }
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check for schedule conflict (exclude current jadwal)
        if ($this->jadwalModel->checkScheduleConflict($data['ruangan_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $id)) {
            $errors[] = "Ruangan sudah digunakan pada jam tersebut";
        }

        // Check dosen availability (exclude current jadwal)
        //if ($this->jadwalModel->checkDosenAvailability($data['dosen_id'], $data['hari'], $data['jam_mulai'], $data['jam_selesai'], $id)) {
         //   $errors[] = "Dosen sudah memiliki jadwal pada jam tersebut";
      //  }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Update jadwal
        if ($this->jadwalModel->updateJadwal(
            $id,
            $data['mata_kuliah_id'], 
            $data['dosen_id'], 
            $data['ruangan_id'], 
            $data['hari'], 
            $data['jam_mulai'], 
            $data['jam_selesai'], 
            $data['kelas'], 
            $data['status'] ?? 'active'
        )) {
            return ['success' => true, 'message' => 'Jadwal berhasil diperbarui'];
        } else {
            return ['success' => false, 'errors' => ['Gagal memperbarui jadwal']];
        }
    }

    // Handle delete jadwal
    public function deleteJadwal($id) {
    try {
        if (empty($id)) {
            return [
                'success' => false,
                'errors' => ['ID tidak boleh kosong']
            ];
        }

       $result = $this->jadwalModel->deleteJadwal($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Jadwal berhasil dihapus'
            ];
        } else {
            return [
                'success' => false,
                'errors' => ['Gagal menghapus jadwal']
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'errors' => [$e->getMessage()]
        ];
    }
    }

    // Get all jadwal
    public function getAllJadwal() {
        return $this->jadwalModel->getAllJadwal();
    }

    // Get jadwal by ID
    public function getJadwalById($id) {
        return $this->jadwalModel->getJadwalById($id);
    }

    // Get jadwal by hari
    public function getJadwalByHari($hari) {
    $query = "SELECT j.*, 
                     mk.kode_mk, mk.nama_mk, 
                     d.nama as nama_dosen, 
                     r.kode_ruangan
              FROM jadwal_kuliah j
              LEFT JOIN mata_kuliah mk ON j.mata_kuliah_id = mk.id
              LEFT JOIN dosen d ON j.dosen_id = d.id
              LEFT JOIN ruangan r ON j.ruangan_id = r.id
              WHERE j.hari = :hari
              ORDER BY j.jam_mulai ASC";

    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':hari', $hari);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Get statistics
    public function getStatistics() {
        return $this->jadwalModel->getCountByStatus();
    }

    // Get statistics by hari
    public function getStatisticsByHari() {
        return $this->jadwalModel->getCountByHari();
    }

    // Get data for dropdowns
    // Get data for dropdowns - PERBAIKAN: gunakan getDosenAktif()
     public function getDropdownData() {
        $mataKuliah = $this->mataKuliahModel->getAllMataKuliah();
        $dosen = $this->dosenModel->getDosenAktif(); // Ubah ke getDosenAktif()
        $ruangan = $this->ruanganModel->getAllRuangan();

        return [
            'mata_kuliah' => $mataKuliah,
            'dosen' => $dosen,
            'ruangan' => $ruangan
        ];
    }

    // Get available waktu based on ruangan and hari
    public function getAvailableWaktu($ruangan_id, $hari, $exclude_id = null) {
        // This would typically query available time slots
        // For simplicity, return fixed time slots
        $timeSlots = [
            '07:00:00', '08:00:00', '09:00:00', '10:00:00', '11:00:00',
            '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00'
        ];
        
        return $timeSlots;
    }

public function updateStatus($id, $status) {
    try {
        $query = "UPDATE jadwal_kuliah SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Status berhasil diperbarui menjadi $status"
            ];
        } else {
            return ['success' => false, 'errors' => 'Gagal update status'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'errors' => $e->getMessage()];
    }
    
}



}

// 🚀 Handler untuk AJAX / POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    $jadwalController = new JadwalController($db);

    if ($_POST['action'] === 'updateStatus') {
        echo json_encode($jadwalController->updateStatus($_POST['id'], $_POST['status']));
    }
}
?>