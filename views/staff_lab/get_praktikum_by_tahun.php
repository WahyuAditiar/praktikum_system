<?php
// views/staff_lab/get_praktikum_by_tahun.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';

// CORS headers untuk development
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (isset($_GET['tahun_ajaran'])) {
    $tahunAjaran = $_GET['tahun_ajaran'];
    
    error_log("AJAX Request - Tahun Ajaran: " . $tahunAjaran);
    
    try {
        $database = new Database();
        $pdo = $database->getConnection();
        
        // Coba get data dari database
        require_once __DIR__ . '/../../models/PraktikumModel.php';
        $praktikumModel = new PraktikumModel($pdo);
        $praktikumList = $praktikumModel->getByTahunAjaran($tahunAjaran);
        
        // Jika tidak ada data, beri sample data untuk testing
        if (empty($praktikumList)) {
            error_log("No data found, returning sample data");
            $praktikumList = [
                [
                    'id' => 1,
                    'nama_praktikum' => 'Prak. Algoritma & Struktur Data',
                    'kelas' => 'A',
                    'tahun_ajaran' => $tahunAjaran,
                    'status' => 'active'
                ],
                [
                    'id' => 2,
                    'nama_praktikum' => 'Prak. Basis Data',
                    'kelas' => 'B',
                    'tahun_ajaran' => $tahunAjaran,
                    'status' => 'active'
                ],
                [
                    'id' => 3,
                    'nama_praktikum' => 'Prak. Pemrograman Web',
                    'kelas' => 'C',
                    'tahun_ajaran' => $tahunAjaran,
                    'status' => 'active'
                ]
            ];
        }
        
        error_log("Returning: " . count($praktikumList) . " records");
        echo json_encode($praktikumList);
        
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        
        // Fallback ke sample data jika database error
        $fallbackData = [
            [
                'id' => 1,
                'nama_praktikum' => 'Prak. Algoritma (Fallback)',
                'kelas' => 'A',
                'tahun_ajaran' => $tahunAjaran,
                'status' => 'active'
            ],
            [
                'id' => 2, 
                'nama_praktikum' => 'Prak. Basis Data (Fallback)',
                'kelas' => 'B',
                'tahun_ajaran' => $tahunAjaran,
                'status' => 'active'
            ]
        ];
        
        echo json_encode($fallbackData);
    }
    exit;
}

// Default empty response
echo json_encode([]);