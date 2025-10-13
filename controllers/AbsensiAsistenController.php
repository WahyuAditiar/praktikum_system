<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/AbsensiAsistenModel.php';

class AbsensiAsistenController {
    private $model;
    private $db;
    private $uploadDir;

    public function __construct($db) {
        $this->db = $db; // ✅ INI ADALAH KONEKSI DATABASE
        $this->model = new AbsensiAsistenModel($db);
        $this->uploadDir = dirname(__DIR__) . '/uploads/absen_asisten/';
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function getAll($onlyOwn = false, $username = null) {
        return $this->model->getAll($onlyOwn, $username);
    }

    public function getById($id) {
        return $this->model->getById($id);
    }

 // Di file controllers/AbsensiAsistenController.php - update method create
public function create($data, $files, $username) {
    try {
        // ✅ PERBAIKAN: Auto-fill NIM jika kosong
        if (empty($data['nim']) && !empty($_SESSION['nim'])) {
            $data['nim'] = $_SESSION['nim'];
        }
        
        // ✅ JIKA MASIH KOSONG, GUNAKAN USERNAME SEBAGAI FALLBACK
        if (empty($data['nim'])) {
            $data['nim'] = $username;
        }

        // ✅ JIKA NAMA KOSONG, GUNAKAN USERNAME
        if (empty($data['nama'])) {
            $data['nama'] = $username;
        }

        // Validasi required fields
        $required = ['nim', 'nama', 'praktikum_name', 'kelas', 'pertemuan', 'tanggal', 'jam_mulai', 'status_hadir'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Field $field harus diisi. Nilai: " . ($data[$field] ?? 'NULL')];
            }
        }

        // ✅ PERBAIKAN: AUTO-SIGNATURE - Convert base64 ke file jika perlu
        $signature_data = null;
        if ($data['status_hadir'] === 'hadir') {
            $signature_data = $this->getSignatureFromUserProfile($data['nim']);
            
            if ($signature_data) {
                // ✅ KONVERSI base64 ke file gambar
                $signature_data = $this->saveSignatureToFile($signature_data, $data['nim'], $data['pertemuan']);
            }
            
            if (!$signature_data) {
                return ['success' => false, 'message' => 'Signature tidak ditemukan di profile. Silakan atur signature terlebih dahulu.'];
            }
        }

        // Handle file uploads
        $foto_path = $this->handleFileUpload($files['foto'], 'foto', $data['nim'], $data['pertemuan']);
        if (!$foto_path) {
            return ['success' => false, 'message' => 'Foto bukti harus diupload'];
        }

        $laporan_path = '';
        if (!empty($files['laporan']['name'])) {
            $laporan_path = $this->handleFileUpload($files['laporan'], 'laporan', $data['nim'], $data['pertemuan']);
        }

        // ✅ UPDATE: Query INSERT dengan materi
        $columns = [
            'nim', 'nama', 'praktikum_name', 'kelas', 'pertemuan', 
            'tanggal', 'jam_mulai', 'materi', 'status_hadir', 'signature_data',
            'foto_path', 'laporan_path', 'gps_lat', 'gps_lng', 'created_by', 'created_at'
        ];
        
        $placeholders = array_fill(0, count($columns), '?');
        $values = [
            $data['nim'], 
            $data['nama'], 
            $data['praktikum_name'], 
            $data['kelas'],
            $data['pertemuan'], 
            $data['tanggal'], 
            $data['jam_mulai'],
            $data['materi'] ?? '', // ✅ TAMBAHKAN MATERI
            $data['status_hadir'], 
            $signature_data,
            $foto_path, 
            $laporan_path,
            $data['gps_lat'] ?? null, 
            $data['gps_lng'] ?? null, 
            $username,
            date('Y-m-d H:i:s')
        ];

        $sql = "INSERT INTO absen_asisten (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($values);
        
        if ($result) {
            return [
                'success' => true, 
                'message' => 'Absensi berhasil disimpan' . 
                    ($signature_data ? ' dengan signature otomatis' : '')
            ];
        } else {
            return ['success' => false, 'message' => 'Gagal menyimpan ke database'];
        }
                
    } catch (PDOException $e) {
        error_log("Database error in create absensi: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// ✅ METHOD BARU: Convert base64 signature ke file
private function saveSignatureToFile($signature_data, $nim, $pertemuan) {
    try {
        $upload_dir = $this->uploadDir;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $filename = 'signature_' . $nim . '_' . $pertemuan . '_' . time() . '.png';
        $filepath = $upload_dir . $filename;
        
        // Decode base64 signature
        if (preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $signature_data)) {
            $signature_data = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $signature_data);
        }
        
        $signature_data = str_replace(' ', '+', $signature_data);
        $signature_binary = base64_decode($signature_data);
        
        // Save file
        if (file_put_contents($filepath, $signature_binary)) {
            return 'absen_asisten/' . $filename;
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Error saving signature to file: " . $e->getMessage());
        return null;
    }
}

// ✅ METHOD BARU: Cari signature by username (fallback)
private function getSignatureByUsername($username) {
    try {
        $query = "SELECT signature_data FROM users WHERE username = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['signature_data'])) {
            return $result['signature_data'];
        }
        
        return null;
        
    } catch (PDOException $e) {
        error_log("Error getting signature by username: " . $e->getMessage());
        return null;
    }
}
// ✅ METHOD BARU: Ambil signature dari tabel users
private function getSignatureFromUserProfile($nim) {
    try {
        $query = "SELECT signature_data FROM users WHERE nim = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$nim]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['signature_data'])) {
            return $result['signature_data'];
        }
        
        return null;
        
    } catch (PDOException $e) {
        error_log("Error getting signature from profile: " . $e->getMessage());
        return null;
    }
}

    // ✅ METHOD BARU: Simpan signature dari profile
    private function saveSignatureFromProfile($signature_data, $nim, $pertemuan) {
        try {
            $upload_dir = $this->uploadDir;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = 'signature_' . $nim . '_' . $pertemuan . '_' . time() . '.png';
            $filepath = $upload_dir . $filename;
            
            // Decode base64 signature
            $signature_data = str_replace('data:image/png;base64,', '', $signature_data);
            $signature_data = str_replace(' ', '+', $signature_data);
            $signature_binary = base64_decode($signature_data);
            
            // Save file
            if (file_put_contents($filepath, $signature_binary)) {
                return 'absen_asisten/' . $filename;
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Error saving signature from profile: " . $e->getMessage());
            return null;
        }
    }

    // ✅ METHOD BARU: Handle file upload
    private function handleFileUpload($file, $type, $nim, $pertemuan) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowed_types = [
            'foto' => ['jpg', 'jpeg', 'png', 'gif'],
            'laporan' => ['pdf', 'doc', 'docx', 'jpg', 'png']
        ];

        $max_size = [
            'foto' => 5 * 1024 * 1024, // 5MB
            'laporan' => 10 * 1024 * 1024 // 10MB
        ];

        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_types[$type])) {
            return false;
        }

        if ($file['size'] > $max_size[$type]) {
            return false;
        }

        $filename = $type . '_' . $nim . '_' . $pertemuan . '_' . time() . '.' . $file_ext;
        $filepath = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'absen_asisten/' . $filename;
        }

        return false;
    }

    public function update($id, $post, $files, $currentUser) {
        // allow update if owner or staff_lab (check in view/controller)
        $existing = $this->model->getById($id);
        if (!$existing) return ['success'=>false,'errors'=>['Data tidak ditemukan']];

        // if signature posted overwrite, otherwise keep existing
        $signature_data = $existing['signature_data'];
        if (!empty($post['signature_data'])) {
            if (preg_match('/^data:image\/png;base64,/', $post['signature_data'])) {
                $data = substr($post['signature_data'], strpos($post['signature_data'], ',') + 1);
                $decoded = base64_decode($data);
                $fname = 'sig_' . uniqid() . '.png';
                $fp = $this->uploadDir . $fname;
                file_put_contents($fp, $decoded);
                $signature_data = 'absen_asisten/' . $fname;
            }
        }

        $foto_path = $existing['foto_path'];
        if (!empty($files['foto']) && $files['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($files['foto']['name'], PATHINFO_EXTENSION);
            $fname = 'foto_' . uniqid() . '.' . $ext;
            $dest = $this->uploadDir . $fname;
            move_uploaded_file($files['foto']['tmp_name'], $dest);
            $foto_path = 'absen_asisten/' . $fname;
        }

        $laporan_path = $existing['laporan_path'];
        if (!empty($files['laporan']) && $files['laporan']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($files['laporan']['name'], PATHINFO_EXTENSION);
            $fname = 'lap_' . uniqid() . '.' . $ext;
            $dest = $this->uploadDir . $fname;
            move_uploaded_file($files['laporan']['tmp_name'], $dest);
            $laporan_path = 'absen_asisten/' . $fname;
        }

        $data = [
            'nim' => $post['nim'],
            'nama' => $post['nama'],
            'praktikum_id' => $post['praktikum_id'] ?? null,
            'praktikum_name' => $post['praktikum_name'],
            'kelas' => $post['kelas'] ?? null,
            'pertemuan' => $post['pertemuan'],
            'tanggal' => $post['tanggal'],
            'jam_mulai' => $post['jam_mulai'] ?? null,
            'jam_akhir' => $post['jam_akhir'] ?? null,
            'materi' => $post['materi'] ?? null,
            'status_hadir' => $post['status_hadir'] ?? 'alpha',
            'signature_data' => $signature_data,
            'foto_path' => $foto_path,
            'laporan_path' => $laporan_path,
            'gps_lat' => $post['gps_lat'] ?? null,
            'gps_lng' => $post['gps_lng'] ?? null
        ];

        $ok = $this->model->updateRecord($id, $data);
        return ['success' => (bool)$ok, 'message' => $ok ? 'Absensi diperbarui' : 'Gagal memperbarui'];
    }

    public function delete($id) {
        return $this->model->deleteRecord($id);
    }

    // --- Tambahan untuk auto-fill asisten berdasarkan NIM ---
    public function getAsistenByNim($nim) {
        return $this->model->getAsistenByNim($nim);
    }

    public function getAsistenList() {
        if ($_SESSION['role'] === 'asisten_praktikum') {
            // ambil hanya data asisten yg login
            return $this->model->getAsistenByNim($_SESSION['nim']);
        } else {
            // staff bisa ambil semua
            return $this->model->getAllAsistenDropdown();
        }
    }

    public function getAbsensiList($nim = null) {
        return $this->model->getAbsensiList($nim);
    }

    public function validasiPulang($post) {
        if (empty($post['nim']) || empty($post['praktikum_name']) || empty($post['pertemuan'])) {
            return ['success' => false, 'message' => 'Data kurang (nim/praktikum/pertemuan harus diisi)'];
        }

        // Debug: log data yang dicari
        error_log("Mencari absensi dengan: " . print_r($post, true));

        $existing = $this->model->findOne([
            'nim' => $post['nim'],
            'praktikum_name' => $post['praktikum_name'],
            'kelas' => $post['kelas'] ?? '',
            'pertemuan' => $post['pertemuan'],
            'tanggal' => $post['tanggal']
        ]);

        if (!$existing) {
            return ['success' => false, 'message' => 'Data absen masuk tidak ditemukan untuk NIM: ' . $post['nim'] . ', Praktikum: ' . $post['praktikum_name'] . ', Pertemuan: ' . $post['pertemuan']];
        }

        $ok = $this->model->updateRecord($existing['id'], [
            'jam_akhir' => $post['jam_akhir']
        ]);

        return ['success' => (bool)$ok, 'message' => $ok ? 'Jam akhir berhasil disimpan' : 'Gagal update jam akhir'];
    }

    // Tambahkan method ini di class AbsensiAsistenController
    public function getRiwayatTahunAjaran($nim) {
        return $this->model->getRiwayatTahunAjaranByNim($nim);
    }
}