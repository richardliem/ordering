<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isset($_SESSION['kitchen_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = filter_var($_GET['order_id'], FILTER_VALIDATE_INT); 
    $status = filter_var($_GET['status'], FILTER_SANITIZE_STRING);  

    if (!$order_id || !$status) {
        die("Invalid order number or status");
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if (!$stmt->execute([$status, $order_id])) {
            throw new Exception("Order status update failed");
        }

        if ($stmt->rowCount() === 0) {
            throw new Exception("No orders were updated, possibly due to invalid order ID");
        }

        if ($status === 'completed') {
            $stmt = $pdo->prepare("INSERT INTO order_history (order_id, table_id, status, order_time, completion_time)
                                   SELECT id, table_id, status, order_time, NOW() FROM orders WHERE id = ?");
            if (!$stmt->execute([$order_id])) {
                throw new Exception("Failed to copy order to order history");
            }
        }

        $pdo->commit();
        header('Location: dashboard.php?status=success');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
} else {
    die("Order ID and status must be specified");
}
?>
