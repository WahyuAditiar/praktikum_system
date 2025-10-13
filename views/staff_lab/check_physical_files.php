<?php
// check_physical_files.php
session_start();

echo "<h2>CEK FILE FISIK DI SERVER</h2>";

$base_path = 'C:/xampp/htdocs/praktikum_system/';
$upload_path = $base_path . 'uploads/absen_asisten/';

echo "Base path: {$base_path}<br>";
echo "Upload path: {$upload_path}<br>";

// Cek apakah folder exists
if (!is_dir($upload_path)) {
    echo "❌ Folder uploads tidak ditemukan!<br>";
    // Coba buat folder
    if (mkdir($upload_path, 0777, true)) {
        echo "✅ Berhasil membuat folder uploads<br>";
    } else {
        echo "❌ Gagal membuat folder uploads<br>";
    }
} else {
    echo "✅ Folder uploads ditemukan<br>";
    
    // List files dalam folder
    $files = scandir($upload_path);
    $image_files = array_filter($files, function($file) {
        return $file != '.' && $file != '..' && preg_match('/\.(png|jpg|jpeg)$/i', $file);
    });
    
    if (empty($image_files)) {
        echo "❌ Tidak ada file gambar di folder uploads<br>";
    } else {
        echo "✅ File gambar di folder uploads: " . implode(', ', $image_files) . "<br>";
        
        // Tampilkan preview
        foreach ($image_files as $file) {
            $file_path = $upload_path . $file;
            $web_path = 'uploads/absen_asisten/' . $file;
            echo "<div style='margin: 10px;'>";
            echo "File: {$file}<br>";
            echo "Size: " . filesize($file_path) . " bytes<br>";
            echo "Preview: <img src='{$web_path}' style='max-width: 100px; border: 1px solid blue;'><br>";
            echo "</div>";
        }
    }
}
?>