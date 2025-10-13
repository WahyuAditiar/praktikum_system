<?php
require_once __DIR__ . '/../models/MahasiswaModel.php';
// 📌 load PhpSpreadsheet - KOMENTARI DULU JIKA BELUM TERINSTALL
// require_once __DIR__ . '/../libraries/PhpSpreadsheet/vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\IOFactory;

class MahasiswaController {
    private $model;

    public function __construct($db) {
        $this->model = new MahasiswaModel($db);
    }

    // Method importExcel sementara di-nonaktifkan karena library belum terinstall
    /*
    public function importExcel($file) {
        try {
            // 1. Load file excel
            $spreadsheet = IOFactory::load($file['tmp_name']);

            // 2. Ambil semua baris dalam bentuk array
            $rows = $spreadsheet->getActiveSheet()->toArray();

            // 3. Loop data, skip header (row pertama)
            foreach ($rows as $index => $row) {
                if ($index == 0) continue; // skip header

                $kode_mk   = $row[0];
                $nama_mk   = $row[1];
                $sks       = $row[2];
                $semester  = $row[3];
                $jurusan   = $row[4];
                $status    = $row[5];

                // Simpan ke database lewat model
                $this->model->createMataKuliah($kode_mk, $nama_mk, $sks, $semester, $jurusan, $status);
            }

            echo "✅ Import data mata kuliah berhasil!";
        } catch (Exception $e) {
            echo "❌ Gagal import Excel: " . $e->getMessage();
        }
    }
    */

    // Ambil semua data mahasiswa
    public function getAllMahasiswa() {
        return $this->model->getAllMahasiswa();
    }

    // Ambil data mahasiswa berdasarkan ID
    public function getMahasiswaById($id) {
        return $this->model->getMahasiswaById($id);
    }

    // Tambah mahasiswa baru
    public function createMahasiswa($data) {
        $errors = [];

        // Validasi input dasar
        if (empty($data['nim'])) $errors[] = "NIM wajib diisi";
        if (empty($data['nama'])) $errors[] = "Nama wajib diisi";
        if (empty($data['kelas'])) $errors[] = "Kelas wajib diisi";
        if (empty($data['email'])) $errors[] = "Email wajib diisi";
        if (empty($data['praktikum_id'])) $errors[] = "Praktikum wajib dipilih";
        if (empty($data['semester'])) $errors[] = "Semester wajib dipilih";
        if (empty($data['tahun_akademik'])) $errors[] = "Tahun Akademik wajib dipilih";
        if (empty($data['prodi'])) $errors[] = "Program Studi wajib dipilih";

        if (!empty($errors)) {
            return ["success" => false, "errors" => $errors];
        }

        // 🔑 Tentukan created_by
        if (isset($data['source']) && $data['source'] === 'mahasiswa') {
            $created_by = 'mahasiswa'; // input mandiri
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'staff_lab') {
            $created_by = 'staff_lab'; // input dari staf lab
        } else {
            $created_by = 'system'; // fallback
        }

        $success = $this->model->createMahasiswa(
            $data['nim'],
            $data['nama'],
            $data['kelas'],
            $data['email'],
            $data['praktikum_id'],
            $data['semester'],
            $data['tahun_akademik'],
            $data['prodi'],
            $created_by
        );

        return [
            "success" => $success,
            "message" => $success ? "Mahasiswa berhasil ditambahkan." : "Gagal menambahkan mahasiswa.",
            "errors"  => $success ? [] : ["Terjadi kesalahan saat insert data."]
        ];
    }

    // Update data mahasiswa
    public function updateMahasiswa($id, $data) {
        $errors = [];

        // Validasi input dasar
        if (empty($data['nim'])) $errors[] = "NIM wajib diisi";
        if (empty($data['nama'])) $errors[] = "Nama wajib diisi";
        if (empty($data['kelas'])) $errors[] = "Kelas wajib diisi";
        if (empty($data['email'])) $errors[] = "Email wajib diisi";
        if (empty($data['praktikum_id'])) $errors[] = "Praktikum wajib dipilih";
        if (empty($data['semester'])) $errors[] = "Semester wajib dipilih";
        if (empty($data['tahun_akademik'])) $errors[] = "Tahun Akademik wajib dipilih";
        if (empty($data['prodi'])) $errors[] = "Program Studi wajib dipilih";

        if (!empty($errors)) {
            return ["success" => false, "errors" => $errors];
        }

        $success = $this->model->updateMahasiswa(
            $id,
            $data['nim'],
            $data['nama'],
            $data['kelas'],
            $data['email'],
            $data['praktikum_id'],
            $data['semester'],
            $data['tahun_akademik'],
            $data['prodi']
        );

        return [
            "success" => $success,
            "message" => $success ? "Data mahasiswa berhasil diperbarui." : "Gagal memperbarui mahasiswa.",
            "errors"  => $success ? [] : ["Terjadi kesalahan saat update data."]
        ];
    }

    // Hapus mahasiswa
    public function deleteMahasiswa($id) {
        $success = $this->model->deleteMahasiswa($id);

        return [
            "success" => $success,
            "message" => $success ? "Mahasiswa berhasil dihapus." : "Gagal menghapus mahasiswa.",
            "errors"  => $success ? [] : ["Terjadi kesalahan saat hapus data."]
        ];
    }

    // Ambil semua praktikum untuk dropdown
    public function getAllPraktikum() {
        return $this->model->getAllPraktikum();
    }
}