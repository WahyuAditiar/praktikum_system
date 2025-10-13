<?php
require_once __DIR__ . '/../models/RuanganModel.php';

class RuanganController {
    private $ruanganModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->ruanganModel = new RuanganModel($db);
    }

    // Handle create ruangan
    public function createRuangan($data) {
        $errors = [];

        // Validasi input
        if (empty($data['kode_ruangan'])) {
            $errors[] = "Kode ruangan harus diisi";
        } elseif ($this->ruanganModel->kodeRuanganExists($data['kode_ruangan'])) {
            $errors[] = "Kode ruangan sudah terdaftar";
        }

        if (empty($data['nama_ruangan'])) {
            $errors[] = "Nama ruangan harus diisi";
        }

        if (empty($data['kapasitas']) || !is_numeric($data['kapasitas']) || $data['kapasitas'] <= 0) {
            $errors[] = "Kapasitas harus angka dan lebih dari 0";
        }

        if (empty($data['lokasi'])) {
            $errors[] = "Lokasi harus diisi";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Create ruangan
       if ($this->ruanganModel->createRuangan(
    $data['kode_ruangan'], 
    $data['nama_ruangan'], 
    $data['kapasitas'], 
    $data['lokasi'], 
    isset($data['fasilitas']) ? $data['fasilitas'] : '', 
    $data['status']
)) {
            return ['success' => true, 'message' => 'Data ruangan berhasil ditambahkan'];
        } else {
            return ['success' => false, 'errors' => ['Gagal menambahkan data ruangan']];
        }
    }

    // Handle update ruangan
    public function updateRuangan($id, $data) {
        $errors = [];

        // Validasi input
        if (empty($data['kode_ruangan'])) {
            $errors[] = "Kode ruangan harus diisi";
        } elseif ($this->ruanganModel->kodeRuanganExists($data['kode_ruangan'], $id)) {
            $errors[] = "Kode ruangan sudah terdaftar";
        }

        if (empty($data['nama_ruangan'])) {
            $errors[] = "Nama ruangan harus diisi";
        }

        if (empty($data['kapasitas']) || !is_numeric($data['kapasitas']) || $data['kapasitas'] <= 0) {
            $errors[] = "Kapasitas harus angka dan lebih dari 0";
        }

        if (empty($data['lokasi'])) {
            $errors[] = "Lokasi harus diisi";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Update ruangan
        if ($this->ruanganModel->updateRuangan(
    $id,
    $data['kode_ruangan'], 
    $data['nama_ruangan'], 
    $data['kapasitas'], 
    $data['lokasi'], 
    isset($data['fasilitas']) ? $data['fasilitas'] : '', 
    $data['status']
)) {
            return ['success' => true, 'message' => 'Data ruangan berhasil diperbarui'];
        } else {
            return ['success' => false, 'errors' => ['Gagal memperbarui data ruangan']];
        }
    }

   // Di RuanganController.php
public function deleteRuangan($id) {
    // ... validasi tetap sama ...

    if ($this->ruanganModel->deleteRuangan($id)) {
        return ['success' => true, 'message' => 'Data ruangan berhasil dihapus secara permanen'];
    } else {
        return ['success' => false, 'errors' => ['Gagal menghapus data ruangan']];
    }
}

    // Get all ruangan
    public function getAllRuangan() {
        return $this->ruanganModel->getAllRuangan();
    }

    // Get ruangan by ID
    public function getRuanganById($id) {
        return $this->ruanganModel->getRuanganById($id);
    }

    // Get statistics
    public function getStatistics() {
        return $this->ruanganModel->getCountByStatus();
    }

    // Get total kapasitas
    public function getTotalKapasitas() {
        return $this->ruanganModel->getTotalKapasitas();
    }
}
?>