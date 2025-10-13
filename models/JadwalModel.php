<?php
class JadwalModel {
    private $conn;
    private $table_name = "jadwal_kuliah";

    public function __construct($db) {
        $this->conn = $db;
    }

// Get all jadwal dengan join ke tabel lain
public function getAllJadwal() {
        $query = "SELECT jk.*, 
                         mk.nama_mk, mk.kode_mk, mk.sks,
                         d.nidn, d.nama AS nama_dosen, d.status AS status_dosen,
                         r.nama_ruangan, r.kode_ruangan, r.lokasi
              FROM " . $this->table_name . " jk
              LEFT JOIN mata_kuliah mk ON jk.mata_kuliah_id = mk.id
              LEFT JOIN dosen d ON jk.dosen_id = d.id
              LEFT JOIN ruangan r ON jk.ruangan_id = r.id
              ORDER BY jk.hari, jk.jam_mulai ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Get jadwal by ID dengan join
public function getJadwalById($id) {
        $query = "SELECT jk.*, 
                         mk.nama_mk, mk.kode_mk, mk.sks,
                         d.nidn, d.nama AS nama_dosen, d.status AS status_dosen,
                         r.nama_ruangan, r.kode_ruangan, r.lokasi
              FROM " . $this->table_name . " jk
              LEFT JOIN mata_kuliah mk ON jk.mata_kuliah_id = mk.id
              LEFT JOIN dosen d ON jk.dosen_id = d.id
              LEFT JOIN ruangan r ON jk.ruangan_id = r.id
              WHERE jk.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Create new jadwal
    public function createJadwal($mata_kuliah_id, $dosen_id, $ruangan_id, $hari, $jam_mulai, $jam_selesai, $kelas, $status) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (mata_kuliah_id, dosen_id, ruangan_id, hari, jam_mulai, jam_selesai, kelas, status) 
                     VALUES (:mata_kuliah_id, :dosen_id, :ruangan_id, :hari, :jam_mulai, :jam_selesai, :kelas, :status)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':mata_kuliah_id', $mata_kuliah_id);
            $stmt->bindParam(':dosen_id', $dosen_id);
            $stmt->bindParam(':ruangan_id', $ruangan_id);
            $stmt->bindParam(':hari', $hari);
            $stmt->bindParam(':jam_mulai', $jam_mulai);
            $stmt->bindParam(':jam_selesai', $jam_selesai);
            $stmt->bindParam(':kelas', $kelas);
            $stmt->bindParam(':status', $status);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating jadwal: " . $e->getMessage());
            return false;
        }
    }

    // Update jadwal
     public function updateJadwal($id, $mata_kuliah_id, $dosen_id, $ruangan_id, $hari, $jam_mulai, $jam_selesai, $kelas, $status) {
        try {
            $query = "UPDATE " . $this->table_name . " SET 
                     mata_kuliah_id = :mata_kuliah_id, 
                     dosen_id = :dosen_id, 
                     ruangan_id = :ruangan_id, 
                     hari = :hari, 
                     jam_mulai = :jam_mulai, 
                     jam_selesai = :jam_selesai, 
                     kelas = :kelas, 
                     status = :status 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':mata_kuliah_id', $mata_kuliah_id);
            $stmt->bindParam(':dosen_id', $dosen_id);
            $stmt->bindParam(':ruangan_id', $ruangan_id);
            $stmt->bindParam(':hari', $hari);
            $stmt->bindParam(':jam_mulai', $jam_mulai);
            $stmt->bindParam(':jam_selesai', $jam_selesai);
            $stmt->bindParam(':kelas', $kelas);
            $stmt->bindParam(':status', $status);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating jadwal: " . $e->getMessage());
            return false;
        }
    }

    // Delete jadwal (soft delete)
    public function deleteJadwal($id) {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();

    }

    // Check for schedule conflict
    public function checkScheduleConflict($ruangan_id, $hari, $jam_mulai, $jam_selesai, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE ruangan_id = :ruangan_id 
                  AND hari = :hari 
                  AND status != 'inactive'
                  AND (
                    (jam_mulai BETWEEN :jam_mulai AND :jam_selesai) 
                    OR (jam_selesai BETWEEN :jam_mulai AND :jam_selesai)
                    OR (:jam_mulai BETWEEN jam_mulai AND jam_selesai)
                  )";
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ruangan_id', $ruangan_id);
        $stmt->bindParam(':hari', $hari);
        $stmt->bindParam(':jam_mulai', $jam_mulai);
        $stmt->bindParam(':jam_selesai', $jam_selesai);
        
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Check if dosen is available
    public function checkDosenAvailability($dosen_id, $hari, $jam_mulai, $jam_selesai, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE dosen_id = :dosen_id 
                  AND hari = :hari 
                  AND status != 'inactive'
                  AND (
                    (jam_mulai BETWEEN :jam_mulai AND :jam_selesai) 
                    OR (jam_selesai BETWEEN :jam_mulai AND :jam_selesai)
                    OR (:jam_mulai BETWEEN jam_mulai AND jam_selesai)
                  )";
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':dosen_id', $dosen_id);
        $stmt->bindParam(':hari', $hari);
        $stmt->bindParam(':jam_mulai', $jam_mulai);
        $stmt->bindParam(':jam_selesai', $jam_selesai);
        
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Get jadwal by hari
   public function getJadwalByHari($hari) {
        $query = "SELECT jk.*, 
                         mk.nama_mk, mk.kode_mk, mk.sks,
                         d.nidn, d.nama AS nama_dosen,
                         r.nama_ruangan, r.kode_ruangan
                  FROM " . $this->table_name . " jk
                  LEFT JOIN mata_kuliah mk ON jk.mata_kuliah_id = mk.id
                  LEFT JOIN dosen d ON jk.dosen_id = d.id
                  LEFT JOIN ruangan r ON jk.ruangan_id = r.id
                  WHERE jk.hari = :hari AND jk.status = 'active'
                  ORDER BY jk.jam_mulai ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hari', $hari);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get count jadwal by status
    public function getCountByStatus() {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get count jadwal by hari
    public function getCountByHari() {
        $query = "SELECT hari, COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'active' GROUP BY hari ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>