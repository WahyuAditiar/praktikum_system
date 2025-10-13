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

    // ✅ PERBAIKI METHOD getByTahunAjaran - SESUAIKAN DENGAN STRUKTUR ANDA
    public function getByTahunAjaran($tahunAjaran) {
        try {
            error_log("Getting praktikum for tahun: " . $tahunAjaran);
            
            // Gunakan $this->conn bukan $this->db
            if (!$this->conn) {
                throw new Exception("Database connection not established");
            }
            
            // Coba query dari tabel praktikum sesuai struktur Anda
            $sql = "SELECT id, nama_praktikum, semester, tahun_ajaran, status 
                    FROM praktikum 
                    WHERE tahun_ajaran = ? 
                    AND status = 'aktif' 
                    ORDER BY nama_praktikum ASC";
                    
            error_log("SQL Query: " . $sql);
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$tahunAjaran]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format result untuk konsisten dengan expected structure
            $formattedResult = [];
            foreach ($result as $row) {
                $formattedResult[] = [
                    'id' => $row['id'],
                    'nama_praktikum' => $row['nama_praktikum'],
                    'kelas' => '-', // Default value since kelas tidak ada di tabel praktikum
                    'tahun_ajaran' => $row['tahun_ajaran'],
                    'status' => $row['status']
                ];
            }
            
            error_log("Query result: " . count($formattedResult) . " records");
            
            return $formattedResult;
            
        } catch (PDOException $e) {
            error_log("Error in getByTahunAjaran: " . $e->getMessage());
            
            // Fallback: coba dari tabel jadwal_praktikum jika ada
            try {
                $sql = "SELECT id, nama_praktikum, kelas, tahun_ajaran, status 
                        FROM jadwal_praktikum 
                        WHERE tahun_ajaran = ? 
                        AND status = 'active' 
                        LIMIT 10";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$tahunAjaran]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $fallbackError) {
                error_log("Fallback also failed: " . $fallbackError->getMessage());
                return [];
            }
        }
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
}
?>