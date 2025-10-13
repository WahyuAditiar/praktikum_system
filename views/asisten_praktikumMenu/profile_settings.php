<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

checkAuth();
checkRole(['asisten_praktikum', 'staff_lab']);

$page_title = "Profile Settings - Tanda Tangan";

// Inisialisasi koneksi database
$database = new Database();
$pdo = $database->getConnection();

// Session ID
$users_id = $_SESSION['users_id'] ?? $_SESSION['user_id'] ?? null;

if (!$users_id) {
    $_SESSION['error'] = "User ID tidak ditemukan. Silakan login kembali.";
    header("Location: login.php");
    exit;
}

// ✅ PERBAIKAN: Query yang lebih sederhana - ambil data dasar dari users saja
$query = "SELECT 
            id, 
            username, 
            nama, 
            nim, 
            role,
            signature_data,
            signature_updated_at
          FROM users 
          WHERE id = :users_id";
          
$stmt = $pdo->prepare($query);
$stmt->execute([':users_id' => $users_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ PERBAIKAN: Coba ambil data praktikum dari session atau query terpisah
$praktikum_data = [];
$kelas_data = '';

// Jika user adalah asisten praktikum, coba ambil data praktikum
if ($_SESSION['role'] == 'asisten_praktikum') {
    // Coba dari session dulu
    if (!empty($_SESSION['nama_praktikum'])) {
        $praktikum_data['nama_praktikum'] = $_SESSION['nama_praktikum'];
    }
    if (!empty($_SESSION['kelas'])) {
        $kelas_data = $_SESSION['kelas'];
    }
    
    // Jika tidak ada di session, coba query langsung ke asisten_praktikum
    if (empty($praktikum_data) && !empty($user['nim'])) {
        $query_praktikum = "SELECT ap.kelas, p.nama_praktikum 
                           FROM asisten_praktikum ap 
                           LEFT JOIN praktikum p ON ap.praktikum_id = p.id 
                           WHERE ap.nim = :nim";
        $stmt_praktikum = $pdo->prepare($query_praktikum);
        $stmt_praktikum->execute([':nim' => $user['nim']]);
        $praktikum_info = $stmt_praktikum->fetch(PDO::FETCH_ASSOC);
        
        if ($praktikum_info) {
            $praktikum_data = $praktikum_info;
            $kelas_data = $praktikum_info['kelas'] ?? '';
        }
    }
}

// PROSES SIMPAN TANDA TANGAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'save_signature') {
        $signature_data = $_POST['signature_data'] ?? '';
        
        // Validasi signature
        if (empty($signature_data) || !str_contains($signature_data, 'data:image/png;base64')) {
            $_SESSION['error'] = "Signature tidak valid!";
            header("Location: profile_settings.php");
            exit;
        }
        
        try {
            $update_query = "UPDATE users 
                            SET signature_data = :signature_data,
                                signature_updated_at = NOW()
                            WHERE id = :users_id";
            
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute([
                ':signature_data' => $signature_data,
                ':users_id' => $users_id
            ]);
            
            // ✅ PERBAIKAN: UPDATE SESSION DENGAN CARA YANG BENAR
            $_SESSION['user_signature_data'] = $signature_data;
            $_SESSION['has_signature'] = true;
            $_SESSION['signature_updated_at'] = date('Y-m-d H:i:s');
            
            $_SESSION['success'] = "Signature berhasil disimpan!";
            header("Location: profile_settings.php");
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error menyimpan signature: " . $e->getMessage();
            header("Location: profile_settings.php");
            exit;
        }
   

    }

    // PROSES HAPUS TANDA TANGAN
    if ($_POST['action'] == 'delete_signature') {
        try {
            $update_query = "UPDATE users 
                            SET signature_data = NULL,
                                signature_updated_at = NULL
                            WHERE id = :users_id";
            
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute([':users_id' => $users_id]);
            
            // ✅ PERBAIKAN: HAPUS DARI SESSION JUGA
            unset($_SESSION['user_signature_data']);
            unset($_SESSION['has_signature']);
            unset($_SESSION['signature_updated_at']);
            unset($_SESSION['signature_data']);
            
            $_SESSION['success'] = "Signature berhasil dihapus!";
            header("Location: profile_settings.php");
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error menghapus signature: " . $e->getMessage();
            header("Location: profile_settings.php");
            exit;
        }
    }

}

?>

<?php include __DIR__ . '/../templates/header.php'; ?>
<?php include __DIR__ . '/../templates/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profile Settings - Tanda Tangan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Notifikasi -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Tanda Tangan Digital</h3>
                        </div>
                        <form method="POST" id="signatureForm">
                            <input type="hidden" name="action" value="save_signature">
                            
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Buat Tanda Tangan Anda</label>
                                    <p class="text-muted small">
                                        Gambar tanda tangan Anda di area berikut. Tanda tangan ini akan digunakan untuk absensi otomatis.
                                    </p>
                                    
                                    <!-- Canvas untuk Signature -->
                                    <div class="signature-container mb-3">
                                        <canvas id="signature-pad" width="600" height="200" 
                                                style="border: 2px dashed #dee2e6; border-radius: 5px; cursor: crosshair;">
                                        </canvas>
                                    </div>
                                    
                                    <input type="hidden" name="signature_data" id="signatureData">
                                    
                                    <!-- Tombol Actions -->
                                    <div class="btn-group mb-3">
                                        <button type="button" id="clear-signature" class="btn btn-warning">
                                            <i class="fas fa-eraser"></i> Hapus
                                        </button>
                                        <button type="button" id="save-signature" class="btn btn-success">
                                            <i class="fas fa-save"></i> Simpan Signature
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submit-signature" disabled>
                                            <i class="fas fa-check"></i> Simpan ke Profile
                                        </button>
                                    </div>
                                    
                                    

                                    <!-- Preview Signature -->
                                    <div id="signature-preview" class="mt-3" style="display: none;">
                                        <h6>Preview Signature:</h6>
                                        <img id="preview-image" src="" alt="Signature Preview" 
                                             style="border: 1px solid #ddd; padding: 5px; max-width: 300px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Info Profile -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Profile</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-user-circle fa-4x text-info"></i>
                            </div>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Username</strong></td>
                                    <td><?= htmlspecialchars($user['username'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama</strong></td>
                                    <td><?= htmlspecialchars($user['nama'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>NIM</strong></td>
                                    <td>
                                        <?php if (!empty($user['nim'])): ?>
                                            <?= htmlspecialchars($user['nim']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak tersedia</span>
                                            <br><small class="text-warning">Hubungi admin untuk update NIM</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Role</strong></td>
                                    <td>
                                        <span class="badge badge-info"><?= htmlspecialchars($user['role'] ?? '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Praktikum</strong></td>
                                    <td>
                                        <?php if (!empty($praktikum_data['nama_praktikum'])): ?>
                                            <?= htmlspecialchars($praktikum_data['nama_praktikum']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Belum ditentukan</span>
                                            <br><small class="text-warning">Hubungi staff lab untuk penempatan</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Kelas</strong></td>
                                    <td>
                                        <?php if (!empty($kelas_data)): ?>
                                            <?= htmlspecialchars($kelas_data) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Signature</strong></td>
                                    <td>
                                        <?php if (!empty($user['signature_data'])): ?>
                                            <span class="badge badge-success">Tersedia</span>
                                            <small class="text-muted d-block">
                                                Update: <?= date('d/m/Y H:i', strtotime($user['signature_updated_at'] ?? 'now')) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Belum Ada</span>
                                            <br><small class="text-info">Buat tanda tangan di form sebelah</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            
                            <?php if (!empty($user['signature_data'])): ?>
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="action" value="delete_signature">
                                    <button type="submit" class="btn btn-danger btn-sm btn-block" 
                                            onclick="return confirm('Yakin hapus signature?')">
                                        <i class="fas fa-trash"></i> Hapus Signature
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <!-- Info tambahan -->
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Jika data NIM/Praktikum tidak sesuai, hubungi administrator.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk -->
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Petunjuk</h3>
                        </div>
                        <div class="card-body">
                            <ol class="pl-3 small">
                                <li>Gambar tanda tangan di area canvas</li>
                                <li>Klik "Simpan Signature" untuk preview</li>
                                <li>Klik "Simpan ke Profile" untuk menyimpan</li>
                                <li>Signature akan digunakan otomatis saat absen</li>
                                <li>Pastikan signature jelas dan konsisten</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Signature Pad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas);
    const clearButton = document.getElementById('clear-signature');
    const saveButton = document.getElementById('save-signature');
    const submitButton = document.getElementById('submit-signature');
    const signatureData = document.getElementById('signatureData');
    const previewDiv = document.getElementById('signature-preview');
    const previewImage = document.getElementById('preview-image');

    // Clear signature
    clearButton.addEventListener('click', function() {
        signaturePad.clear();
        signatureData.value = '';
        previewDiv.style.display = 'none';
        submitButton.disabled = true;
    });

    // Save signature preview
    saveButton.addEventListener('click', function() {
        if (signaturePad.isEmpty()) {
            alert('Silakan buat tanda tangan terlebih dahulu.');
            return;
        }

        const dataURL = signaturePad.toDataURL('image/png');
        signatureData.value = dataURL;
        previewImage.src = dataURL;
        previewDiv.style.display = 'block';
        submitButton.disabled = false;
        
        alert('Signature berhasil disimpan! Klik "Simpan ke Profile" untuk menyimpan permanen.');
    });

    // Adjust canvas coordinate space taking into account pixel ratio,
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
});
</script>

<style>
.signature-container {
    position: relative;
    max-width: 100%;
}

#signature-pad {
    width: 100%;
    height: 200px;
    background-color: #f8f9fa;
}

.btn-group .btn {
    margin-right: 5px;
}
</style>

<?php include __DIR__ . '/../templates/footer.php'; ?>