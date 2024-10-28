<?php
include '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $table_id = $data['table_id'];

    if (!isset($_SESSION['staff_logged_in'])) {
        echo json_encode(['success' => false, 'message' => 'You are not logged in.']);
        exit();
    }

    try {
        $pdo->beginTransaction();

        
        $stmt = $pdo->prepare("SELECT table_number FROM tables WHERE id = ?");
        $stmt->execute([$table_id]);
        $table = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$table) {
            throw new Exception("The specified table was not found");
        }

        $table_number = $table['table_number'];

        
        $stmt = $pdo->prepare("
            SELECT o.id AS order_id, o.order_time, oi.menu_id, oi.quantity, m.price, (oi.quantity * m.price) AS item_total 
            FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            JOIN menus m ON oi.menu_id = m.id 
            WHERE o.table_id = ? AND o.payment_status = 'pending'
        ");
        $stmt->execute([$table_id]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orderItems)) {
            throw new Exception("No pending orders found for this table");
        }

        
        $total_amount = array_sum(array_column($orderItems, 'item_total'));

        
        $stmt = $pdo->prepare("INSERT INTO receipts (table_number, total_amount) VALUES (?, ?)");
        $stmt->execute([$table_number, $total_amount]);
        $receipt_id = $pdo->lastInsertId();

        
        foreach ($orderItems as $item) {
            $stmt = $pdo->prepare("INSERT INTO receipt_items (receipt_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$receipt_id, $item['menu_id'], $item['quantity'], $item['price']]);
        }

        
        $stmt = $pdo->prepare("INSERT INTO order_history (order_id, table_id, status, order_time, completion_time) VALUES (?, ?, ?, ?, NOW())");
        foreach ($orderItems as $item) {
            $stmt->execute([$item['order_id'], $table_id, 'completed', $item['order_time']]);
        }

        
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid' WHERE table_id = ? AND payment_status = 'pending'");
        $stmt->execute([$table_id]);


        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE table_id = ? AND payment_status = 'paid')");
        $stmt->execute([$table_id]);

        $stmt = $pdo->prepare("DELETE FROM orders WHERE table_id = ? AND payment_status = 'paid'");
        $stmt->execute([$table_id]);

        
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        $pdo->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}

