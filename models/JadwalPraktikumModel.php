<?php
// models/JadwalPraktikumModel.php
class JadwalPraktikumModel {
    private $conn;
    private $table = 'jadwal_praktikum';

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getAllJadwal() {
        $sql = "SELECT jp.*, p.nama_praktikum,
                       d.nama AS nama_dosen,
                       r.kode_ruangan
                FROM {$this->table} jp
                LEFT JOIN praktikum p ON jp.praktikum_id = p.id
                LEFT JOIN dosen d ON jp.dosen_id = d.id
                LEFT JOIN ruangan r ON jp.ruangan_id = r.id
                ORDER BY FIELD(jp.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jp.jam_mulai";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJadwalById($id) {
        $sql = "SELECT jp.*, p.nama_praktikum,
                       d.nama AS nama_dosen,
                       r.kode_ruangan, r.nama_ruangan
                FROM {$this->table} jp
                LEFT JOIN praktikum p ON jp.praktikum_id = p.id
                LEFT JOIN dosen d ON jp.dosen_id = d.id
                LEFT JOIN ruangan r ON jp.ruangan_id = r.id
                WHERE jp.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // create expects $data array (praktikum_id, dosen_id, ruangan_id, hari, jam_mulai, jam_selesai, kelas, group, kode_random?, status?)
    public function createJadwal(array $data) {
        // Generate kode_random jika belum ada
        if (empty($data['kode_random'])) {
            $data['kode_random'] = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6);
        }
        // Set absen_open_until 1 jam dari sekarang jika belum ada
        if (empty($data['absen_open_until'])) {
            $data['absen_open_until'] = date('Y-m-d H:i:s', strtotime('+1 hour'));
        }
        $sql = "INSERT INTO {$this->table}
                (praktikum_id, dosen_id, ruangan_id, hari, jam_mulai, jam_selesai, kelas, `group`, kode_random, absen_open_until, status)
                VALUES (:praktikum_id, :dosen_id, :ruangan_id, :hari, :jam_mulai, :jam_selesai, :kelas, :group, :kode_random, :absen_open_until, :status)";
        $stmt = $this->conn->prepare($sql);
        $params = [
            ':praktikum_id' => $data['praktikum_id'],
            ':dosen_id'     => $data['dosen_id'],
            ':ruangan_id'   => $data['ruangan_id'],
            ':hari'         => $data['hari'],
            ':jam_mulai'    => $data['jam_mulai'],
            ':jam_selesai'  => $data['jam_selesai'],
            ':kelas'        => $data['kelas'],
            ':group'        => isset($data['group']) && $data['group'] !== '' ? $data['group'] : '',
            ':kode_random'  => $data['kode_random'],
            ':absen_open_until' => $data['absen_open_until'],
            ':status'       => $data['status'] ?? 'active'
        ];
        return $stmt->execute($params);
    }

    // update expects $data array (same keys as create). returns boolean
    public function updateJadwal($id, array $data) {
        $sql = "UPDATE {$this->table} SET
                    praktikum_id = :praktikum_id,
                    dosen_id = :dosen_id,
                    ruangan_id = :ruangan_id,
                    hari = :hari,
                    jam_mulai = :jam_mulai,
                    jam_selesai = :jam_selesai,
                    kelas = :kelas,
                    `group` = :group,
                    kode_random = :kode_random,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $params = [
            ':praktikum_id' => $data['praktikum_id'],
            ':dosen_id'     => $data['dosen_id'],
            ':ruangan_id'   => $data['ruangan_id'],
            ':hari'         => $data['hari'],
            ':jam_mulai'    => $data['jam_mulai'],
            ':jam_selesai'  => $data['jam_selesai'],
            ':kelas'        => $data['kelas'],
            ':group'        => $data['group'] ?? null,
            ':kode_random'  => $data['kode_random'] ?? null,
            ':status'       => $data['status'] ?? 'active',
            ':id'           => $id
        ];
        return $stmt->execute($params);
    }

    public function deleteJadwal($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // try update kode_random and absen_open_until (if column exists). fallback to updating kode_random only
    public function updateKodeRandom($id, $kode_random, $minutes = 30) {
        // first try with absen_open_until (if table has it)
        try {
            $sql = "UPDATE {$this->table} SET kode_random = :kode_random, absen_open_until = DATE_ADD(NOW(), INTERVAL :minutes MINUTE), updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':kode_random', $kode_random);
            $stmt->bindValue(':minutes', (int)$minutes, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // fallback: update only kode_random
            $sql2 = "UPDATE {$this->table} SET kode_random = :kode_random, updated_at = NOW() WHERE id = :id";
            $stmt2 = $this->conn->prepare($sql2);
            return $stmt2->execute([':kode_random' => $kode_random, ':id' => $id]);
        }
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function checkScheduleConflict($ruangan_id, $hari, $jam_mulai, $jam_selesai, $exclude_id = null) {
        $sql = "SELECT id FROM {$this->table}
                WHERE ruangan_id = :ruangan_id
                  AND hari = :hari
                  AND status != 'inactive'
                  AND (
                        (jam_mulai BETWEEN :jam_mulai AND :jam_selesai)
                        OR (jam_selesai BETWEEN :jam_mulai AND :jam_selesai)
                        OR (:jam_mulai BETWEEN jam_mulai AND jam_selesai)
                  )";
        if ($exclude_id) $sql .= " AND id != :exclude_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ruangan_id', $ruangan_id);
        $stmt->bindValue(':hari', $hari);
        $stmt->bindValue(':jam_mulai', $jam_mulai);
        $stmt->bindValue(':jam_selesai', $jam_selesai);
        if ($exclude_id) $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function checkDosenAvailability($dosen_id, $hari, $jam_mulai, $jam_selesai, $exclude_id = null) {
        $sql = "SELECT id FROM {$this->table}
                WHERE dosen_id = :dosen_id
                  AND hari = :hari
                  AND status != 'inactive'
                  AND (
                        (jam_mulai BETWEEN :jam_mulai AND :jam_selesai)
                        OR (jam_selesai BETWEEN :jam_mulai AND :jam_selesai)
                        OR (:jam_mulai BETWEEN jam_mulai AND jam_selesai)
                  )";
        if ($exclude_id) $sql .= " AND id != :exclude_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':dosen_id', $dosen_id);
        $stmt->bindValue(':hari', $hari);
        $stmt->bindValue(':jam_mulai', $jam_mulai);
        $stmt->bindValue(':jam_selesai', $jam_selesai);
        if ($exclude_id) $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getCountByStatus() {
        $sql = "SELECT status, COUNT(*) as total FROM {$this->table} GROUP BY status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCountByHari() {
        $sql = "SELECT hari, COUNT(*) as total FROM {$this->table} WHERE status = 'active' GROUP BY hari
                ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJadwalByKode($kode_random) {
        $sql = "SELECT jp.*, p.nama_praktikum,
                       d.nama AS nama_dosen,
                       r.kode_ruangan, r.nama_ruangan
                FROM {$this->table} jp
                LEFT JOIN praktikum p ON jp.praktikum_id = p.id
                LEFT JOIN dosen d ON jp.dosen_id = d.id
                LEFT JOIN ruangan r ON jp.ruangan_id = r.id
                WHERE jp.kode_random = :kode_random
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':kode_random', $kode_random);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
