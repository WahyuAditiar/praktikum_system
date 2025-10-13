<?php
require_once __DIR__ . '/../assets/libs/phpqrcode/qrlib.php';

class QRCodeController {
    public function generate() {
        // URL tujuan QR (form mahasiswa)
        $url = "http://localhost/praktikum_system/index.php?controller=mahasiswa&action=create";

        // Lokasi file PNG
        $filePath = __DIR__ . '/../assets/qrcodes/form_mahasiswa.png';

        // Generate QR Code (level error L, ukuran 6)
        QRcode::png($url, $filePath, QR_ECLEVEL_L, 6);

        // Tampilkan QR Code ke browser
        header('Content-Type: image/png');
        readfile($filePath);
    }
}
