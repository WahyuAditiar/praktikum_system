<?php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private $userModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->userModel = new UserModel($db);
    }

    // Login user
public function login($username, $password)
{
    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // simpan semua data penting ke session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];  // dipakai di created_by
        $_SESSION['nama']     = $user['nama'];      // simpan nama lengkap juga
        $_SESSION['role']     = $user['role'];
        $_SESSION['nim']      = $user['nim'];       // ✅ TAMBAHAN: Simpan NIM dari users table
        
        // ✅ PERBAIKAN PENTING: SIMPAN SIGNATURE KE SESSION
        if (!empty($user['signature_data'])) {
            $_SESSION['user_signature_data'] = $user['signature_data'];
            $_SESSION['has_signature'] = true;
            $_SESSION['signature_updated_at'] = $user['signature_updated_at'] ?? null;
        } else {
            // Pastikan session signature kosong jika tidak ada
            unset($_SESSION['user_signature_data']);
            unset($_SESSION['has_signature']);
        }
        
        // -----------------------------
        // tambahan untuk asisten_praktikum
        // -----------------------------
        if ($user['role'] === 'asisten_praktikum') {
            // ✅ PERBAIKAN: Query yang lebih baik untuk ambil data asisten
            $stmt2 = $this->db->prepare("
                SELECT a.nim, a.nama, a.kelas, p.nama_praktikum 
                FROM asisten_praktikum a 
                LEFT JOIN praktikum p ON a.praktikum_id = p.id 
                WHERE a.nim = :nim
                LIMIT 1
            ");
            $stmt2->bindParam(':nim', $user['nim']);
            $stmt2->execute();
            $asisten = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($asisten) {
                $_SESSION['nim']            = $asisten['nim'];
                $_SESSION['nama']           = $asisten['nama']; // override dengan nama dari tabel asisten
                $_SESSION['nama_praktikum'] = $asisten['nama_praktikum'];
                $_SESSION['kelas']          = $asisten['kelas'];
            } else {
                // Jika tidak ada data di asisten_praktikum, gunakan data dari users
                $_SESSION['nama_praktikum'] = 'Belum ditentukan';
                $_SESSION['kelas'] = '-';
            }
        }
        // -----------------------------

        return true;
    }

    return false;
}
    // ✅ PERBAIKAN: Registrasi user baru dengan parameter NIM
    public function register($nama, $username, $password, $role, $nim = null)
    {
        $allowed_roles = ['admin', 'staff_lab', 'staff_prodi', 'asisten_praktikum'];
        if (!in_array($role, $allowed_roles)) {
            return "Role tidak valid!";
        }

        // ✅ TAMBAHAN: Validasi NIM untuk asisten praktikum
        if ($role === 'asisten_praktikum') {
            if (empty($nim)) {
                return "NIM wajib diisi untuk Asisten Praktikum!";
            }
            
            // Validasi format NIM (opsional)
            if (!$this->validateNIM($nim)) {
                return "Format NIM tidak valid! NIM harus berupa angka 8-15 digit.";
            }
            
            // Cek NIM sudah terdaftar
            if ($this->userModel->nimExists($nim)) {
                return "NIM sudah terdaftar!";
            }
        }

        // cek username sudah ada
        if ($this->userModel->usernameExists($username)) {
            return "Username sudah digunakan!";
        }

        // ✅ PERBAIKAN: Buat user baru dengan NIM
        if ($this->userModel->createUser($nama, $username, $password, $role, $nim)) {
            return true;
        } else {
            return "Registrasi gagal! Silakan coba lagi.";
        }
    }

    // ✅ TAMBAHAN: Method validasi NIM
    private function validateNIM($nim)
    {
        // Validasi: NIM harus angka 8-15 digit
        return preg_match('/^\d{8,15}$/', $nim);
    }

    // Logout
   // Logout
public function logout()
{
    // Hapus semua data session
    $_SESSION = array();

    // Hapus session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Hancurkan session
    session_destroy();

    // ✅ PERBAIKAN: Redirect ke halaman login yang benar
    header("Location: /praktikum_system/login.php");
    exit();
}

    // Validasi password strength
    public function validatePasswordStrength($password)
    {
        if (strlen($password) < 6) {
            return "Password minimal 6 karakter";
        }
        return true;
    }

    // ✅ TAMBAHAN: Method untuk update profile user
    public function updateProfile($user_id, $nama, $username, $nim = null)
    {
        // Cek jika username sudah digunakan oleh user lain
        $checkQuery = "SELECT id FROM users WHERE username = :username AND id != :user_id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->execute([':username' => $username, ':user_id' => $user_id]);
        
        if ($checkStmt->rowCount() > 0) {
            return "Username sudah digunakan!";
        }

        // Update data user
        $updateQuery = "UPDATE users SET nama = :nama, username = :username, nim = :nim WHERE id = :user_id";
        $updateStmt = $this->db->prepare($updateQuery);
        
        return $updateStmt->execute([
            ':nama' => $nama,
            ':username' => $username,
            ':nim' => $nim,
            ':user_id' => $user_id
        ]);
    }

    // ✅ TAMBAHAN: Method untuk mendapatkan data user by ID
    public function getUserById($user_id)
    {
        $query = "SELECT id, username, nama, nim, role, created_at FROM users WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ TAMBAHAN: Method untuk cek apakah user sudah login
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // ✅ TAMBAHAN: Method untuk redirect berdasarkan role
    public static function redirectBasedOnRole()
    {
        if (!self::isLoggedIn()) {
            header("Location: ../login.php");
            exit();
        }

        $role = $_SESSION['role'] ?? '';
        switch ($role) {
            case 'admin':
                header("Location: ../views/admin/dashboard.php");
                break;
            case 'staff_lab':
                header("Location: ../views/staff_lab/dashboard.php");
                break;
            case 'staff_prodi':
                header("Location: ../views/staff_prodi/dashboard.php");
                break;
            case 'asisten_praktikum':
                header("Location: ../views/asisten_praktikumMenu/dashboard.php");
                break;
            default:
                header("Location: ../login.php");
        }
        exit();
    }
}