<?php

class AbsensiModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /** 
     * Ambil semua jadwal praktikum (untuk dropdown)
     * Termasuk nama praktikum & kode_random 
     */
    public function getJadwalPraktikum() {
        $query = "
            SELECT 
                jp.id, 
                p.nama_praktikum,
                jp.kode_random, 
                jp.kelas, 
                jp.hari, 
                jp.jam_mulai, 
                jp.jam_selesai,
                jp.praktikum_id
            FROM jadwal_praktikum jp
            LEFT JOIN praktikum p ON jp.praktikum_id = p.id
            WHERE jp.status = 'active'
            ORDER BY p.nama_praktikum ASC, jp.kelas ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 
     * Validasi kode random 
     * Return detail jadwal kalau benar 
     */
    public function validasiKodeRandom($jadwal_id, $kode_random) {
        $query = "SELECT * FROM jadwal_praktikum WHERE id = :id AND kode_random = :kode_random";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $jadwal_id);
        $stmt->bindParam(':kode_random', $kode_random);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPraktikumFromMahasiswa() {
        $query = "SELECT 
                    p.*, 
                    mk.nama_mk,
                    mk.kode_mk,
                    CASE 
                        WHEN mk.nama_mk LIKE 'Prak.%' THEN CONCAT(mk.nama_mk, ' - ', p.nama_praktikum)
                        ELSE CONCAT('Prak. ', mk.nama_mk, ' - ', p.nama_praktikum)
                    END as display_name
                  FROM praktikum p 
                  JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id 
                  WHERE p.status = 'aktif'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJadwalByPraktikum($praktikum_id) {
        if (empty($praktikum_id)) return [];
        
        $query = "SELECT jp.*, p.nama_praktikum, mk.nama_mk, mk.kode_mk
                  FROM jadwal_praktikum jp
                  JOIN praktikum p ON jp.praktikum_id = p.id
                  JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                  WHERE jp.praktikum_id = ? AND jp.status = 'active'";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$praktikum_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** 
     * ✅ PERBAIKAN: Ambil mahasiswa berdasarkan jadwal praktikum & KELAS
     */
    public function getMahasiswaByJadwal($jadwal_id) {
        // Dapatkan praktikum_id dan KELAS dari jadwal_praktikum
        $query_jadwal = "SELECT praktikum_id, kelas FROM jadwal_praktikum WHERE id = ?";
        $stmt_jadwal = $this->db->prepare($query_jadwal);
        $stmt_jadwal->execute([$jadwal_id]);
        $jadwal = $stmt_jadwal->fetch(PDO::FETCH_ASSOC);
        
        if (!$jadwal || !isset($jadwal['praktikum_id']) || !isset($jadwal['kelas'])) {
            error_log("Jadwal tidak ditemukan atau tidak memiliki kelas: " . $jadwal_id);
            return [];
        }
        
        $praktikum_id = $jadwal['praktikum_id'];
        $kelas = $jadwal['kelas'];
        
        // ✅ PERBAIKAN: Ambil mahasiswa berdasarkan praktikum_id DAN kelas
        $query = "SELECT * FROM mahasiswa WHERE praktikum_id = ? AND kelas = ? ORDER BY nim ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$praktikum_id, $kelas]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Found " . count($result) . " mahasiswa for praktikum_id: $praktikum_id, kelas: $kelas");
        return $result;
    }

    /** 
     * Ambil data absensi berdasarkan jadwal & pertemuan 
     */
    public function getAbsensi($jadwal_id, $pertemuan) {
        $query = "
            SELECT * 
            FROM absensi 
            WHERE jadwal_praktikum_id = :jadwal_id 
              AND pertemuan = :pertemuan
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':jadwal_id', $jadwal_id);
        $stmt->bindParam(':pertemuan', $pertemuan);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 
     * Simpan atau update absensi 
     */
    public function simpanAbsensi($data) {
        // Ambil data tambahan dari database
        $query_mahasiswa = "SELECT nama FROM mahasiswa WHERE id = ?";
        $stmt_mahasiswa = $this->db->prepare($query_mahasiswa);
        $stmt_mahasiswa->execute([$data['mahasiswa_id']]);
        $mahasiswa = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC);
        
        // Ambil data praktikum dan kelas dari jadwal_praktikum
        $query_praktikum = "SELECT p.nama_praktikum, mk.nama_mk, jp.kelas 
                           FROM praktikum p 
                           JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                           JOIN jadwal_praktikum jp ON p.id = jp.praktikum_id
                           WHERE jp.id = ?";
        $stmt_praktikum = $this->db->prepare($query_praktikum);
        $stmt_praktikum->execute([$data['jadwal_praktikum_id']]);
        $praktikum = $stmt_praktikum->fetch(PDO::FETCH_ASSOC);
        
        // Cek apakah data absensi sudah ada
        $query_cek = "SELECT id FROM absensi 
                      WHERE mahasiswa_id = ? 
                      AND jadwal_praktikum_id = ? 
                      AND pertemuan = ?";
        $stmt_cek = $this->db->prepare($query_cek);
        $stmt_cek->execute([
            $data['mahasiswa_id'],
            $data['jadwal_praktikum_id'],
            $data['pertemuan']
        ]);
        $existing = $stmt_cek->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // UPDATE jika data sudah ada
            $query = "UPDATE absensi 
                      SET status = ?, 
                          keterangan = ?,
                          nama_mahasiswa = ?,
                          nama_praktikum = ?,
                          kelas = ?,
                          tanggal = ?,
                          updated_at = NOW()
                      WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['status'],
                $data['keterangan'],
                $mahasiswa['nama'] ?? '',
                $praktikum['nama_mk'] ?? $praktikum['nama_praktikum'] ?? 'Praktikum',
                $praktikum['kelas'] ?? '',
                $data['tanggal'],
                $existing['id']
            ]);
        } else {
            // INSERT jika data baru
            $query = "INSERT INTO absensi 
                      (praktikum_id, nama_praktikum, kelas, mahasiswa_id, nama_mahasiswa, 
                       jadwal_praktikum_id, pertemuan, tanggal, status, keterangan, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['praktikum_id'],
                $praktikum['nama_mk'] ?? $praktikum['nama_praktikum'] ?? 'Praktikum',
                $praktikum['kelas'] ?? '',
                $data['mahasiswa_id'],
                $mahasiswa['nama'] ?? '',
                $data['jadwal_praktikum_id'],
                $data['pertemuan'],
                $data['tanggal'],
                $data['status'],
                $data['keterangan']
            ]);
        }
    }

    /** 
     * Ambil detail jadwal praktikum untuk tampilan 
     */
    public function getDetailJadwal($jadwal_id) {
        $query = "
            SELECT jp.*, p.nama_praktikum, p.id as praktikum_id
            FROM jadwal_praktikum jp
            JOIN praktikum p ON jp.praktikum_id = p.id
            WHERE jp.id = :id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $jadwal_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPraktikumByProdi($prodi) {
        $query = "SELECT id, nama_praktikum 
                  FROM praktikum 
                  WHERE status = 'aktif' 
                  AND EXISTS (
                      SELECT 1 FROM mahasiswa 
                      WHERE mahasiswa.praktikum_id = praktikum.id 
                      AND mahasiswa.prodi = :prodi
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':prodi', $prodi);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRekapAbsensi($jadwal_id, $pertemuan) {
        $query = "SELECT 
                    a.*,
                    m.nama as nama_mahasiswa,
                    m.nim,
                    mk.nama_mk as nama_praktikum,
                    jp.kelas
                  FROM absensi a
                  JOIN mahasiswa m ON a.mahasiswa_id = m.id
                  JOIN praktikum p ON a.praktikum_id = p.id
                  JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                  JOIN jadwal_praktikum jp ON a.jadwal_praktikum_id = jp.id
                  WHERE a.jadwal_praktikum_id = ? AND a.pertemuan = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$jadwal_id, $pertemuan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 
     * ✅ FUNCTION BARU: Untuk debug - cek data mahasiswa berdasarkan praktikum & kelas
     */
    public function debugMahasiswaByPraktikumKelas($praktikum_id, $kelas) {
        $query = "SELECT COUNT(*) as total, kelas FROM mahasiswa WHERE praktikum_id = ? AND kelas = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$praktikum_id, $kelas]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}