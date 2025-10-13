<?php
// test_dosen.php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Test query langsung
$query = "SELECT * FROM dosen";
$stmt = $db->prepare($query);
$stmt->execute();
$dosen = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Test Data Dosen</h1>";
echo "<p>Jumlah data: " . count($dosen) . "</p>";
echo "<pre>";
print_r($dosen);
echo "</pre>";
?>