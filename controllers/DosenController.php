<?php
require_once __DIR__ . '/../models/DosenModel.php';

class DosenController {
    private $dosenModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->dosenModel = new DosenModel($db);
    }

    // Handle create dosen
    public function createDosen($data) {
        $errors = [];

        // Validasi input
        if (empty($data['nidn'])) {
            $errors[] = "NIDN harus diisi";
        } elseif (!preg_match('/^[0-9]{10}$/', $data['nidn'])) {
            $errors[] = "NIDN harus 10 digit angka";
        } elseif ($this->dosenModel->nidnExists($data['nidn'])) {
            $errors[] = "NIDN sudah terdaftar";
        }

        if (empty($data['nama'])) {
            $errors[] = "Nama dosen harus diisi";
        }

        if (empty($data['hp'])) {
            $errors[] = "Nomor HP harus diisi";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Create dosen
        if ($this->dosenModel->createDosen($data['nidn'], $data['nama'], $data['hp'], $data['status'])) {
            return ['success' => true, 'message' => 'Data dosen berhasil ditambahkan'];
        } else {
            return ['success' => false, 'errors' => ['Gagal menambahkan data dosen']];
        }
    }

    // Handle update dosen
    public function updateDosen($id, $data) {
        $errors = [];

        // Validasi input
        if (empty($data['nidn'])) {
            $errors[] = "NIDN harus diisi";
        } elseif (!preg_match('/^[0-9]{10}$/', $data['nidn'])) {
            $errors[] = "NIDN harus 10 digit angka";
        } elseif ($this->dosenModel->nidnExists($data['nidn'], $id)) {
            $errors[] = "NIDN sudah terdaftar";
        }

        if (empty($data['nama'])) {
            $errors[] = "Nama dosen harus diisi";
        }

        if (empty($data['hp'])) {
            $errors[] = "Nomor HP harus diisi";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Update dosen
        if ($this->dosenModel->updateDosen($id, $data['nidn'], $data['nama'], $data['hp'], $data['status'])) {
            return ['success' => true, 'message' => 'Data dosen berhasil diperbarui'];
        } else {
            return ['success' => false, 'errors' => ['Gagal memperbarui data dosen']];
        }
    }

    // Handle delete dosen
   public function deleteDosen($id) {
    // ... validasi tetap sama ...

    // Delete dosen
    if ($this->dosenModel->deleteDosen($id)) {
        return ['success' => true, 'message' => 'Data dosen berhasil dihapus secara permanen'];
    } else {
        return ['success' => false, 'errors' => ['Gagal menghapus data dosen']];
    }
}

    // Get all dosen
    public function getAllDosen() {
        return $this->dosenModel->getAllDosen();
    }

    // Get dosen by ID
    public function getDosenById($id) {
        return $this->dosenModel->getDosenById($id);
    }

    // Get statistics
    public function getStatistics() {
        return $this->dosenModel->getCountByStatus();
    }


// Di controllers/DosenController.php, pastikan validasi status sesuai:

// Di method createDosen dan updateDosen, pastikan validasi:

}
?>