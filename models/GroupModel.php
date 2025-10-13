<?php
class GroupModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // ===============================
    // 📊 GET GROUP CONFIG
    // ===============================
    public function getGroupConfig($jadwal_id) {
        $query = "SELECT config_data FROM praktikum_group_config WHERE jadwal_praktikum_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$jadwal_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? json_decode($result['config_data'], true) : null;
    }

    // ===============================
    // ⚙️ UPDATE GROUP CONFIG
    // ===============================
    public function updateGroupConfig($jadwal_id, $config) {
        $query = "INSERT INTO praktikum_group_config (jadwal_praktikum_id, config_data, updated_by, updated_at) 
                  VALUES (?, ?, ?, NOW())
                  ON DUPLICATE KEY UPDATE 
                  config_data = VALUES(config_data), 
                  updated_by = VALUES(updated_by), 
                  updated_at = NOW()";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $jadwal_id,
            json_encode($config),
            $_SESSION['user_id'] ?? 1
        ]);
    }

    // ===============================
    // 🔍 DEBUG: CEK DATA ASSIGNMENT
    // ===============================
    public function debugAssignment($jadwal_id) {
        try {
            $result = [];
            
            // Cek semua assignment di jadwal
            $stmt = $this->db->prepare("
                SELECT pga.*, 
                       CASE 
                           WHEN pga.entity_type = 'mahasiswa' THEN m.nama
                           WHEN pga.entity_type = 'asisten' THEN ap.nama
                       END as nama_entity
                FROM praktikum_group_assignment pga
                LEFT JOIN mahasiswa m ON pga.entity_type = 'mahasiswa' AND pga.entity_id = m.id
                LEFT JOIN asisten_praktikum ap ON pga.entity_type = 'asisten' AND pga.entity_id = ap.id
                WHERE pga.jadwal_praktikum_id = ?
                ORDER BY pga.group_number, pga.entity_type
            ");
            $stmt->execute([$jadwal_id]);
            $result['assignments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Cek total mahasiswa per group
            $stmt = $this->db->prepare("
                SELECT group_number, 
                       COUNT(CASE WHEN entity_type = 'mahasiswa' THEN 1 END) as total_mahasiswa,
                       COUNT(CASE WHEN entity_type = 'asisten' THEN 1 END) as total_asisten
                FROM praktikum_group_assignment 
                WHERE jadwal_praktikum_id = ?
                GROUP BY group_number
            ");
            $stmt->execute([$jadwal_id]);
            $result['group_summary'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error in debugAssignment: " . $e->getMessage());
            return [];
        }
    }

// ===============================
// 🐛 DEBUG: CEK ASSIGNMENT DETAIL
// ===============================
public function debugAsprakAssignment($user_id, $jadwal_id) {
    try {
        $debug_info = [];
        
        // 1. Cek data user
        $stmt = $this->db->prepare("SELECT id, username, nama, nim, role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $debug_info['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 2. Cek data asisten_praktikum
        if ($debug_info['user'] && $debug_info['user']['nim']) {
            $stmt = $this->db->prepare("SELECT * FROM asisten_praktikum WHERE nim = ?");
            $stmt->execute([$debug_info['user']['nim']]);
            $debug_info['asisten_praktikum'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // 3. Cek assignment
        if ($debug_info['asisten_praktikum']) {
            $stmt = $this->db->prepare("
                SELECT * FROM praktikum_group_assignment 
                WHERE jadwal_praktikum_id = ? 
                AND entity_type = 'asisten' 
                AND entity_id = ?
            ");
            $stmt->execute([$jadwal_id, $debug_info['asisten_praktikum']['id']]);
            $debug_info['assignment'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // 4. Cek semua assignments di jadwal ini
        $stmt = $this->db->prepare("
            SELECT pga.*, 
                   CASE 
                       WHEN pga.entity_type = 'asisten' THEN ap.nama
                       WHEN pga.entity_type = 'mahasiswa' THEN m.nama
                   END as nama_entity
            FROM praktikum_group_assignment pga
            LEFT JOIN asisten_praktikum ap ON pga.entity_type = 'asisten' AND pga.entity_id = ap.id
            LEFT JOIN mahasiswa m ON pga.entity_type = 'mahasiswa' AND pga.entity_id = m.id
            WHERE pga.jadwal_praktikum_id = ?
            ORDER BY pga.group_number, pga.entity_type
        ");
        $stmt->execute([$jadwal_id]);
        $debug_info['all_assignments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $debug_info;
        
    } catch (PDOException $e) {
        error_log("Error in debugAsprakAssignment: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

    // ===============================
    // 👥 ASSIGN TO GROUP - FIXED VERSION
    // ===============================
    public function assignToGroup($jadwal_id, $entity_type, $entity_id, $group_number, $assigned_by) {
        // Tentukan tabel referensi berdasarkan entity_type
        $reference_table = ($entity_type === 'mahasiswa') ? 'mahasiswa' : 'asisten_praktikum';
        
        $query = "INSERT INTO praktikum_group_assignment 
                  (jadwal_praktikum_id, entity_type, entity_id, group_number, reference_table, assigned_by, assigned_at) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW())
                  ON DUPLICATE KEY UPDATE 
                  group_number = VALUES(group_number), 
                  assigned_by = VALUES(assigned_by), 
                  assigned_at = NOW()";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $jadwal_id,
            $entity_type,
            $entity_id,
            $group_number,
            $reference_table,
            $assigned_by
        ]);
    }

    // ===============================
    // 🔍 GET MAHASISWA BY GROUP
    // ===============================
    public function getMahasiswaByGroup($jadwal_id, $group_number) {
        $query = "SELECT m.* 
                  FROM mahasiswa m
                  JOIN praktikum_group_assignment pga ON m.id = pga.entity_id
                  WHERE pga.jadwal_praktikum_id = ? 
                  AND pga.group_number = ?
                  AND pga.entity_type = 'mahasiswa'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$jadwal_id, $group_number]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // 🔍 GET ASPRAK BY GROUP
    // ===============================
    public function getAsprakByGroup($jadwal_id, $group_number) {
        $query = "SELECT ap.* 
                  FROM asisten_praktikum ap
                  JOIN praktikum_group_assignment pga ON ap.id = pga.entity_id
                  WHERE pga.jadwal_praktikum_id = ? 
                  AND pga.group_number = ?
                  AND pga.entity_type = 'asisten'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$jadwal_id, $group_number]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // 🗑️ REMOVE FROM GROUP
    // ===============================
    public function removeFromGroup($jadwal_id, $entity_type, $entity_id) {
        $query = "DELETE FROM praktikum_group_assignment 
                  WHERE jadwal_praktikum_id = ? 
                  AND entity_type = ? 
                  AND entity_id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$jadwal_id, $entity_type, $entity_id]);
    }

    // ===============================
    // 📊 GET GROUP SUMMARY
    // ===============================
    public function getGroupSummary($jadwal_id) {
        $query = "SELECT 
                    group_number,
                    COUNT(CASE WHEN entity_type = 'mahasiswa' THEN 1 END) as total_mahasiswa,
                    COUNT(CASE WHEN entity_type = 'asisten' THEN 1 END) as total_asisten
                  FROM praktikum_group_assignment 
                  WHERE jadwal_praktikum_id = ?
                  GROUP BY group_number
                  ORDER BY group_number";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$jadwal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // 🔄 CLEAR ALL ASSIGNMENTS
    // ===============================
    public function clearAllAssignments($jadwal_id) {
        $query = "DELETE FROM praktikum_group_assignment WHERE jadwal_praktikum_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$jadwal_id]);
    }

    // ===============================
// 🔍 GET ASPRAK GROUP - VERSION FIXED
// ===============================
public function getAsprakGroup($user_id, $jadwal_id) {
    try {
        error_log("🔍 Mencari group asisten: user_id=$user_id, jadwal_id=$jadwal_id");
        
        // 1. Cari asisten_praktikum_id dari user_id via tabel users
        $stmt = $this->db->prepare("
            SELECT u.nim, ap.id as asprak_id, ap.nama 
            FROM users u 
            LEFT JOIN asisten_praktikum ap ON u.nim = ap.nim 
            WHERE u.id = ? AND u.role = 'asisten_praktikum'
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        $asprak = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$asprak) {
            error_log("❌ Asisten praktikum tidak ditemukan untuk user_id: $user_id");
            return null;
        }
        
        $asprak_id = $asprak['asprak_id'];
        $asprak_nim = $asprak['nim'];
        
        if (!$asprak_id) {
            error_log("❌ Data asisten_praktikum tidak ditemukan untuk NIM: $asprak_nim");
            return null;
        }
        
        error_log("✅ Asisten ditemukan: asprak_id=$asprak_id, nim=$asprak_nim, nama=" . $asprak['nama']);
        
        // 2. Cari group assignment untuk asisten ini di jadwal tertentu
        $stmt = $this->db->prepare("
            SELECT group_number 
            FROM praktikum_group_assignment 
            WHERE jadwal_praktikum_id = ? 
            AND entity_type = 'asisten' 
            AND entity_id = ?
            LIMIT 1
        ");
        $stmt->execute([$jadwal_id, $asprak_id]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($assignment) {
            error_log("✅ Assignment ditemukan: group_number=" . $assignment['group_number']);
            return $assignment['group_number'];
        }
        
        error_log("❌ Tidak ada assignment untuk asprak_id: $asprak_id di jadwal: $jadwal_id");
        
        // Debug: cek semua assignment asisten di jadwal ini
        $stmt_debug = $this->db->prepare("
            SELECT pga.entity_id, pga.group_number, ap.nama, ap.nim
            FROM praktikum_group_assignment pga
            LEFT JOIN asisten_praktikum ap ON pga.entity_id = ap.id
            WHERE pga.jadwal_praktikum_id = ? AND pga.entity_type = 'asisten'
        ");
        $stmt_debug->execute([$jadwal_id]);
        $all_assignments = $stmt_debug->fetchAll(PDO::FETCH_ASSOC);
        error_log("📋 Semua assignment asisten di jadwal $jadwal_id: " . json_encode($all_assignments));
        
        return null;
        
    } catch (PDOException $e) {
        error_log("❌ Error in getAsprakGroup: " . $e->getMessage());
        return null;
    }
}


    // ===============================
    // 🔍 GET ALL MAHASISWA IN JADWAL (UNTUK DEBUG)
    // ===============================
    public function getAllMahasiswaInJadwal($jadwal_id) {
        try {
            // Dapatkan praktikum_id dari jadwal
            $stmt = $this->db->prepare("SELECT praktikum_id FROM jadwal_praktikum WHERE id = ?");
            $stmt->execute([$jadwal_id]);
            $jadwal = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$jadwal) return [];
            
            $praktikum_id = $jadwal['praktikum_id'];
            
            // Ambil semua mahasiswa di praktikum ini
            $stmt = $this->db->prepare("
                SELECT m.* FROM mahasiswa m WHERE m.praktikum_id = ? ORDER BY m.nim
            ");
            $stmt->execute([$praktikum_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error in getAllMahasiswaInJadwal: " . $e->getMessage());
            return [];
        }
    }
}
?>