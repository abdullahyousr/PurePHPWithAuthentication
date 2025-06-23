<?php
    require_once 'Configuration/session.php';
    require_once 'Configuration/database.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

// Only allow the owner to delete their post
$sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);

header('Location: dashboard.php');
exit;
?>