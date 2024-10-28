<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt->execute([$id]);
    $menu = $stmt->fetch();

    if ($menu) {
        
        $pdo->beginTransaction();
        try {
            
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE menu_id = ?");
            $stmt->execute([$id]);

            
            $stmt = $pdo->prepare("DELETE FROM menus WHERE id = ?");
            $stmt->execute([$id]);

            
            $pdo->commit();

            $_SESSION['success_message'] = "The menu has been successfully deleted";
        } catch (Exception $e) {
            
            $pdo->rollBack();
            $_SESSION['error_message'] = "Menu deletion failed: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "The menu you want to delete is not found.";
    }
} else {
    $_SESSION['error_message'] = "Invalid menu code";
}

header('Location: manage_menus.php');
exit();
?>
