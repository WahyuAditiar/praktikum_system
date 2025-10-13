<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AbsensiAsistenModel.php';

class AbsensiAsistenModel {
    private $db;
    private $conn;
    private $table = "absen_asisten";

    public function __construct($db) {
        $this->db = $db;}

   public function createRecord($data) {
        $sql = "INSERT INTO {$this->table} 
            (nim, nama, praktikum_id, praktikum_name, kelas, pertemuan, tanggal, jam_mulai, jam_akhir, materi,
            status_hadir, signature_path, foto_path, laporan_path, gps_lat, gps_lng, created_by) 
            VALUES 
            (:nim, :nama, :praktikum_id, :praktikum_name, :kelas, :pertemuan, :tanggal, :jam_mulai, :jam_akhir, :materi, 
            :status_hadir, :signature_path, :foto_path, :laporan_path, :gps_lat, :gps_lng, :created_by)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

     public function updateRecord($id, array $data)
{
    // Pastikan id valid
    if (!$id || empty($data)) {
        return false;
    }

    // Bangun query dinamis sesuai field yang dikirim
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "$key = :$key";
    }

    // Tambahkan timestamp otomatis
    $set[] = "updated_at = CURRENT_TIMESTAMP";

    $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE id = :id";
    $stmt = $this->db->prepare($sql);

    // Tambahkan id ke parameter
    $data['id'] = $id;

    return $stmt->execute($data);
}

    // Hapus absen
    public function deleteRecord($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

     public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Untuk staff_lab: semua; untuk asprak: hanya created_by = username
    public function getAll($onlyOwn = false, $username = null) {
    $sql = "SELECT * FROM {$this->table}";
    
    if ($onlyOwn && $username) {
        $sql .= " WHERE created_by = :created_by";
    }

    $sql .= " ORDER BY tanggal DESC, jam_mulai DESC";

    $stmt = $this->db->prepare($sql);

    if ($onlyOwn && $username) {
        $stmt->bindParam(":created_by", $username);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getCount() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update method getAsistenByNim untuk include tahun_ajaran
public function getAsistenByNim($nim) {
    $sql = "SELECT a.nim, a.nama, a.kelas, a.tahun_ajaran, p.id as praktikum_id, p.nama_praktikum
            FROM asisten_praktikum a
            LEFT JOIN praktikum p ON a.praktikum_id = p.id
            WHERE a.nim = :nim AND a.status = 'active'
            ORDER BY a.tahun_ajaran DESC
            LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':nim', $nim);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getAllAsistenDropdown() {
    $query = "SELECT 
                a.id,
                a.nim,
                a.nama,
                a.kelas,
                
                p.id AS praktikum_id,
                p.nama_praktikum
              FROM asisten_praktikum a
              JOIN praktikum p ON a.praktikum_id = p.id
              WHERE a.status = 'active'
              ORDER BY a.nama ASC";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getAbsensiList($nim = null) {
    if ($nim) {
        $stmt = $this->db->prepare("SELECT * FROM absen_asisten WHERE nim = ?");
        $stmt->execute([$nim]);
    } else {
        $stmt = $this->db->prepare("SELECT * FROM absen_asisten");
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}    
public function getByFilter($filters) {
    $sql = "SELECT * FROM absensi_asisten WHERE 1=1";
    foreach ($filters as $k => $v) {
        $sql .= " AND $k='" . $this->db->real_escape_string($v) . "'";
    }
    $sql .= " LIMIT 1";

    $result = $this->db->query($sql);
    return $result->fetch_assoc();
}

public function findOne(array $criteria) {
    $sql = "SELECT * FROM {$this->table} WHERE 1=1";
    $params = [];
    
    foreach ($criteria as $k => $v) {
        // Skip empty criteria
        if ($v === null || $v === '') continue;
        
        $sql .= " AND `$k` = :$k";
        $params[":$k"] = $v;
    }
    
    $sql .= " ORDER BY id DESC LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Tambahkan method ini di class AbsensiAsistenModel
public function getRiwayatTahunAjaranByNim($nim) {
    $sql = "SELECT DISTINCT tahun_ajaran 
            FROM asisten_praktikum 
            WHERE nim = :nim 
            ORDER BY tahun_ajaran DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':nim', $nim);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}



}


