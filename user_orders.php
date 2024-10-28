<?php
include 'includes/db.php';

$table_number = isset($_GET['table_number']) ? intval($_GET['table_number']) : 0;

if ($table_number <= 0) {
    die("Invalid table number");
}


$stmt = $pdo->prepare("SELECT id FROM tables WHERE table_number = ?");
$stmt->execute([$table_number]);
$table = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$table) {
    die("Invalid table number");
}

$table_id = $table['id'];

try {
    $stmt = $pdo->prepare("
        SELECT o.id AS order_id, o.table_id, o.status, o.payment_status, o.order_time, 
               GROUP_CONCAT(m.name SEPARATOR ', ') AS items, SUM(oi.quantity * m.price) AS total_amount
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menus m ON oi.menu_id = m.id
        WHERE o.table_id = ?
        GROUP BY o.id
        ORDER BY o.order_time DESC
    ");
    $stmt->execute([$table_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    $total_sum = array_reduce($orders, function($carry, $order) {
        return $carry + $order['total_amount'];
    }, 0);
} catch (Exception $e) {
    die('An error occurred: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Order list of Table number <?php echo htmlspecialchars($table_number); ?></title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        @media (max-width: 576px) {
            .table-responsive {
                font-size: 0.8rem;
            }
            .table th, .table td {
                padding: 0.3rem;
            }
        }
        .total-summary {
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Order list of Table number <?php echo htmlspecialchars($table_number); ?></h1>

        <?php if (!empty($orders)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Order No</th>
                            <th>status</th>
                            <th>Payment status</th>
                            <th>time</th>
                            <th>Food menu</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_time']); ?></td>
                                <td><?php echo htmlspecialchars($order['items']); ?></td>
                                <td><?php echo htmlspecialchars($order['total_amount']); ?> TWD</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end total-summary">
                Total: <?php echo htmlspecialchars($total_sum); ?> TWD
            </div>
        <?php else: ?>
            <p class="text-center">There are no orders to display</p>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="menu.php?table_number=<?php echo htmlspecialchars($table_number); ?>" class="btn btn-primary mb-2">Back to menu</a>
            <a href="index.php?table_number=<?php echo htmlspecialchars($table_number); ?>" class="btn btn-secondary mb-2">Return to home page</a>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
