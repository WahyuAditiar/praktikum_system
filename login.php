<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/AuthController.php';

$database = new Database();
$db = $database->getConnection();
$authController = new AuthController($db);

$error = '';
$success = '';

// Handle Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($authController->login($username, $password)) {
        // TAMBAHKAN SESSION VARIABLE INI
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $_SESSION['role'] ?? 'asisten_praktikum'; // Default role
        
        // Store user data if available
        if (isset($_SESSION['user_data'])) {
            $_SESSION['user_data'] = $_SESSION['user_data'];
        } else {
            $_SESSION['user_data'] = [
                'username' => $username,
                'role' => $_SESSION['role']
            ];
        }

        // Redirect based on role dengan redirect URL handling
        $redirect_url = $_SESSION['redirect_url'] ?? ''; // Check if there's intended URL
        
        switch ($_SESSION['role']) {
            case 'admin':
                $target = $redirect_url ?: "views/admin/dashboard.php";
                break;
            case 'staff_lab':
                $target = $redirect_url ?: "views/staff_lab/dashboard.php";
                break;
            case 'staff_prodi':
                $target = $redirect_url ?: "views/staff_prodi/dashboard.php";
                break;
            case 'asisten_praktikum':
                $target = $redirect_url ?: "views/asisten_praktikumMenu/dashboard.php";
                break;
            default:
                $target = "login.php";
        }
        
        // Clear redirect URL
        if (isset($_SESSION['redirect_url'])) {
            unset($_SESSION['redirect_url']);
        }
        
        header("Location: " . $target);
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

// Handle Register
if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $nim      = $_POST['nim'] ?? ''; // ✅ TAMBAHAN: Ambil NIM dari form

    // ✅ TAMBAHAN: Validasi NIM untuk asisten praktikum
    if ($role === 'asisten_praktikum' && empty($nim)) {
        $error = "NIM wajib diisi untuk role Asisten Praktikum!";
    } else {
        $validPass = $authController->validatePasswordStrength($password);
        if ($validPass !== true) {
            $error = $validPass;
        } else {
            // ✅ TAMBAHAN: Kirim NIM ke method register
            $result = $authController->register($nama, $username, $password, $role, $nim);

            if ($result === true) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = $result;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Praktikum</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: url('assets/images/bg-lab.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .auth-container {
      max-width: 500px;
      width: 100%;
      padding: 25px;
      background: rgba(255,255,255,0.9);
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }
    .nav-tabs .nav-link.active {
      background-color: #2575fc;
      color: #fff !important;
    }
    .btn-primary {
      background: #2575fc;
      border: none;
    }
    .btn-primary:hover {
      background: #6a11cb;
    }
    .nim-field {
      display: none;
    }
  </style>
</head>
<body>
<div class="auth-container">
  <h3 class="text-center mb-3">SISTEM PRAKTIKUM</h3>

  <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error; ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
      <div class="alert alert-success"><?= $success; ?></div>
  <?php endif; ?>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">Login</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">Register</button>
    </li>
  </ul>

  <div class="tab-content">
    <!-- Login -->
    <div class="tab-pane fade show active" id="login" role="tabpanel">
      <form method="POST">
        <input type="hidden" name="login" value="1">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" name="username" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" name="password" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt me-2"></i>Login</button>
      </form>
    </div>

    <!-- Register -->
    <div class="tab-pane fade" id="register" role="tabpanel">
      <form method="POST">
        <input type="hidden" name="register" value="1">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        
        <!-- ✅ TAMBAHAN: Input NIM (muncul hanya untuk asisten praktikum) -->
        <div class="mb-3 nim-field" id="nimField">
          <label class="form-label">NIM <span class="text-danger">*</span></label>
          <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM">
          <small class="text-muted">Wajib diisi untuk Asisten Praktikum</small>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" id="roleSelect" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="staff_lab">Staff Lab</option>
            <option value="staff_prodi">Staff Prodi</option>
            <option value="asisten_praktikum">Asisten Praktikum</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-user-plus me-2"></i>Register</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ✅ TAMBAHAN: Script untuk menampilkan/menyembunyikan field NIM
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    const nimField = document.getElementById('nimField');
    const nimInput = document.querySelector('input[name="nim"]');

    function toggleNIMField() {
        if (roleSelect.value === 'asisten_praktikum') {
            nimField.style.display = 'block';
            nimInput.required = true;
        } else {
            nimField.style.display = 'none';
            nimInput.required = false;
            nimInput.value = ''; // Clear value jika bukan asisten
        }
    }

    // Event listener untuk perubahan role
    roleSelect.addEventListener('change', toggleNIMField);
    
    // Jalankan sekali saat load untuk set initial state
    toggleNIMField();
});
</script>
</body>
</html>