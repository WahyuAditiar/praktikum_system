<?php
class RuanganModel {
    private $conn;
    private $table_name = "ruangan";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all ruangan
    public function getAllRuangan() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY kode_ruangan ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get ruangan by ID
    public function getRuanganById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create new ruangan
    public function createRuangan($kode_ruangan, $nama_ruangan, $kapasitas, $lokasi, $fasilitas, $status) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (kode_ruangan, nama_ruangan, kapasitas, lokasi, fasilitas, status) 
                 VALUES (:kode_ruangan, :nama_ruangan, :kapasitas, :lokasi, :fasilitas, :status)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':kode_ruangan', $kode_ruangan);
        $stmt->bindParam(':nama_ruangan', $nama_ruangan);
        $stmt->bindParam(':kapasitas', $kapasitas);
        $stmt->bindParam(':lokasi', $lokasi);
        $stmt->bindParam(':fasilitas', $fasilitas);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Update ruangan
    public function updateRuangan($id, $kode_ruangan, $nama_ruangan, $kapasitas, $lokasi, $fasilitas, $status) {
        $query = "UPDATE " . $this->table_name . " SET 
                 kode_ruangan = :kode_ruangan, 
                 nama_ruangan = :nama_ruangan, 
                 kapasitas = :kapasitas, 
                 lokasi = :lokasi, 
                 fasilitas = :fasilitas, 
                 status = :status 
                 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':kode_ruangan', $kode_ruangan);
        $stmt->bindParam(':nama_ruangan', $nama_ruangan);
        $stmt->bindParam(':kapasitas', $kapasitas);
        $stmt->bindParam(':lokasi', $lokasi);
        $stmt->bindParam(':fasilitas', $fasilitas);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

   // Delete ruangan (PERMANEN) - PERBAIKAN
    public function deleteRuangan($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error deleting ruangan: " . $e->getMessage());
            return false;
        }
    }


    // Check if kode ruangan already exists
    public function kodeRuanganExists($kode_ruangan, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE kode_ruangan = :kode_ruangan";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kode_ruangan', $kode_ruangan);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Get count ruangan by status
    public function getCountByStatus() {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total kapasitas
    public function getTotalKapasitas() {
        $query = "SELECT SUM(kapasitas) as total_kapasitas FROM " . $this->table_name . " WHERE status != 'inactive'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>