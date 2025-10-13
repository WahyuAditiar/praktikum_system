<?php
// views/staff_lab/get_praktikum_by_tahun.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/PraktikumModel.php';

// CORS headers untuk development
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (isset($_GET['tahun_ajaran'])) {
    $tahunAjaran = $_GET['tahun_ajaran'];
    
    error_log("AJAX Request - Tahun Ajaran: " . $tahunAjaran);
    
    try {
        // ✅ GUNAKAN CONSTRUCTOR TANPA PARAMETER
        $praktikumModel = new PraktikumModel();
        $praktikumList = $praktikumModel->getByTahunAjaran($tahunAjaran);
        
        error_log("AJAX Response - Records found: " . count($praktikumList));
        
        echo json_encode($praktikumList);
        
    } catch (Exception $e) {
        error_log("AJAX Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Default empty response
echo json_encode([]);