<?php
// views/mahasiswa/form_mahasiswa.php
// Halaman form untuk mahasiswa (akses publik, tanpa login)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/MahasiswaController.php';

$database = new Database();
$db = $database->getConnection();
$mahasiswaController = new MahasiswaController($db);

$praktikumList = $mahasiswaController->getAllPraktikum();

$errors = [];
$success = '';
$old = [
    'nim' => '',
    'nama' => '',
    'kelas' => '',
    'email' => '',
    'praktikum_id' => '',
    'semester' => '',
    'tahun_akademik' => '',
    'prodi' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nim' => $_POST['nim'] ?? '',
        'nama' => $_POST['nama'] ?? '',
        'kelas' => $_POST['kelas'] ?? '',
        'email' => $_POST['email'] ?? '',
        'praktikum_id' => $_POST['praktikum_id'] ?? '',
        'semester' => $_POST['semester'] ?? '',
        'tahun_akademik' => $_POST['tahun_akademik'] ?? '',
        'prodi' => $_POST['prodi'] ?? '',
        'source' => 'mahasiswa'
    ];

    $old = $data;

    $_SESSION['username'] = 'mahasiswa';
    $_SESSION['role']     = 'mahasiswa';

    $result = $mahasiswaController->createMahasiswa($data);

    if ($result['success']) {
        $_SESSION['flash_success'] = $result['message'] ?? 'Data berhasil dikirim.';
        header("Location: form_mahasiswa.php");
        exit;
    } else {
        $errors = $result['errors'] ?? ['Gagal menyimpan data.'];
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Form Pendaftaran Praktikum - Universitas Pancasila</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .form-header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .form-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .form-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        
        .form-body {
            padding: 2.5rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .required::after {
            content: " *";
            color: var(--accent-color);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            border: none;
            border-radius: 8px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid var(--secondary-color);
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
        }
        
        .back-link {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #f8f9fa;
        }
        
        .input-group-icon {
            position: relative;
        }
        
        .input-group-icon .form-control {
            padding-left: 2.5rem;
        }
        
        .input-group-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }
        
        @media (max-width: 768px) {
            .form-body {
                padding: 1.5rem;
            }
            
            .form-header {
                padding: 1.5rem;
            }
            
            .form-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="form-container">
                    <div class="form-header">
                        <h1><i class="fas fa-clipboard-list me-2"></i>Form Pendaftaran Praktikum</h1>
                        <p>Fakultas Teknik - Universitas Pancasila</p>
                    </div>
                    
                    <div class="form-body">
                        <?php if (isset($_SESSION['flash_success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($_SESSION['flash_success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['flash_success']); ?>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post" novalidate class="needs-validation">
                            <!-- Section Data Pribadi -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user-graduate me-2"></i>Data Pribadi
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">NIM</label>
                                        <div class="input-group-icon">
                                            <i class="fas fa-id-card"></i>
                                            <input type="text" name="nim" class="form-control" required 
                                                   maxlength="20" value="<?= htmlspecialchars($old['nim']); ?>"
                                                   placeholder="Masukkan NIM">
                                        </div>
                                        <div class="form-text">Contoh: 202351001</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Nama Lengkap</label>
                                        <div class="input-group-icon">
                                            <i class="fas fa-user"></i>
                                            <input type="text" name="nama" class="form-control" required
                                                   value="<?= htmlspecialchars($old['nama']); ?>"
                                                   placeholder="Masukkan nama lengkap">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Program Studi</label>
                                        <select class="form-select" name="prodi" required>
                                            <option value="">Pilih Prodi</option>
                                            <option value="Teknik Informatika" <?= ($old['prodi'] == "Teknik Informatika") ? 'selected' : ''; ?>>Teknik Informatika</option>
                                            <option value="Sistem Informasi" <?= ($old['prodi'] == "Sistem Informasi") ? 'selected' : ''; ?>>Sistem Informasi</option>
                                            <option value="Teknik Komputer" <?= ($old['prodi'] == "Teknik Komputer") ? 'selected' : ''; ?>>Teknik Komputer</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Email</label>
                                        <div class="input-group-icon">
                                            <i class="fas fa-envelope"></i>
                                            <input type="email" name="email" class="form-control" required
                                                   value="<?= htmlspecialchars($old['email']); ?>"
                                                   placeholder="email@example.com">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Akademik -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-book me-2"></i>Data Akademik
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Semester</label>
                                        <select class="form-select" name="semester" required>
                                            <option value="">Pilih Semester</option>
                                            <option value="Gasal" <?= ($old['semester'] == "Gasal") ? 'selected' : ''; ?>>Gasal</option>
                                            <option value="Genap" <?= ($old['semester'] == "Genap") ? 'selected' : ''; ?>>Genap</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Tahun Akademik</label>
                                        <select class="form-select" name="tahun_akademik" required>
                                            <option value="">Pilih Tahun Akademik</option>
                                            <?php
                                            $currentYear = date("Y");
                                            for ($i = -1; $i <= 4; $i++) {
                                                $start = $currentYear + $i;
                                                $end = $start + 1;
                                                $thn = $start . "/" . $end;
                                                $selected = ($old['tahun_akademik'] == $thn) ? 'selected' : '';
                                                echo "<option value='$thn' $selected>$thn</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label required">Praktikum</label>
                                        <select name="praktikum_id" class="form-select" required>
                                            <option value="">-- Pilih Praktikum --</option>
                                            <?php foreach ($praktikumList as $p): ?>
                                                <option value="<?= htmlspecialchars($p['id']); ?>"
                                                    <?= ($old['praktikum_id'] == $p['id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars(($p['kode_mk'] ?? '') . ' - ' . ($p['nama_mk'] ?? '') . ' (' . ($p['nama_praktikum'] ?? '') . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Pilih mata kuliah praktikum yang ingin diambil</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Kelas</label>
                                        <select name="kelas" class="form-select" required>
                                            <option value="">Pilih Kelas</option>
                                            <?php
                                            $kelasList = range('A', 'H');
                                            foreach ($kelasList as $kelas):
                                            ?>
                                                <option value="<?= $kelas; ?>"
                                                    <?= (!empty($old['kelas']) && $old['kelas'] === $kelas) ? 'selected' : ''; ?>>
                                                    Kelas <?= $kelas; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="info-box">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Informasi Penting:</strong> Data dari form ini akan otomatis disimpan dengan 
                                <strong>created_by = mahasiswa</strong>. Penginputan hanya bisa dilakukan sekali, 
                                apabila ada kesalahan, harap konfirmasi ke staf lab.
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="https://teknik.univpancasila.ac.id/INFORMATIKA/" class="back-link">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Website Fakultas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        
        // Auto-select current academic year
        document.addEventListener('DOMContentLoaded', function() {
            const tahunSelect = document.querySelector('select[name="tahun_akademik"]');
            const currentYear = new Date().getFullYear();
            const currentSemester = new Date().getMonth() >= 6 ? 'Gasal' : 'Genap';
            const defaultTahun = currentYear + '/' + (currentYear + 1);
            
            // Set default tahun akademik if not already set
            if (!tahunSelect.value) {
                for (let option of tahunSelect.options) {
                    if (option.value === defaultTahun) {
                        option.selected = true;
                        break;
                    }
                }
            }
            
            // Set default semester if not already set
            const semesterSelect = document.querySelector('select[name="semester"]');
            if (!semesterSelect.value) {
                for (let option of semesterSelect.options) {
                    if (option.value === currentSemester) {
                        option.selected = true;
                        break;
                    }
                }
            }
        });
    </script>
</body>
</html>