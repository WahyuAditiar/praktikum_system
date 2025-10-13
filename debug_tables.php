<?php
// debug_tables.php - Letakkan di ROOT folder praktikum_system
require_once __DIR__ . '/config/database.php'; // ✅ PATH YANG BENAR

$database = new Database();
$db = $database->getConnection();

echo "<h3>🔍 Debug Database Structure</h3>";

// Cek tabel yang ada
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "<h4>📋 Tables in database:</h4>";
echo "<ul>";
foreach ($tables as $table) {
    echo "<li>$table</li>";
}
echo "</ul>";

// Cek struktur tabel praktikum
if (in_array('praktikum', $tables)) {
    echo "<h4>📊 Structure of 'praktikum' table:</h4>";
    $stmt = $db->query("DESCRIBE praktikum");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
    }
    echo "</table>";

    // Cek data sample di praktikum
    echo "<h4>📝 Sample data from 'praktikum':</h4>";
    $stmt = $db->query("SELECT * FROM praktikum LIMIT 5");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($data, true) . "</pre>";
} else {
    echo "<p>❌ Table 'praktikum' tidak ada</p>";
}

// Cek struktur tabel jadwal_praktikum
if (in_array('jadwal_praktikum', $tables)) {
    echo "<h4>📊 Structure of 'jadwal_praktikum' table:</h4>";
    $stmt = $db->query("DESCRIBE jadwal_praktikum");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
    }
    echo "</table>";

    // Cek data sample di jadwal_praktikum
    echo "<h4>📝 Sample data from 'jadwal_praktikum':</h4>";
    $stmt = $db->query("SELECT * FROM jadwal_praktikum LIMIT 5");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($data, true) . "</pre>";
} else {
    echo "<p>❌ Table 'jadwal_praktikum' tidak ada</p>";
}

// Cek tahun ajaran yang ada
echo "<h4>📅 Available tahun_ajaran:</h4>";
$availableYears = [];

// Dari jadwal_praktikum
if (in_array('jadwal_praktikum', $tables)) {
    $stmt = $db->query("SELECT DISTINCT tahun_ajaran FROM jadwal_praktikum");
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $availableYears = array_merge($availableYears, $years);
}

// Dari praktikum
if (in_array('praktikum', $tables)) {
    $stmt = $db->query("SELECT DISTINCT tahun_ajaran FROM praktikum");
    $years = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $availableYears = array_merge($availableYears, $years);
}

$availableYears = array_unique($availableYears);
echo "<pre>" . print_r($availableYears, true) . "</pre>";

echo "<hr>";
echo "<h4>🔍 Test Query untuk tahun 2023/2024:</h4>";

// Test query JOIN
if (in_array('praktikum', $tables) && in_array('jadwal_praktikum', $tables)) {
    $sql = "SELECT 
                p.id,
                p.nama_praktikum,
                j.kelas,
                j.tahun_ajaran
            FROM praktikum p
            INNER JOIN jadwal_praktikum j ON p.id = j.praktikum_id
            WHERE j.tahun_ajaran = '2023/2024'
            LIMIT 5";
    
    echo "<p>Query: <code>$sql</code></p>";
    
    try {
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>" . print_r($result, true) . "</pre>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Tidak bisa test JOIN, tabel tidak lengkap</p>";
}
?>