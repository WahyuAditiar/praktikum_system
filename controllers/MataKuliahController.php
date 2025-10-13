<?php
require_once __DIR__ . '/../models/MataKuliahModel.php';

class MataKuliahController {
    private $mataKuliahModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->mataKuliahModel = new MataKuliahModel($db);
    }

    // Handle create mata kuliah
    public function createMataKuliah($data) {
        $errors = [];

        // Validasi input
        if (empty($data['kode_mk'])) {
            $errors[] = "Kode mata kuliah harus diisi";
        } elseif ($this->mataKuliahModel->kodeMkExists($data['kode_mk'])) {
            $errors[] = "Kode mata kuliah sudah terdaftar";
        }

        if (empty($data['nama_mk'])) {
            $errors[] = "Nama mata kuliah harus diisi";
        }

        if (empty($data['sks']) || !is_numeric($data['sks']) || $data['sks'] <= 0) {
            $errors[] = "SKS harus angka dan lebih dari 0";
        }

        if (empty($data['semester'])) {
            $errors[] = "Semester harus dipilih";
        }

        if (empty($data['jurusan'])) {
            $errors[] = "Jurusan harus diisi";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Create mata kuliah
      if ($this->mataKuliahModel->createMataKuliah(
    $data['kode_mk'], 
    $data['nama_mk'], 
    $data['sks'], 
    $data['semester'], 
    $data['jurusan'], 
    isset($data['deskripsi']) ? $data['deskripsi'] : '', 
    $data['status']
)) {
    return ['success' => true, 'message' => 'Data mata kuliah berhasil ditambahkan'];
}
    }

    // Handle update mata kuliah
    public function updateMataKuliah($id, $data) {
        $errors = [];

        // Validasi input
        if (empty($data['kode_mk'])) {
            $errors[] = "Kode mata kuliah harus diisi";
        } elseif ($this->mataKuliahModel->kodeMkExists($data['kode_mk'], $id)) {
            $errors[] = "Kode mata kuliah sudah terdaftar";
        }

        if (empty($data['nama_mk'])) {
            $errors[] = "Nama mata kuliah harus diisi";
        }

        if (empty($data['sks']) || !is_numeric($data['sks']) || $data['sks'] <= 0) {
            $errors[] = "SKS harus angka dan lebih dari 0";
        }

        if (empty($data['semester'])) {
            $errors[] = "Semester harus dipilih";
        }

        if (empty($data['jurusan'])) {
            $errors[] = "Jurusan harus diisi";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Update mata kuliah
       if ($this->mataKuliahModel->updateMataKuliah(
    $id,
    $data['kode_mk'], 
    $data['nama_mk'], 
    $data['sks'], 
    $data['semester'], 
    $data['jurusan'], 
    isset($data['deskripsi']) ? $data['deskripsi'] : '', 
    $data['status']
)) {
    return ['success' => true, 'message' => 'Data mata kuliah berhasil diperbarui'];
}

    }

    // Handle delete mata kuliah
  public function deleteMataKuliah($id) {
    // ... validasi tetap sama ...

    if ($this->mataKuliahModel->deleteMataKuliah($id)) {
        return ['success' => true, 'message' => 'Data mata kuliah berhasil dihapus secara permanen'];
    } else {
        return ['success' => false, 'errors' => ['Gagal menghapus data mata kuliah']];
    }
}

    // Get all mata kuliah
    public function getAllMataKuliah() {
        return $this->mataKuliahModel->getAllMataKuliah();
    }

    // Get mata kuliah by ID
    public function getMataKuliahById($id) {
        return $this->mataKuliahModel->getMataKuliahById($id);
    }

    // Get statistics
    public function getStatistics() {
        return $this->mataKuliahModel->getCountByStatus();
    }

    // Get statistics by semester
    public function getStatisticsBySemester() {
        return $this->mataKuliahModel->getCountBySemester();
    }

    // Get total SKS
    public function getTotalSKS() {
        return $this->mataKuliahModel->getTotalSKS();
    }

    // Get mata kuliah by semester
    public function getMataKuliahBySemester($semester) {
        return $this->mataKuliahModel->getMataKuliahBySemester($semester);
    }
}
?>