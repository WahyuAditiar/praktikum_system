<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT id, username, password, role, nama, nim, status FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Cek status user
            if ($user['status'] !== 'active') {
                return false;
            }
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    // ✅ TAMBAHAN: Method createUser dengan parameter NIM
    public function createUser($nama, $username, $password, $role, $nim = null) {
        // Validasi input
        if (empty($nama) || empty($username) || empty($password) || empty($role)) {
            return false;
        }

        // ✅ TAMBAHAN: Validasi NIM untuk asisten praktikum
        if ($role === 'asisten_praktikum' && empty($nim)) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // ✅ TAMBAHAN: Query INSERT dengan kolom nim
        $query = "INSERT INTO " . $this->table_name . " 
                  (nama, username, password, role, nim, status) 
                  VALUES (:nama, :username, :password, :role, :nim, 'active')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':nim', $nim);

        return $stmt->execute();
    }

    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // ✅ TAMBAHAN: Method untuk cek NIM sudah ada
    public function nimExists($nim) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE nim = :nim";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAllUsers() {
        // ✅ TAMBAHAN: Include kolom nim dalam SELECT
        $query = "SELECT id, username, role, nama, nim, status, created_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersByRole($role) {
        // ✅ TAMBAHAN: Include kolom nim dalam SELECT
        $query = "SELECT id, username, nama, nim, created_at 
                  FROM " . $this->table_name . " 
                  WHERE role = :role AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ TAMBAHAN: Method baru untuk mendapatkan user by ID
    public function getUserById($id) {
        $query = "SELECT id, username, role, nama, nim, status, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ TAMBAHAN: Method baru untuk update user
    public function updateUser($id, $nama, $username, $role, $nim = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nama = :nama, username = :username, role = :role, nim = :nim 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':nim', $nim);

        return $stmt->execute();
    }

    // ✅ TAMBAHAN: Method baru untuk update NIM saja
    public function updateNIM($id, $nim) {
        $query = "UPDATE " . $this->table_name . " SET nim = :nim WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nim', $nim);
        return $stmt->execute();
    }

    // ✅ TAMBAHAN: Method baru untuk mendapatkan asisten praktikum dengan data lengkap
    public function getAsistenPraktikum() {
        $query = "SELECT u.id, u.nama, u.username, u.nim, u.created_at, 
                         ap.kelas, p.nama_praktikum 
                  FROM " . $this->table_name . " u
                  LEFT JOIN asisten_praktikum ap ON u.nim = ap.nim 
                  LEFT JOIN praktikum p ON ap.praktikum_id = p.id 
                  WHERE u.role = 'asisten_praktikum' AND u.status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ TAMBAHAN: Method untuk update status user
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    // ✅ TAMBAHAN: Method untuk delete user
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>