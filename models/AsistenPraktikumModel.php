<?php
class AsistenPraktikumModel
{
    private $conn;
    private $table_name = "asisten_praktikum";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all asisten with praktikum and mata_kuliah info
    public function getAllAsisten()
    {
        $query = "SELECT a.*, p.nama_praktikum, p.tahun_ajaran, m.kode_mk, m.nama_mk 
              FROM " . $this->table_name . " a 
              JOIN praktikum p ON a.praktikum_id = p.id 
              JOIN mata_kuliah m ON p.mata_kuliah_id = m.id 
              ORDER BY a.nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAsistenById($id)
    {
        $query = "SELECT a.*, p.nama_praktikum, p.tahun_ajaran, m.kode_mk, m.nama_mk 
              FROM " . $this->table_name . " a 
              JOIN praktikum p ON a.praktikum_id = p.id 
              JOIN mata_kuliah m ON p.mata_kuliah_id = m.id 
              WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createAsisten($nim, $nama, $praktikum_id, $nama_praktikum, $kelas, $semester, $tahun_ajaran, $status)
    {
        $query = "INSERT INTO " . $this->table_name . " 
             (nim, nama, praktikum_id, nama_praktikum, kelas, semester, tahun_ajaran, status, created_at) 
             VALUES (:nim, :nama, :praktikum_id, :nama_praktikum, :kelas, :semester, :tahun_ajaran, :status, NOW())";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':praktikum_id', $praktikum_id);
        $stmt->bindParam(':nama_praktikum', $nama_praktikum);
        $stmt->bindParam(':kelas', $kelas);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':tahun_ajaran', $tahun_ajaran);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    public function updateAsisten($id, $nim, $nama, $praktikum_id, $nama_praktikum, $kelas, $semester, $tahun_ajaran, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET 
             nim = :nim, 
             nama = :nama, 
             praktikum_id = :praktikum_id, 
             nama_praktikum = :nama_praktikum,
             kelas = :kelas,
             semester = :semester, 
             tahun_ajaran = :tahun_ajaran,
             status = :status 
             WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':praktikum_id', $praktikum_id);
        $stmt->bindParam(':nama_praktikum', $nama_praktikum);
        $stmt->bindParam(':kelas', $kelas);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':tahun_ajaran', $tahun_ajaran);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    public function deleteAsisten($id)
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error deleting asisten: " . $e->getMessage());
            return false;
        }
    }

    // Check if NIM already exists for praktikum
    public function nimExists($nim, $praktikum_id, $exclude_id = null)
    {
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

    // Get count asisten by status
    public function getCountByStatus()
    {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all praktikum for dropdown - PERBAIKI QUERY
public function getAllPraktikum()
{
    $query = "
    SELECT 
        id AS praktikum_id,
        nama_praktikum,
        kelas,
        tahun_ajaran
    FROM praktikum
    WHERE status = 'aktif'
    ORDER BY nama_praktikum ASC, kelas ASC
";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // Get asisten by NIM
    public function getAsistenByNim($nim)
    {
        $stmt = $this->conn->prepare("
            SELECT a.nim, a.nama, a.praktikum_id, p.nama_praktikum, a.kelas
            FROM " . $this->table_name . " a
            LEFT JOIN praktikum p ON a.praktikum_id = p.id
            WHERE a.nim = ?
        ");
        $stmt->execute([$nim]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // NEW: Get available kelas for praktikum
    public function getAvailableKelas($praktikum_id)
    {
        // Cek kelas yang sudah digunakan di praktikum ini
        $query = "SELECT DISTINCT kelas FROM " . $this->table_name . " 
                  WHERE praktikum_id = :praktikum_id 
                  ORDER BY kelas";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':praktikum_id', $praktikum_id);
        $stmt->execute();
        
        $usedKelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Default kelas options
        $allKelas = ['A', 'B', 'C', 'D', 'E', 'F'];
        
        // Jika ada kelas yang digunakan, suggest kelas berikutnya
        if (!empty($usedKelas)) {
            $lastKelas = end($usedKelas);
            $nextKelasIndex = array_search($lastKelas, $allKelas) + 1;
            return isset($allKelas[$nextKelasIndex]) ? $allKelas[$nextKelasIndex] : 'A';
        }
        
        // Default ke A jika belum ada kelas
        return 'A';
    }
}
?>