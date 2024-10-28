<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit();
}


$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM tables WHERE id = ?");
$stmt->execute([$id]);

header('Location: manage_tables.php');
exit();
?>