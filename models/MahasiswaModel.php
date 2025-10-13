<?php
class MahasiswaModel {
    private $conn;
    private $table_name = "mahasiswa";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ambil semua data mahasiswa + info praktikum
    public function getAllMahasiswa() {
        $query = "SELECT m.*, p.nama_praktikum, mk.kode_mk, mk.nama_mk 
                  FROM " . $this->table_name . " m
                  JOIN praktikum p ON m.praktikum_id = p.id
                  JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                  ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil mahasiswa by ID
    public function getMahasiswaById($id) {
        $query = "SELECT m.*, p.nama_praktikum, mk.kode_mk, mk.nama_mk
                  FROM " . $this->table_name . " m
                  JOIN praktikum p ON m.praktikum_id = p.id
                  JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                  WHERE m.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tambah mahasiswa baru
    public function createMahasiswa($nim, $nama, $kelas, $email, $praktikum_id, $semester, $tahun_akademik, $prodi, $created_by) {
        $query = "INSERT INTO mahasiswa 
            (nim, nama, kelas, email, praktikum_id, semester, tahun_akademik, prodi, created_by, created_at, updated_at)
            VALUES 
            (:nim, :nama, :kelas, :email, :praktikum_id, :semester, :tahun_akademik, :prodi, :created_by, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nim' => $nim,
            ':nama' => $nama,
            ':kelas' => $kelas,
            ':email' => $email,
            ':praktikum_id' => $praktikum_id,
            ':semester' => $semester,
            ':tahun_akademik' => $tahun_akademik,
            ':prodi' => $prodi,
            ':created_by' => $created_by
        ]);
    }

    // Update data mahasiswa
    public function updateMahasiswa($id, $nim, $nama, $kelas, $email, $praktikum_id) {
        $query = "UPDATE " . $this->table_name . " SET
                  nim = :nim,
                  nama = :nama,
                  kelas = :kelas,
                  email = :email,
                  praktikum_id = :praktikum_id,
                  updated_at = NOW()
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':kelas', $kelas);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':praktikum_id', $praktikum_id);
        return $stmt->execute();
    }

    // Hapus mahasiswa
    public function deleteMahasiswa($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Cek apakah NIM sudah ada di praktikum tertentu
    public function nimExists($nim, $praktikum_id, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE nim = :nim AND praktikum_id = :praktikum_id";
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':praktikum_id', $praktikum_id);

        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }

        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Statistik mahasiswa per status
    /*
    public function getCountByStatus() {
        $query = "SELECT status, COUNT(*) as total 
                  FROM " . $this->table_name . " 
                  GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } */

    // Ambil daftar praktikum aktif untuk dropdown
    public function getAllPraktikum() {
        $query = "SELECT p.id, p.nama_praktikum, mk.kode_mk, mk.nama_mk
                  FROM praktikum p
                  JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                  WHERE p.status = 'aktif'
                  ORDER BY mk.nama_mk ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil mahasiswa berdasarkan praktikum_id
    public function getMahasiswaByPraktikum($praktikum_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE praktikum_id = :praktikum_id ORDER BY nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':praktikum_id', $praktikum_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Jumlah total mahasiswa
    public function getTotalMahasiswa() {
        $sql = "SELECT COUNT(*) AS total FROM mahasiswa";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // hasil: ['total' => 20]
    }
}
