<?php
class AbsensiPraktikanModel {
    private $conn;
    private $table_name = "absensi_praktikan";

    public function __construct($db) {
        $this->conn = $db;
    }


    // Tambah absen praktikan dengan field pertemuan, tanggal, mahasiswa_id
    public function createAbsenWithPertemuan($nim, $jadwal_praktikum_id, $pertemuan, $status, $keterangan, $created_by) {
        // Ambil mahasiswa_id dari tabel mahasiswa
        $stmt = $this->conn->prepare("SELECT id FROM mahasiswa WHERE nim = :nim LIMIT 1");
        $stmt->execute([':nim' => $nim]);
        $mhs = $stmt->fetch(PDO::FETCH_ASSOC);
        $mahasiswa_id = $mhs ? $mhs['id'] : null;
        if (!$mahasiswa_id) return false;
        $query = "INSERT INTO $this->table_name (jadwal_praktikum_id, pertemuan, mahasiswa_id, tanggal, status, keterangan, created_at) VALUES (:jadwal_praktikum_id, :pertemuan, :mahasiswa_id, :tanggal, :status, :keterangan, NOW())";
        $stmt2 = $this->conn->prepare($query);
        return $stmt2->execute([
            ':jadwal_praktikum_id' => $jadwal_praktikum_id,
            ':pertemuan' => $pertemuan,
            ':mahasiswa_id' => $mahasiswa_id,
            ':tanggal' => date('Y-m-d'),
            ':status' => $status,
            ':keterangan' => $keterangan
        ]);
    }

    // Ambil absen berdasarkan jadwal
    public function getAbsenByJadwal($jadwal_praktikum_id) {
        $query = "SELECT a.*, m.nama FROM $this->table_name a JOIN mahasiswa m ON a.nim = m.nim WHERE a.jadwal_praktikum_id = :jadwal_praktikum_id ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':jadwal_praktikum_id', $jadwal_praktikum_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil semua absen (untuk admin)
    public function getAllAbsen() {
        $query = "SELECT a.*, m.nama, j.kode as kode_jadwal FROM $this->table_name a JOIN mahasiswa m ON a.nim = m.nim JOIN jadwal_praktikum j ON a.jadwal_praktikum_id = j.id ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
