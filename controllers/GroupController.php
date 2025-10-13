<?php
require_once __DIR__ . '/../models/GroupModel.php';
require_once __DIR__ . '/../config/database.php';

class GroupController {
    private $groupModel;
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->groupModel = new GroupModel($this->db);
    }

    // ===============================
    // 📋 CONFIG GROUP
    // ===============================
    public function config() {
        // Cek session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Batasi akses
        if (!in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
            $_SESSION['error'] = "Anda tidak memiliki akses ke halaman ini";
            header("Location: ?page=dashboard");
            exit;
        }

        $praktikum_id = $_GET['praktikum_id'] ?? null;
        $kelas = $_GET['kelas'] ?? null;

        // Ambil semua praktikum untuk dropdown
        $all_praktikum = $this->getAllPraktikum();

        // Jika sudah pilih praktikum & kelas
        if ($praktikum_id && $kelas) {
            // Ambil jadwal berdasarkan praktikum & kelas
            $jadwal = $this->getJadwalByPraktikumKelas($praktikum_id, $kelas);
            
            if ($jadwal) {
                $data = [
                    'jadwal' => $jadwal,
                    'mahasiswa' => $this->getMahasiswaByJadwal($jadwal['id']),
                    'aspraks' => $this->getAsprakByJadwal($jadwal['id']),
                    'group_config' => $this->groupModel->getGroupConfig($jadwal['id']),
                    'user_role' => $_SESSION['role'],
                    'all_praktikum' => $all_praktikum
                ];
            } else {
                $data = [
                    'all_praktikum' => $all_praktikum,
                    'user_role' => $_SESSION['role']
                ];
                $_SESSION['error'] = "Jadwal praktikum tidak ditemukan untuk pilihan ini";
            }
        } else {
            // Tampilkan form pilihan
            $data = [
                'all_praktikum' => $all_praktikum,
                'user_role' => $_SESSION['role']
            ];
        }

        require_once __DIR__ . '/../views/staff_lab/group_configuration.php';
    }

    // ===============================
    // 📚 GET ALL PRAKTIKUM - FIXED VERSION
    // ===============================
    private function getAllPraktikum() {
        try {
            // Coba ambil dari tabel praktikum
            $stmt = $this->db->prepare("
                SELECT p.id, p.nama_praktikum, mk.nama_mk
                FROM praktikum p
                LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                WHERE p.status = 'active'
                ORDER BY p.nama_praktikum ASC
            ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($result)) {
                error_log("Found " . count($result) . " praktikum from praktikum table");
                return $result;
            }
            
            // Fallback: coba dari jadwal_praktikum
            $stmt = $this->db->prepare("
                SELECT DISTINCT jp.praktikum_id as id, p.nama_praktikum, mk.nama_mk
                FROM jadwal_praktikum jp
                JOIN praktikum p ON jp.praktikum_id = p.id
                LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                WHERE jp.status = 'active'
                ORDER BY p.nama_praktikum ASC
            ");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Found " . count($result) . " praktikum from jadwal_praktikum table");
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error in getAllPraktikum: " . $e->getMessage());
            return [];
        }
    }

    // ===============================
    // 🔍 GET JADWAL BY PRAKTIKUM & KELAS
    // ===============================
    private function getJadwalByPraktikumKelas($praktikum_id, $kelas) {
        try {
            $stmt = $this->db->prepare("
                SELECT jp.*, p.nama_praktikum, mk.nama_mk 
                FROM jadwal_praktikum jp
                JOIN praktikum p ON jp.praktikum_id = p.id
                LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
                WHERE jp.praktikum_id = ? AND jp.kelas = ? AND jp.status = 'active'
                LIMIT 1
            ");
            $stmt->execute([$praktikum_id, $kelas]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("Found jadwal: " . $result['nama_praktikum'] . " - Kelas " . $result['kelas']);
            } else {
                error_log("No jadwal found for praktikum_id: $praktikum_id, kelas: $kelas");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error in getJadwalByPraktikumKelas: " . $e->getMessage());
            return null;
        }
    }

    // ===============================
    // ⚙️ UPDATE KONFIGURASI GROUP
    // ===============================
    public function updateConfig() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses';
            header("Location: ?page=dashboard");
            exit;
        }

        $jadwal_id = $_POST['jadwal_id'] ?? null;
        $total_groups = $_POST['total_groups'] ?? 1;
        $max_mahasiswa = $_POST['max_mahasiswa'] ?? 10;
        $praktikum_id = $_POST['praktikum_id'] ?? null;
        $kelas = $_POST['kelas'] ?? null;

        if (!$jadwal_id) {
            $_SESSION['error'] = "Jadwal praktikum tidak ditemukan";
            header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
            exit;
        }

        $config = [
            'total_groups' => (int)$total_groups,
            'max_mahasiswa_per_group' => (int)$max_mahasiswa,
            'max_asisten_per_group' => 2,
            'auto_assignment' => true
        ];

        if ($this->groupModel->updateGroupConfig($jadwal_id, $config)) {
            $_SESSION['success'] = "Konfigurasi group berhasil diupdate";
        } else {
            $_SESSION['error'] = "Gagal update konfigurasi";
        }

        header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
        exit;
    }

    // ===============================
    // 🤖 PEMBAGIAN OTOMATIS
    // ===============================
   public function bagiOtomatis() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
        $_SESSION['error'] = 'Anda tidak memiliki akses';
        header("Location: ?page=dashboard");
        exit;
    }

    $jadwal_id = $_POST['jadwal_id'] ?? null;
    $praktikum_id = $_POST['praktikum_id'] ?? null;
    $kelas = $_POST['kelas'] ?? null;

    if (!$jadwal_id) {
        $_SESSION['error'] = "Jadwal praktikum tidak ditemukan";
        header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
        exit;
    }

    $config = $this->groupModel->getGroupConfig($jadwal_id);
    $total_groups = $config['total_groups'] ?? 1;

    $mahasiswa = $this->getMahasiswaByJadwal($jadwal_id);
    $aspraks = $this->getAsprakByJadwal($jadwal_id);

    if (empty($mahasiswa)) {
        $_SESSION['error'] = "Tidak ada mahasiswa dalam praktikum ini";
        header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
        exit;
    }

    // Hapus semua assignment sebelumnya
    $this->groupModel->clearAllAssignments($jadwal_id);

    // ✅ PERBAIKAN: BAGI MAHASISWA OTOMATIS - ROUND ROBIN
    foreach ($mahasiswa as $index => $mhs) {
        $group = ($index % $total_groups) + 1;
        $this->groupModel->assignToGroup(
            $jadwal_id, 'mahasiswa', $mhs['id'], $group, $_SESSION['user_id']
        );
    }

    // ✅ PERBAIKAN: BAGI ASISTEN OTOMATIS - ROUND ROBIN JUGA
    if (!empty($aspraks)) {
        error_log("🔄 Membagi " . count($aspraks) . " asisten ke $total_groups group");
        
        foreach ($aspraks as $index => $asprak) {
            $group = ($index % $total_groups) + 1;
            $success = $this->groupModel->assignToGroup(
                $jadwal_id, 'asisten', $asprak['id'], $group, $_SESSION['user_id']
            );
            
            if ($success) {
                error_log("✅ Asisten {$asprak['nama']} di-assign ke Group $group");
            } else {
                error_log("❌ Gagal assign asisten {$asprak['nama']} ke Group $group");
            }
        }
        
        $_SESSION['success'] = "Pembagian group otomatis berhasil! " . 
                              count($mahasiswa) . " mahasiswa dan " . 
                              count($aspraks) . " asisten telah dibagi ke $total_groups group.";
    } else {
        $_SESSION['success'] = "Pembagian mahasiswa berhasil! " . 
                              count($mahasiswa) . " mahasiswa telah dibagi ke $total_groups group. " .
                              "Tidak ada asisten yang dibagi.";
    }

    header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
    exit;
}

    // ===============================
    // 🗑️ RESET ASSIGNMENTS
    // ===============================
    public function resetAssignments() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses';
            header("Location: ?page=dashboard");
            exit;
        }

        $jadwal_id = $_POST['jadwal_id'] ?? null;
        $praktikum_id = $_POST['praktikum_id'] ?? null;
        $kelas = $_POST['kelas'] ?? null;

        if (!$jadwal_id) {
            $_SESSION['error'] = "Jadwal praktikum tidak ditemukan";
            header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
            exit;
        }

        if ($this->groupModel->clearAllAssignments($jadwal_id)) {
            $_SESSION['success'] = "Semua penugasan berhasil direset";
        } else {
            $_SESSION['error'] = "Gagal reset penugasan";
        }

        header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
        exit;
    }

    // ===============================
    // 🧩 ASSIGN MANUAL
    // ===============================
    public function assignManual() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses';
            header("Location: ?page=dashboard");
            exit;
        }

        $jadwal_id = $_GET['jadwal_id'] ?? null;
        $group_number = $_GET['group'] ?? null;
        $praktikum_id = $_GET['praktikum_id'] ?? null;
        $kelas = $_GET['kelas'] ?? null;

        if (!$jadwal_id || !$group_number) {
            $_SESSION['error'] = "Parameter tidak lengkap";
            header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
            exit;
        }

        $data = [
            'jadwal' => $this->getJadwalPraktikum($jadwal_id),
            'group_number' => $group_number,
            'mahasiswa' => $this->getMahasiswaByJadwal($jadwal_id),
            'aspraks' => $this->getAsprakByJadwal($jadwal_id),
            'assigned_mahasiswa' => $this->groupModel->getMahasiswaByGroup($jadwal_id, $group_number),
            'assigned_aspraks' => $this->groupModel->getAsprakByGroup($jadwal_id, $group_number),
            'user_role' => $_SESSION['role'],
            'praktikum_id' => $praktikum_id,
            'kelas' => $kelas
        ];

        require_once __DIR__ . '/../views/staff_lab/assign_group.php';
    }

    // ===============================
    // 🔄 UPDATE ASSIGNMENTS
    // ===============================
    public function updateAssignments() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
            $_SESSION['error'] = 'Anda tidak memiliki akses';
            header("Location: ?page=dashboard");
            exit;
        }

        $jadwal_id = $_POST['jadwal_id'] ?? null;
        $group_number = $_POST['group_number'] ?? null;
        $entity_type = $_POST['entity_type'] ?? null;
        $praktikum_id = $_POST['praktikum_id'] ?? null;
        $kelas = $_POST['kelas'] ?? null;

        if (!$jadwal_id || !$group_number || !$entity_type) {
            $_SESSION['error'] = "Parameter tidak lengkap";
            header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
            exit;
        }

        // Hapus semua assignment untuk entity type ini di group ini
        $this->clearGroupAssignments($jadwal_id, $group_number, $entity_type);

        // Mahasiswa
        if ($entity_type === 'mahasiswa' && isset($_POST['assign_mahasiswa'])) {
            foreach ($_POST['assign_mahasiswa'] as $mahasiswa_id) {
                $this->groupModel->assignToGroup(
                    $jadwal_id, 'mahasiswa', $mahasiswa_id, $group_number, $_SESSION['user_id']
                );
            }
            $_SESSION['success'] = "Assign mahasiswa berhasil diupdate";
        }

        // Asisten
        if ($entity_type === 'asisten' && isset($_POST['assign_asprak'])) {
            foreach ($_POST['assign_asprak'] as $asprak_id) {
                $this->groupModel->assignToGroup(
                    $jadwal_id, 'asisten', $asprak_id, $group_number, $_SESSION['user_id']
                );
            }
            $_SESSION['success'] = "Assign asisten berhasil diupdate";
        }

        header("Location: ?page=group&action=assignManual&jadwal_id=$jadwal_id&group=$group_number&praktikum_id=$praktikum_id&kelas=$kelas");
        exit;
    }

    // ===============================
    // 🗑️ CLEAR GROUP ASSIGNMENTS
    // ===============================
    private function clearGroupAssignments($jadwal_id, $group_number, $entity_type) {
        $query = "DELETE FROM praktikum_group_assignment 
                  WHERE jadwal_praktikum_id = ? 
                  AND group_number = ?
                  AND entity_type = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$jadwal_id, $group_number, $entity_type]);
    }

    // ===============================
    // 🔍 HELPER FUNCTIONS
    // ===============================
    private function getJadwalPraktikum($id) {
        $stmt = $this->db->prepare("
            SELECT jp.*, p.nama_praktikum, mk.nama_mk 
            FROM jadwal_praktikum jp
            JOIN praktikum p ON jp.praktikum_id = p.id
            LEFT JOIN mata_kuliah mk ON p.mata_kuliah_id = mk.id
            WHERE jp.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    private function getMahasiswaByJadwal($jadwal_id) {
        $jadwal = $this->getJadwalPraktikum($jadwal_id);
        if (!$jadwal) return [];
        
        $praktikum_id = $jadwal['praktikum_id'];
        $kelas = $jadwal['kelas'];
        
        $stmt = $this->db->prepare("
            SELECT m.* 
            FROM mahasiswa m 
            WHERE m.praktikum_id = ? 
            AND m.kelas = ?
            ORDER BY m.nama ASC
        ");
        $stmt->execute([$praktikum_id, $kelas]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAsprakByJadwal($jadwal_id) {
        $jadwal = $this->getJadwalPraktikum($jadwal_id);
        if (!$jadwal) return [];
        
        $praktikum_id = $jadwal['praktikum_id'];
        
        $stmt = $this->db->prepare("
            SELECT ap.* 
            FROM asisten_praktikum ap 
            WHERE ap.praktikum_id = ?
            ORDER BY ap.nama ASC
        ");
        $stmt->execute([$praktikum_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // 📊 INDEX (Redirect ke config)
    // ===============================
    public function index() {
        $this->config();
    }

    // ===============================
// 👥 ASSIGN ALL ASPRAK KE GROUP
// ===============================
public function assignAllAsprak() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!in_array($_SESSION['role'], ['admin', 'staff_lab'])) {
        $_SESSION['error'] = 'Anda tidak memiliki akses';
        header("Location: ?page=dashboard");
        exit;
    }

    $jadwal_id = $_POST['jadwal_id'] ?? null;
    $group_number = $_POST['group_number'] ?? null;
    $praktikum_id = $_POST['praktikum_id'] ?? null;
    $kelas = $_POST['kelas'] ?? null;

    if (!$jadwal_id || !$group_number) {
        $_SESSION['error'] = "Parameter tidak lengkap";
        header("Location: ?page=group&action=config&praktikum_id=" . $praktikum_id . "&kelas=" . $kelas);
        exit;
    }

    // Ambil semua asisten untuk jadwal ini
    $aspraks = $this->getAsprakByJadwal($jadwal_id);
    
    if (empty($aspraks)) {
        $_SESSION['error'] = "Tidak ada asisten yang ditemukan untuk praktikum ini";
        header("Location: ?page=group&action=assignManual&jadwal_id=$jadwal_id&group=$group_number&praktikum_id=$praktikum_id&kelas=$kelas");
        exit;
    }

    // Assign semua asisten ke group yang dipilih
    $success_count = 0;
    foreach ($aspraks as $asprak) {
        $success = $this->groupModel->assignToGroup(
            $jadwal_id, 'asisten', $asprak['id'], $group_number, $_SESSION['user_id']
        );
        if ($success) {
            $success_count++;
        }
    }

    if ($success_count > 0) {
        $_SESSION['success'] = "Berhasil assign $success_count asisten ke Group $group_number";
    } else {
        $_SESSION['error'] = "Gagal assign asisten ke group";
    }

    header("Location: ?page=group&action=assignManual&jadwal_id=$jadwal_id&group=$group_number&praktikum_id=$praktikum_id&kelas=$kelas");
    exit;
}
}
?>