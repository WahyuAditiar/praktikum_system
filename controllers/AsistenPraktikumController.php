<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AsistenPraktikumModel.php';

class AsistenPraktikumController
{
    private $asistenModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->asistenModel = new AsistenPraktikumModel($db);
    }

    // Handle create asisten
    public function createAsisten($data)
    {
        $errors = [];

        // Validasi input
        if (empty($data['nim'])) {
            $errors[] = "NIM harus diisi";
        } elseif (!preg_match('/^\d+$/', $data['nim'])) {
            $errors[] = "NIM harus berupa angka saja";
        }

        if (empty($data['nama'])) {
            $errors[] = "Nama asisten harus diisi";
        }

        if (empty($data['praktikum_id'])) {
            $errors[] = "Praktikum harus dipilih";
        }

        if (empty($data['nama_praktikum'])) {
            $errors[] = "Nama Praktikum harus dipilih";
        }

        if (empty($data['kelas'])) {
            $errors[] = "Kelas harus dipilih";
        }

        if (empty($data['semester'])) {
            $errors[] = "Semester harus dipilih";
        }

        // Validasi tahun_ajaran
        if (empty($data['tahun_ajaran'])) {
            $errors[] = "Tahun ajaran harus diisi";
        } elseif (!preg_match('/^\d{4}\/\d{4}$/', $data['tahun_ajaran'])) {
            $errors[] = "Format tahun ajaran harus: Tahun/Tahun (contoh: 2023/2024)";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Create asisten
        if ($this->asistenModel->createAsisten(
            $data['nim'],
            $data['nama'],
            $data['praktikum_id'],
            $data['nama_praktikum'],
            $data['kelas'],
            $data['semester'],
            $data['tahun_ajaran'], // TAMBAH PARAMETER INI
            $data['status']
        )) {
            return ['success' => true, 'message' => 'Data asisten praktikum berhasil ditambahkan'];
        } else {
            return ['success' => false, 'errors' => ['Gagal menambahkan data asisten praktikum']];
        }
    }

    // Handle update asisten
    public function updateAsisten($id, $data)
    {
        $errors = [];

        // Validasi input
        if (empty($data['nim'])) {
            $errors[] = "NIM harus diisi";
        } elseif (!preg_match('/^\d+$/', $data['nim'])) {
            $errors[] = "NIM harus berupa angka saja";
        }

        if (empty($data['nama'])) {
            $errors[] = "Nama asisten harus diisi";
        }

        if (empty($data['praktikum_id'])) {
            $errors[] = "Praktikum harus dipilih";
        }

        if (empty($data['nama_praktikum'])) {
            $errors[] = "Nama Praktikum harus dipilih";
        }

        if (empty($data['kelas'])) {
            $errors[] = "Kelas harus dipilih";
        }

        if (empty($data['semester'])) {
            $errors[] = "Semester harus dipilih";
        }

        // Validasi tahun_ajaran
        if (empty($data['tahun_ajaran'])) {
            $errors[] = "Tahun ajaran harus diisi";
        } elseif (!preg_match('/^\d{4}\/\d{4}$/', $data['tahun_ajaran'])) {
            $errors[] = "Format tahun ajaran harus: Tahun/Tahun (contoh: 2023/2024)";
        }

        if (empty($data['status'])) {
            $errors[] = "Status harus dipilih";
        }

        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        // Update asisten
        if ($this->asistenModel->updateAsisten(
            $id,
            $data['nim'],
            $data['nama'],
            $data['praktikum_id'],
            $data['nama_praktikum'],
            $data['kelas'],
            $data['semester'],
            $data['tahun_ajaran'], // TAMBAH PARAMETER INI
            $data['status']
        )) {
            return ['success' => true, 'message' => 'Data asisten praktikum berhasil diperbarui'];
        } else {
            return ['success' => false, 'errors' => ['Gagal memperbarui data asisten praktikum']];
        }
    }

    // Delete asisten
    public function deleteAsisten($id)
    {
        if ($this->asistenModel->deleteAsisten($id)) {
            return ['success' => true, 'message' => 'Data asisten praktikum berhasil dihapus secara permanen'];
        } else {
            return ['success' => false, 'errors' => ['Gagal menghapus data asisten praktikum']];
        }
    }

    // Get all asisten with praktikum info
    public function getAllAsisten()
    {
        return $this->asistenModel->getAllAsisten();
    }

    // Get asisten by ID
    public function getAsistenById($id)
    {
        return $this->asistenModel->getAsistenById($id);
    }

    // Get statistics
    public function getStatistics()
    {
        return $this->asistenModel->getCountByStatus();
    }

    // Get all praktikum for dropdown
    public function getAllPraktikum()
    {
        return $this->asistenModel->getAllPraktikum();
    }
}
