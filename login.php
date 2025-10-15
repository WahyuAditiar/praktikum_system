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
    :root {
      --primary-color: #2563eb;
      --primary-dark: #1d4ed8;
      --primary-light: #3b82f6;
      --text-dark: #1f2937;
      --text-light: #6b7280;
      --bg-white: rgba(255, 255, 255, 0.95);
      --border-light: #e5e7eb;
      --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: url('assets/images/bg-lab.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
      backdrop-filter: blur(2px);
    }

    .auth-container {
      max-width: 440px;
      width: 95%;
      background: var(--bg-white);
      border-radius: 20px;
      box-shadow: var(--shadow-medium);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      overflow: hidden;
      position: relative;
      z-index: 1;
      margin: 20px;
    }

    .auth-header {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: white;
      padding: 2.5rem 2rem;
      text-align: center;
      position: relative;
    }

    .auth-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .auth-header h1 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      position: relative;
    }

    .auth-header p {
      font-size: 0.95rem;
      opacity: 0.9;
      font-weight: 400;
    }

    .auth-body {
      padding: 2.5rem 2rem;
    }

    .nav-tabs {
      border: none;
      background: #f8fafc;
      border-radius: 12px;
      padding: 4px;
      margin-bottom: 2rem;
    }

    .nav-tabs .nav-item {
      flex: 1;
      text-align: center;
    }

    .nav-tabs .nav-link {
      border: none;
      border-radius: 10px;
      padding: 0.875rem 1rem;
      font-weight: 600;
      color: var(--text-light);
      transition: all 0.3s ease;
      background: transparent;
    }

    .nav-tabs .nav-link:hover {
      color: var(--primary-color);
      background: rgba(37, 99, 235, 0.05);
    }

    .nav-tabs .nav-link.active {
      background: var(--primary-color);
      color: white;
      box-shadow: var(--shadow-soft);
    }

    .form-label {
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .form-control, .form-select {
      border: 2px solid var(--border-light);
      border-radius: 12px;
      padding: 0.875rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: #fff;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      transform: translateY(-1px);
    }

    .input-group-text {
      background: #f8fafc;
      border: 2px solid var(--border-light);
      border-right: none;
      border-radius: 12px 0 0 12px;
      color: var(--text-light);
    }

    .form-control:focus + .input-group-text {
      border-color: var(--primary-color);
    }

    .btn {
      border-radius: 12px;
      padding: 0.875rem 1.5rem;
      font-weight: 600;
      font-size: 0.95rem;
      border: none;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .alert {
      border: none;
      border-radius: 12px;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      font-weight: 500;
    }

    .alert-danger {
      background: #fef2f2;
      color: #dc2626;
      border-left: 4px solid #dc2626;
    }

    .alert-success {
      background: #f0fdf4;
      color: #16a34a;
      border-left: 4px solid #16a34a;
    }

    .nim-field {
      display: none;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-text {
      font-size: 0.8rem;
      color: var(--text-light);
    }

    .tab-pane {
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from { 
        opacity: 0;
        transform: translateX(10px);
      }
      to { 
        opacity: 1;
        transform: translateX(0);
      }
    }

    /* Responsive Design */
    @media (max-width: 480px) {
      .auth-container {
        margin: 10px;
        border-radius: 16px;
      }
      
      .auth-header {
        padding: 2rem 1.5rem;
      }
      
      .auth-body {
        padding: 2rem 1.5rem;
      }
      
      .nav-tabs .nav-link {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
      }
    }

    /* Loading animation */
    .btn-loading {
      pointer-events: none;
      opacity: 0.8;
    }

    .btn-loading::after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      top: 50%;
      left: 50%;
      margin: -10px 0 0 -10px;
      border: 2px solid transparent;
      border-top: 2px solid #ffffff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
<div class="auth-container">
  <!-- Header -->
  <div class="auth-header">
    <h1><i class="fas fa-flask me-2"></i>SISTEM PRAKTIKUM</h1>
    <p>Laboratorium Teknik Informatika</p>
  </div>

  <!-- Body -->
  <div class="auth-body">
    <?php if ($error): ?>
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle me-2"></i><?= $error; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle me-2"></i><?= $success; ?>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="authTabs" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">
          <i class="fas fa-sign-in-alt me-2"></i>Login
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">
          <i class="fas fa-user-plus me-2"></i>Register
        </button>
      </li>
    </ul>

    <div class="tab-content mt-4">
      <!-- Login -->
      <div class="tab-pane fade show active" id="login" role="tabpanel">
        <form method="POST" id="loginForm">
          <input type="hidden" name="login" value="1">
          
          <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-user"></i></span>
              <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
            </div>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem
          </button>
        </form>
      </div>

      <!-- Register -->
      <div class="tab-pane fade" id="register" role="tabpanel">
        <form method="POST" id="registerForm">
          <input type="hidden" name="register" value="1">
          
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Buat username unik" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Buat password yang kuat" required>
          </div>
          
          <!-- Input NIM -->
          <div class="mb-3 nim-field" id="nimField">
            <label class="form-label">NIM <span class="text-danger">*</span></label>
            <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM">
            <div class="form-text">Wajib diisi untuk Asisten Praktikum</div>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" id="roleSelect" required>
              <option value="">-- Pilih Role --</option>
              <option value="admin">Admin</option>
              <option value="staff_lab">Staff Lab</option>
              <option value="staff_prodi">Staff Prodi</option>
              <option value="asisten_praktikum">Asisten Praktikum</option>
            </select>
          </div>
          
          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="fas fa-user-plus me-2"></i>Daftar Akun Baru
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    const nimField = document.getElementById('nimField');
    const nimInput = document.querySelector('input[name="nim"]');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Toggle NIM Field berdasarkan role
    function toggleNIMField() {
        if (roleSelect.value === 'asisten_praktikum') {
            nimField.style.display = 'block';
            nimInput.required = true;
        } else {
            nimField.style.display = 'none';
            nimInput.required = false;
            nimInput.value = '';
        }
    }

    // Loading state untuk form submission
    function setLoadingState(form, isLoading) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (isLoading) {
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        } else {
            submitBtn.classList.remove('btn-loading');
            submitBtn.disabled = false;
        }
    }

    // Event listeners
    roleSelect.addEventListener('change', toggleNIMField);
    
    loginForm.addEventListener('submit', function() {
        setLoadingState(this, true);
    });
    
    registerForm.addEventListener('submit', function() {
        setLoadingState(this, true);
    });

    // Auto focus pada input pertama saat tab berubah
    document.getElementById('authTabs').addEventListener('shown.bs.tab', function (e) {
        const activePane = document.querySelector(e.target.getAttribute('data-bs-target'));
        const firstInput = activePane.querySelector('input, select');
        if (firstInput) {
            firstInput.focus();
        }
    });

    // Jalankan sekali saat load
    toggleNIMField();
});
</script>
</body>
</html>