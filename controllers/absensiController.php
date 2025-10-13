<?php
require_once __DIR__ . '/../models/absensiModel.php';
require_once __DIR__ . '/../models/GroupModel.php';

class AbsensiController {
    private $model;
    private $role;
    private $groupModel;
    private $db;

    public function __construct($db, $role = null) {
        $this->db = $db;
        $this->model = new AbsensiModel($db);
        $this->role = $role;
        $this->groupModel = new GroupModel($db);
    }

    /* ------------------ wrapper untuk view (gunakan ini di view) ------------------ */
    public function getJadwalPraktikum() {
        return $this->model->getJadwalPraktikum();
    }

    public function validateKodeRandom($jadwal_id, $kode_random) {
        return $this->model->validasiKodeRandom($jadwal_id, $kode_random);
    }

    // ✅ PERBAIKI FUNCTION INI - VERSI FIXED
    public function getMahasiswaByJadwal($jadwal_id) {
        error_log("🎯 getMahasiswaByJadwal - Role: {$this->role}, Jadwal: {$jadwal_id}");
        
        // JIKA ASISTEN PRAKTIKUM, FILTER BY GROUP
        if ($this->role == 'asisten_praktikum') {
            $user_id = $_SESSION['user_id'] ?? null;
            
            if ($user_id) {
                // CARI GROUP ASISTEN DI JADWAL INI
                $asprak_group = $this->groupModel->getAsprakGroup($user_id, $jadwal_id);
                
                if ($asprak_group) {
                    error_log("✅ Asprak di group: $asprak_group");
                    $mahasiswa_group = $this->groupModel->getMahasiswaByGroup($jadwal_id, $asprak_group);
                    error_log("📊 Mahasiswa di group: " . count($mahasiswa_group) . " orang");
                    return $mahasiswa_group;
                } else {
                    error_log("❌ Asprak tidak punya group, fallback ke semua mahasiswa");
                    // FALLBACK: Jika asisten tidak punya group, tampilkan semua mahasiswa
                    return $this->getAllMahasiswaByJadwal($jadwal_id);
                }
            }
        }
        
        // JIKA ADMIN/DOSEN/STAFF LAB, AMBIL SEMUA MAHASISWA DI KELAS INI
        return $this->getAllMahasiswaByJadwal($jadwal_id);
    }

    // ✅ FUNCTION BARU: CARI GROUP ASISTEN DARI TABEL ASSIGNMENT
    private function getAsprakGroupFromAssignment($user_id, $jadwal_id) {
        try {
            // Cari asisten_praktikum_id dari user_id
            $stmt = $this->db->prepare("
                SELECT id FROM asisten_praktikum WHERE user_id = ? LIMIT 1
            ");
            $stmt->execute([$user_id]);
            $asprak = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$asprak) {
                error_log("Asisten praktikum tidak ditemukan untuk user_id: $user_id");
                return null;
            }
            
            $asprak_id = $asprak['id'];
            
            // Cari group assignment untuk asisten ini di jadwal tertentu
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
                return $assignment['group_number'];
            }
            
            error_log("Tidak ada assignment untuk asisten_id: $asprak_id di jadwal: $jadwal_id");
            return null;
            
        } catch (PDOException $e) {
            error_log("Error in getAsprakGroupFromAssignment: " . $e->getMessage());
            return null;
        }
    }

    // ✅ PERBAIKI FUNCTION INI - AMBIL MAHASISWA BERDASARKAN KELAS
    private function getAllMahasiswaByJadwal($jadwal_id) {
        try {
            // Dapatkan detail jadwal untuk mengetahui praktikum_id dan kelas
            $detail = $this->getDetailJadwal($jadwal_id);
            if (!$detail) {
                error_log("Jadwal tidak ditemukan: $jadwal_id");
                return [];
            }
            
            $praktikum_id = $detail['praktikum_id'];
            $kelas = $detail['kelas'];
            
            $stmt = $this->db->prepare("
                SELECT m.* 
                FROM mahasiswa m 
                WHERE m.praktikum_id = ? 
                AND m.kelas = ?
                ORDER BY m.nim ASC
            ");
            $stmt->execute([$praktikum_id, $kelas]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Found " . count($result) . " mahasiswa for praktikum_id: $praktikum_id, kelas: $kelas");
            return $result;
            
        } catch (PDOException $e) {
            error_log("Error in getAllMahasiswaByJadwal: " . $e->getMessage());
            return [];
        }
    }

    // ✅ PERBAIKI: GANTI $this->conn MENJADI $this->db
    public function getAbsensi($jadwal_id, $pertemuan, $group_id = null) {
        try {
            if ($group_id) {
                // Filter by group - PERBAIKAN: gunakan $this->db
                $query = "SELECT * FROM absensi 
                         WHERE jadwal_praktikum_id = ? 
                         AND pertemuan = ? 
                         AND group_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$jadwal_id, $pertemuan, $group_id]);
            } else {
                // Tanpa filter group (backward compatibility)
                $query = "SELECT * FROM absensi 
                         WHERE jadwal_praktikum_id = ? 
                         AND pertemuan = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$jadwal_id, $pertemuan]);
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAbsensi: " . $e->getMessage());
            return [];
        }
    }

    public function getDetailJadwal($jadwal_id) {
        return $this->model->getDetailJadwal($jadwal_id);
    }

    public function getPraktikumFromMahasiswa() {
        return $this->model->getPraktikumFromMahasiswa();
    }

    public function getJadwalByPraktikum($praktikum_id) {
        return $this->model->getJadwalByPraktikum($praktikum_id);
    }

    /* ------------------ handling form POST (router memanggil ini) ------------------ */
    // ✅ PERBAIKI: GANTI $this->conn MENJADI $this->db
   // ✅ PERBAIKI METHOD simpan() - SESUAIKAN DENGAN STRUKTUR TABEL
// ✅ PERBAIKI METHOD simpan() - HANDLE FOREIGN KEY CONSTRAINT
public function simpan() {
    try {
        $jadwal_id = $_POST['jadwal_praktikum_id'] ?? '';
        $pertemuan = $_POST['pertemuan'] ?? '';
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $absensi_data = $_POST['absensi'] ?? [];
        
        // Dapatkan group_id dari ASPRAK yang login
        $groupModel = new GroupModel($this->db);
        $group_id = $groupModel->getAsprakGroup($_SESSION['user_id'], $jadwal_id);
        
        if (!$group_id) {
            $_SESSION['error'] = "Anda belum ditugaskan di group manapun!";
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit;
        }
        
        // Dapatkan praktikum_id dari jadwal
        $detail_jadwal = $this->getDetailJadwal($jadwal_id);
        if (!$detail_jadwal || !isset($detail_jadwal['praktikum_id'])) {
            $_SESSION['error'] = "Tidak dapat menemukan data praktikum untuk jadwal ini!";
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit;
        }
        
        $praktikum_id = $detail_jadwal['praktikum_id'];
        error_log("🔍 Praktikum ID: $praktikum_id, Jadwal ID: $jadwal_id");
        
        // Hanya simpan untuk mahasiswa di group ini
        $mahasiswa_in_group = $groupModel->getMahasiswaByGroup($jadwal_id, $group_id);
        $mahasiswa_ids = array_column($mahasiswa_in_group, 'id');
        
        error_log("📊 Mahasiswa in group $group_id: " . count($mahasiswa_ids));
        
        if (!empty($mahasiswa_ids)) {
            // Hapus hanya absensi untuk mahasiswa di group ini
            $placeholders = str_repeat('?,', count($mahasiswa_ids) - 1) . '?';
            $deleteQuery = "DELETE FROM absensi 
                           WHERE jadwal_praktikum_id = ? 
                           AND pertemuan = ? 
                           AND mahasiswa_id IN ($placeholders)";
            
            $deleteParams = array_merge([$jadwal_id, $pertemuan], $mahasiswa_ids);
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->execute($deleteParams);
            
            error_log("🗑️ Deleted existing absensi for group $group_id");
        }
        
        // CEK STRUKTUR TABEL ABSENSI TERLEBIH DAHULU
        $checkTableQuery = "SHOW COLUMNS FROM absensi";
        $checkStmt = $this->db->prepare($checkTableQuery);
        $checkStmt->execute();
        $columns = $checkStmt->fetchAll(PDO::FETCH_COLUMN);
        
        error_log("📋 Columns in absensi table: " . implode(', ', $columns));
        
        $hasPraktikumId = in_array('praktikum_id', $columns);
        $hasGroupId = in_array('group_id', $columns);
        
        error_log("🔍 Table has praktikum_id: " . ($hasPraktikumId ? 'YES' : 'NO'));
        error_log("🔍 Table has group_id: " . ($hasGroupId ? 'YES' : 'NO'));
        
        // SIMPAN ABSENSI BARU DENGAN STRUCTURE YANG SESUAI
        $saved_count = 0;
        
        if ($hasPraktikumId && $hasGroupId) {
            // ✅ VERSI 1: Dengan praktikum_id DAN group_id
            $insertQuery = "INSERT INTO absensi 
                           (praktikum_id, jadwal_praktikum_id, pertemuan, group_id, mahasiswa_id, status, keterangan, tanggal) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $this->db->prepare($insertQuery);
            
            foreach ($absensi_data as $mahasiswa_id => $data) {
                if (in_array($mahasiswa_id, $mahasiswa_ids)) {
                    $insertStmt->execute([
                        $praktikum_id,
                        $jadwal_id,
                        $pertemuan,
                        $group_id,
                        $mahasiswa_id,
                        $data['status'] ?? 'alfa',
                        $data['keterangan'] ?? '',
                        $tanggal
                    ]);
                    $saved_count++;
                }
            }
            
        } elseif ($hasPraktikumId && !$hasGroupId) {
            // ✅ VERSI 2: Hanya dengan praktikum_id (tanpa group_id)
            $insertQuery = "INSERT INTO absensi 
                           (praktikum_id, jadwal_praktikum_id, pertemuan, mahasiswa_id, status, keterangan, tanggal) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $this->db->prepare($insertQuery);
            
            foreach ($absensi_data as $mahasiswa_id => $data) {
                if (in_array($mahasiswa_id, $mahasiswa_ids)) {
                    $insertStmt->execute([
                        $praktikum_id,
                        $jadwal_id,
                        $pertemuan,
                        $mahasiswa_id,
                        $data['status'] ?? 'alfa',
                        $data['keterangan'] ?? '',
                        $tanggal
                    ]);
                    $saved_count++;
                }
            }
            
        } elseif (!$hasPraktikumId && $hasGroupId) {
            // ✅ VERSI 3: Hanya dengan group_id (tanpa praktikum_id)
            $insertQuery = "INSERT INTO absensi 
                           (jadwal_praktikum_id, pertemuan, group_id, mahasiswa_id, status, keterangan, tanggal) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $this->db->prepare($insertQuery);
            
            foreach ($absensi_data as $mahasiswa_id => $data) {
                if (in_array($mahasiswa_id, $mahasiswa_ids)) {
                    $insertStmt->execute([
                        $jadwal_id,
                        $pertemuan,
                        $group_id,
                        $mahasiswa_id,
                        $data['status'] ?? 'alfa',
                        $data['keterangan'] ?? '',
                        $tanggal
                    ]);
                    $saved_count++;
                }
            }
            
        } else {
            // ✅ VERSI 4: Tanpa praktikum_id DAN tanpa group_id
            $insertQuery = "INSERT INTO absensi 
                           (jadwal_praktikum_id, pertemuan, mahasiswa_id, status, keterangan, tanggal) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $this->db->prepare($insertQuery);
            
            foreach ($absensi_data as $mahasiswa_id => $data) {
                if (in_array($mahasiswa_id, $mahasiswa_ids)) {
                    $insertStmt->execute([
                        $jadwal_id,
                        $pertemuan,
                        $mahasiswa_id,
                        $data['status'] ?? 'alfa',
                        $data['keterangan'] ?? '',
                        $tanggal
                    ]);
                    $saved_count++;
                }
            }
        }
        
        error_log("💾 Saved $saved_count attendance records for group $group_id");
        
        $_SESSION['success'] = "Absensi berhasil disimpan untuk Group $group_id! ($saved_count mahasiswa)";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
        
    } catch (PDOException $e) {
        error_log("❌ Error in simpan absensi: " . $e->getMessage());
        $_SESSION['error'] = "Gagal menyimpan absensi: " . $e->getMessage();
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    }
}

    /* ------------------ endpoint AJAX (opsional) ------------------ */
    public function ajaxValidateKodeRandom() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jadwal_id = $_POST['jadwal_id'] ?? null;
            $kode_random = $_POST['kode_random'] ?? null;
            if (!$jadwal_id || !$kode_random) {
                echo json_encode(['status'=>'error','message'=>'Parameter tidak lengkap']);
                return;
            }
            $result = $this->validateKodeRandom($jadwal_id, $kode_random);
            if ($result) echo json_encode(['status'=>'success','jadwal'=>$result]);
            else echo json_encode(['status'=>'error','message'=>'Kode salah']);
        }
    }

    public function ajaxGetMahasiswa() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['jadwal_id'])) {
            $jadwal_id = $_GET['jadwal_id'];
            echo json_encode($this->getMahasiswaByJadwal($jadwal_id));
        }
    }

    public function ajaxGetAbsensi() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $jadwal_id = $_GET['jadwal_id'] ?? null;
            $pertemuan = $_GET['pertemuan'] ?? null;
            if (!$jadwal_id || !$pertemuan) {
                echo json_encode(['status'=>'error','message'=>'Parameter tidak lengkap']);
                return;
            }
            echo json_encode(['status'=>'success','data'=>$this->getAbsensi($jadwal_id,$pertemuan)]);
        }
    }

    public function ajaxSaveAbsensi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payload = $_POST;
            try {
                $jadwal_id = $payload['jadwal_praktikum_id'] ?? null;
                $tanggal = $payload['tanggal'] ?? date('Y-m-d');
                $detail = $this->getDetailJadwal($jadwal_id);
                $praktikum_id = $detail['praktikum_id'] ?? null;

                // ✅ HANYA SIMPAN MAHASISWA YANG BISA DIAKSES OLEH ASISTEN
                $mahasiswa_accessible = $this->getMahasiswaByJadwal($jadwal_id);
                $accessible_ids = array_column($mahasiswa_accessible, 'id');

                foreach ($payload['absensi'] as $mahasiswa_id => $s) {
                    // ✅ CEK APAKAH MAHASISWA INI BISA DIAKSES OLEH ASISTEN
                    if ($this->role === 'asisten_praktikum' && !in_array($mahasiswa_id, $accessible_ids)) {
                        continue; // Skip mahasiswa yang tidak ada di group asisten
                    }

                    $data = [
                        'praktikum_id' => $praktikum_id,
                        'mahasiswa_id' => $mahasiswa_id,
                        'jadwal_praktikum_id' => $jadwal_id,
                        'pertemuan' => $payload['pertemuan'] ?? null,
                        'tanggal' => $tanggal,
                        'status' => $s['status'] ?? 'alfa',
                        'keterangan' => $s['keterangan'] ?? ''
                    ];
                    $this->model->simpanAbsensi($data);
                }
                echo json_encode(['status'=>'success','message'=>'Disimpan']);
            } catch (Exception $e) {
                echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
            }
        }
    }

    public function lihatRekap() {
        $jadwal_id = $_GET['jadwal_id'] ?? '';
        $pertemuan = $_GET['pertemuan'] ?? '';
        
        if (!$jadwal_id || !$pertemuan) {
            $_SESSION['error'] = "Parameter jadwal_id dan pertemuan diperlukan.";
            header("Location: ?page=absensi");
            exit();
        }
        
        // Ambil data untuk rekap
        $data['detail_jadwal'] = $this->getDetailJadwal($jadwal_id);
        $data['mahasiswa'] = $this->getMahasiswaByJadwal($jadwal_id);
        $data['absensi'] = $this->getAbsensi($jadwal_id, $pertemuan);
        $data['selected_pertemuan'] = $pertemuan;
        
        // Extract data untuk view
        extract($data);
        
        // Render view rekap berdasarkan role
        if ($this->role === 'asisten_praktikum') {
            require __DIR__ . '/../views/asisten_praktikumMenu/rekapAbsensi.php';
        } else {
            require __DIR__ . '/../views/staff_lab/rekapAbsensi.php';
        }
    }

    public function index() {
        $data = [];
        
        // Step 1: Ambil daftar praktikum dari mahasiswa
        $data['praktikum_list'] = $this->getPraktikumFromMahasiswa();
        
        // Step 2: Jika praktikum sudah dipilih, ambil jadwalnya
        $selected_praktikum = $_POST['praktikum_id'] ?? $_GET['praktikum_id'] ?? '';
        if ($selected_praktikum) {
            $data['selected_praktikum'] = $selected_praktikum;
            $data['jadwal_praktikum'] = $this->getJadwalByPraktikum($selected_praktikum);
            
            // Jika form post untuk menampilkan daftar mahasiswa/absensi
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $jadwal_id = $_POST['jadwal_praktikum_id'] ?? null;
                $pertemuan = $_POST['pertemuan'] ?? null;
                $kode_random = $_POST['kode_random'] ?? null;

                // ✅ VALIDASI BERDASARKAN ROLE
                if (in_array($this->role, ['staff_lab', 'admin'])) {
                    // STAFF/ADMIN: Hanya butuh jadwal & pertemuan
                    if (!$jadwal_id || !$pertemuan) {
                        $data['error'] = "Harap pilih jadwal dan pertemuan.";
                    } else {
                        $data['selected_jadwal'] = $jadwal_id;
                        $data['selected_pertemuan'] = $pertemuan;
                        $data['mahasiswa'] = $this->getMahasiswaByJadwal($jadwal_id);
                        $data['absensi_existing'] = $this->getAbsensi($jadwal_id, $pertemuan);
                        $data['detail_jadwal'] = $this->getDetailJadwal($jadwal_id);
                        
                        $data['absensi_terisi'] = !empty($data['absensi_existing']);
                        $data['bisa_edit'] = true; // Staff/admin selalu bisa edit
                    }
                } else {
                    // ASISTEN PRAKTIKUM: Butuh kode random
                    if (!$jadwal_id || !$pertemuan || !$kode_random) {
                        $data['error'] = "Harap pilih jadwal, pertemuan, dan masukkan kode random.";
                    } 
                    // VALIDASI: Cek kode random
                    else if (!$this->validateKodeRandom($jadwal_id, $kode_random)) {
                        $data['error'] = "Kode random salah atau tidak valid.";
                    }
                    else {
                        $data['selected_jadwal'] = $jadwal_id;
                        $data['selected_pertemuan'] = $pertemuan;
                        $data['mahasiswa'] = $this->getMahasiswaByJadwal($jadwal_id);
                        $data['absensi_existing'] = $this->getAbsensi($jadwal_id, $pertemuan);
                        $data['detail_jadwal'] = $this->getDetailJadwal($jadwal_id);
                        
                        $data['absensi_terisi'] = !empty($data['absensi_existing']);
                        $data['bisa_edit'] = !$data['absensi_terisi']; // Asisten hanya bisa edit jika belum tersimpan
                        
                        // ✅ DEBUG INFO
                        error_log("Asisten accessing jadwal: $jadwal_id, pertemuan: $pertemuan");
                        error_log("Mahasiswa count: " . count($data['mahasiswa']));
                    }
                }
            }
        }

        // Extract data untuk view
        extract($data);

        // Render view berdasarkan role
        if ($this->role === 'asisten_praktikum') {
            require __DIR__ . '/../views/asisten_praktikumMenu/absensi.php';
        } else {
            require __DIR__ . '/../views/staff_lab/absensiStaffnAdmin.php';
        }
    }

    public function getMahasiswaByJadwalAndGroup($jadwal_id, $group_id) {
        try {
            $query = "SELECT m.* FROM mahasiswa m 
                      INNER JOIN praktikum_group_assignment pga ON m.id = pga.entity_id 
                      WHERE pga.jadwal_praktikum_id = ? 
                      AND pga.entity_type = 'mahasiswa' 
                      AND pga.group_number = ?
                      ORDER BY m.nim";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$jadwal_id, $group_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error in getMahasiswaByJadwalAndGroup: " . $e->getMessage());
            return [];
        }
    }
}