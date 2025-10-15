<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/absensiController.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Untuk library PDF seperti TCPDF

use TCPDF as TCPDF;

if (!in_array($_SESSION['role'] ?? '', ['staff_lab', 'admin'])) {
    http_response_code(403);
    exit('Access denied');
}

// Ambil data dari POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $jadwal_id = $input['jadwal_id'] ?? $_POST['jadwal_id'] ?? '';
    $pertemuan = $input['pertemuan'] ?? $_POST['pertemuan'] ?? '';
    $praktikum = $input['praktikum'] ?? $_POST['praktikum'] ?? '';
    $kelas = $input['kelas'] ?? $_POST['kelas'] ?? '';
    $tanggal = $input['tanggal'] ?? $_POST['tanggal'] ?? date('d/m/Y');
    
    if (!$jadwal_id || !$pertemuan) {
        http_response_code(400);
        exit('Missing required parameters');
    }
    
    // Buat PDF
    generatePDF($jadwal_id, $pertemuan, $praktikum, $kelas, $tanggal, $input['mahasiswa'] ?? [], $input['absensi'] ?? []);
}

function generatePDF($jadwal_id, $pertemuan, $praktikum, $kelas, $tanggal, $mahasiswa, $absensi) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Sistem Absensi Praktikum');
    $pdf->SetAuthor('Staff Lab');
    $pdf->SetTitle('Rekap Absensi Praktikum');
    $pdf->SetSubject('Rekap Absensi');
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Title
    $pdf->Cell(0, 10, 'REKAP ABSENSI PRAKTIKUM', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, $praktikum . ' - Kelas ' . $kelas, 0, 1, 'C');
    $pdf->Cell(0, 10, 'Pertemuan ' . $pertemuan . ' - ' . $tanggal, 0, 1, 'C');
    
    // Add some space
    $pdf->Ln(10);
    
    // Create table header
    $pdf->SetFont('helvetica', 'B', 10);
    $header = array('No', 'NIM', 'Nama Mahasiswa', 'Status', 'Keterangan');
    
    // Column widths
    $w = array(10, 25, 70, 25, 60);
    
    // Header
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
    }
    $pdf->Ln();
    
    // Data
    $pdf->SetFont('helvetica', '', 9);
    $no = 1;
    
    foreach ($mahasiswa as $mhs) {
        $absen = array_filter($absensi, fn($a) => $a['mahasiswa_id'] == $mhs['id']);
        $data = $absen ? reset($absen) : null;
        $status = $data['status'] ?? 'Belum Absen';
        $keterangan = $data['keterangan'] ?? '-';
        
        $pdf->Cell($w[0], 6, $no, 'LR', 0, 'C');
        $pdf->Cell($w[1], 6, $mhs['nim'], 'LR', 0, 'L');
        $pdf->Cell($w[2], 6, substr($mhs['nama'], 0, 40), 'LR', 0, 'L');
        $pdf->Cell($w[3], 6, ucfirst($status), 'LR', 0, 'C');
        $pdf->Cell($w[4], 6, substr($keterangan, 0, 30), 'LR', 0, 'L');
        $pdf->Ln();
        
        $no++;
    }
    
    // Closing line
    $pdf->Cell(array_sum($w), 0, '', 'T');
    
    // Statistics
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 10, 'STATISTIK KEHADIRAN', 0, 1, 'L');
    
    $total_mahasiswa = count($mahasiswa);
    $hadir = count(array_filter($absensi, fn($a) => $a['status'] == 'hadir'));
    $sakit = count(array_filter($absensi, fn($a) => $a['status'] == 'sakit'));
    $izin = count(array_filter($absensi, fn($a) => $a['status'] == 'izin'));
    $alfa = count(array_filter($absensi, fn($a) => $a['status'] == 'alfa'));
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Total Mahasiswa: ' . $total_mahasiswa . ' orang', 0, 1);
    $pdf->Cell(0, 6, 'Hadir: ' . $hadir . ' orang (' . ($total_mahasiswa > 0 ? round(($hadir / $total_mahasiswa) * 100, 1) : 0) . '%)', 0, 1);
    $pdf->Cell(0, 6, 'Sakit: ' . $sakit . ' orang (' . ($total_mahasiswa > 0 ? round(($sakit / $total_mahasiswa) * 100, 1) : 0) . '%)', 0, 1);
    $pdf->Cell(0, 6, 'Izin: ' . $izin . ' orang (' . ($total_mahasiswa > 0 ? round(($izin / $total_mahasiswa) * 100, 1) : 0) . '%)', 0, 1);
    $pdf->Cell(0, 6, 'Alfa: ' . $alfa . ' orang (' . ($total_mahasiswa > 0 ? round(($alfa / $total_mahasiswa) * 100, 1) : 0) . '%)', 0, 1);
    
    // Output PDF
    $pdf->Output('rekap_absensi.pdf', 'D');
}
?>