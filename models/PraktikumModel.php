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


}
?>
