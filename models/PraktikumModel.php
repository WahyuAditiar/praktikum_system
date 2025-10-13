<?php
require_once __DIR__ . '/../config/database.php';

class PraktikumModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ✅ Semua data praktikum
    public function getAll() {
        $sql = "SELECT * FROM praktikum ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Praktikum dengan status aktif
    public function getAktif() {
        $sql = "SELECT * FROM praktikum WHERE status = 'aktif'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Insert praktikum
    public function insert($data) {
        $sql = "INSERT INTO praktikum (nama_praktikum, semester, tahun_ajaran) 
                VALUES (:nama_praktikum, :semester, :tahun_ajaran)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nama_praktikum' => $data['nama_praktikum'],
            ':semester'       => $data['semester'],
            ':tahun_ajaran'   => $data['tahun_ajaran']
        ]);
    }

    // ✅ Ambil daftar praktikum (untuk dropdown)
    public function getAllPraktikum() {
        $sql = "SELECT p.id, p.nama_praktikum, p.semester, p.tahun_ajaran, 
                       m.kode_mk, m.nama_mk
                FROM praktikum p
                LEFT JOIN mata_kuliah m ON p.mata_kuliah_id = m.id
                ORDER BY m.kode_mk ASC, p.nama_praktikum ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAsisten($nim) {
        $sql = "SELECT p.* 
                FROM praktikum p
                JOIN asisten_praktikum ap ON ap.praktikum_id = p.id
                WHERE ap.nim_asisten = :nim";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['nim' => $nim]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // models/PraktikumModel.php
    public function getByAsistenNama($namaAsisten) {
        $sql = "SELECT ap.id, ap.nim, ap.nama, ap.kelas, ap.nama_praktikum, ap.semester, ap.status
                FROM asisten_praktikum ap
                WHERE ap.nama = :nama";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':nama' => $namaAsisten]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAsistenNim($nim) {
        $sql = "SELECT ap.id, ap.nim, ap.nama, ap.kelas, ap.nama_praktikum, ap.semester, ap.status
                FROM asisten_praktikum ap
                WHERE ap.nim = :nim";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':nim' => $nim]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  public function getByTahunAjaran($tahun_ajaran)
{
    $query = "SELECT 
                  jp.id,
                  p.nama_praktikum,
                  jp.kelas,
                  p.tahun_ajaran
              FROM jadwal_praktikum jp
              JOIN praktikum p ON jp.praktikum_id = p.id
              WHERE p.tahun_ajaran = :tahun_ajaran";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':tahun_ajaran', $tahun_ajaran);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // ✅ METHOD TAMBAHAN: Cek apakah tabel jadwal_praktikum ada
    public function cekTabelJadwalPraktikum() {
        try {
            $sql = "SHOW TABLES LIKE 'jadwal_praktikum'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // ✅ METHOD TAMBAHAN: Get dari jadwal_praktikum jika ada
    public function getFromJadwalPraktikum($tahunAjaran) {
        try {
            if (!$this->cekTabelJadwalPraktikum()) {
                return [];
            }
            
            $sql = "SELECT id, nama_praktikum, kelas, tahun_ajaran, status 
                    FROM jadwal_praktikum 
                    WHERE tahun_ajaran = ? 
                    AND status = 'active' 
                    ORDER BY nama_praktikum, kelas";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$tahunAjaran]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error getFromJadwalPraktikum: " . $e->getMessage());
            return [];
        }
    }

    // ✅ METHOD UNTUK CEK STRUKTUR TABEL
private function checkTableStructure() {
    $result = [
        'praktikum' => false,
        'jadwal_praktikum' => false
    ];
    
    try {
        // Cek tabel praktikum
        $sql = "SHOW TABLES LIKE 'praktikum'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result['praktikum'] = $stmt->rowCount() > 0;
        
        // Cek tabel jadwal_praktikum
        $sql = "SHOW TABLES LIKE 'jadwal_praktikum'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result['jadwal_praktikum'] = $stmt->rowCount() > 0;
        
        error_log("📊 Table check - Praktikum: " . ($result['praktikum'] ? 'YES' : 'NO') . 
                 ", Jadwal_Praktikum: " . ($result['jadwal_praktikum'] ? 'YES' : 'NO'));
                 
    } catch (Exception $e) {
        error_log("❌ Error checking table structure: " . $e->getMessage());
    }
    
    return $result;
}

// ✅ METHOD UNTUK TESTING KONEKSI DAN DATA
public function testGetByTahunAjaran($tahunAjaran) {
    error_log("🧪 TESTING getByTahunAjaran for: " . $tahunAjaran);
    
    // Test koneksi
    if (!$this->conn) {
        error_log("❌ Database connection failed");
        return false;
    }
    
    // Test query
    try {
        $sql = "SELECT 
                    p.id,
                    p.nama_praktikum,
                    j.kelas,
                    j.tahun_ajaran
                FROM praktikum p
                INNER JOIN jadwal_praktikum j ON p.id = j.praktikum_id
                WHERE j.tahun_ajaran = ? 
                LIMIT 5";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tahunAjaran]);
        $testResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("🧪 Test query result: " . count($testResult) . " records");
        if (count($testResult) > 0) {
            error_log("🧪 Sample data: " . json_encode($testResult[0]));
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("❌ Test query failed: " . $e->getMessage());
        return false;
    }
}

}
?>