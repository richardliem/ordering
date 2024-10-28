<?php
include '../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'], $data['status'])) {
    $id = intval($data['id']);
    $status = $data['status'] == 'Available' ? 1 : 2; 

    $stmt = $pdo->prepare("UPDATE menus SET status_id = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
