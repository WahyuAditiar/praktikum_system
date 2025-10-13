<?php
class MataKuliahModel {
    private $conn;
    private $table_name = "mata_kuliah";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all mata kuliah
    public function getAllMataKuliah() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY semester ASC, kode_mk ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get mata kuliah by ID
    public function getMataKuliahById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create new mata kuliah
    public function createMataKuliah($kode_mk, $nama_mk, $sks, $semester, $jurusan, $deskripsi, $status) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (kode_mk, nama_mk, sks, semester, jurusan, deskripsi, status) 
                 VALUES (:kode_mk, :nama_mk, :sks, :semester, :jurusan, :deskripsi, :status)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':kode_mk', $kode_mk);
        $stmt->bindParam(':nama_mk', $nama_mk);
        $stmt->bindParam(':sks', $sks);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':jurusan', $jurusan);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Update mata kuliah
    public function updateMataKuliah($id, $kode_mk, $nama_mk, $sks, $semester, $jurusan, $deskripsi, $status) {
        $query = "UPDATE " . $this->table_name . " SET 
                 kode_mk = :kode_mk, 
                 nama_mk = :nama_mk, 
                 sks = :sks, 
                 semester = :semester, 
                 jurusan = :jurusan, 
                 deskripsi = :deskripsi, 
                 status = :status 
                 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':kode_mk', $kode_mk);
        $stmt->bindParam(':nama_mk', $nama_mk);
        $stmt->bindParam(':sks', $sks);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':jurusan', $jurusan);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

     // Delete mata kuliah (PERMANEN) - PERBAIKAN
    public function deleteMataKuliah($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error deleting mata kuliah: " . $e->getMessage());
            return false;
        }
    }

    // Check if kode mata kuliah already exists
    public function kodeMkExists($kode_mk, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE kode_mk = :kode_mk";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kode_mk', $kode_mk);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get count mata kuliah by status
    public function getCountByStatus() {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get count by semester
    public function getCountBySemester() {
        $query = "SELECT semester, COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'active' GROUP BY semester ORDER BY semester";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total SKS
    public function getTotalSKS() {
        $query = "SELECT SUM(sks) as total_sks FROM " . $this->table_name . " WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get mata kuliah by semester
    public function getMataKuliahBySemester($semester) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE semester = :semester AND status = 'active' ORDER BY kode_mk";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':semester', $semester);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>