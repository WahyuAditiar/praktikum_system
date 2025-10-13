<?php
require_once '../../config/config.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM praktikum WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: praktikum.php?deleted=1");
exit;
?>
