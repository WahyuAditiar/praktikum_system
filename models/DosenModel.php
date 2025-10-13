<?php
class DosenModel {
    private $conn;
    private $table_name = "dosen";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all dosen yang aktif (tetap dan tidak tetap)
    public function getAllDosen() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status != 'inactive' ORDER BY nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get dosen by ID
    public function getDosenById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create new dosen
    public function createDosen($nidn, $nama, $hp, $status) {
        $query = "INSERT INTO " . $this->table_name . " (nidn, nama, no_hp, status) VALUES (:nidn, :nama, :hp, :status)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nidn', $nidn);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':hp', $hp);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Update dosen
    public function updateDosen($id, $nidn, $nama, $hp, $status) {
        $query = "UPDATE " . $this->table_name . " SET nidn = :nidn, nama = :nama, no_hp = :hp, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nidn', $nidn);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':hp', $hp);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

   // Delete dosen (PERMANEN) - PERBAIKAN
    public function deleteDosen($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error deleting dosen: " . $e->getMessage());
            return false;
        }
    }

    // Check if NIDN already exists
    public function nidnExists($nidn, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE nidn = :nidn";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nidn', $nidn);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get count dosen by status
    public function getCountByStatus() {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get dosen aktif saja (untuk dropdown)
    public function getDosenAktif() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status IN ('tetap', 'tidak_tetap') ORDER BY nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>