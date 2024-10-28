<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['staff_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Fetch all orders and table statuses
$orders = $pdo->query("
    SELECT o.id AS order_id, o.table_id, o.order_time, o.status, o.payment_status, t.table_number, oi.menu_id, oi.quantity, m.name, m.price 
    FROM orders o 
    JOIN tables t ON o.table_id = t.id 
    JOIN order_items oi ON o.id = oi.order_id
    JOIN menus m ON oi.menu_id = m.id
    ORDER BY o.order_time DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Group orders by table
$groupedOrders = [];
foreach ($orders as $order) {
    $table_number = $order['table_number'];
    if (!isset($groupedOrders[$table_number])) {
        $groupedOrders[$table_number] = [
            'table_number' => $table_number,
            'table_id' => $order['table_id'],
            'orders' => []
        ];
    }

    $order_id = $order['order_id'];
    if (!isset($groupedOrders[$table_number]['orders'][$order_id])) {
        $groupedOrders[$table_number]['orders'][$order_id] = [
            'order_id' => $order_id,
            'order_time' => $order['order_time'],
            'status' => $order['status'],
            'payment_status' => $order['payment_status'],
            'items' => []
        ];
    }

    $groupedOrders[$table_number]['orders'][$order_id]['items'][] = [
        'name' => $order['name'],
        'quantity' => $order['quantity'],
        'price' => $order['price']
    ];
}

// Function to display order status
function getOrderStatusText($status) {
    switch ($status) {
        case 'pending':
            return 'pending';
        case 'preparing':
            return 'preparing';
        case 'completed':
            return 'completed';
        default:
            return 'Unknown status';
    }
}

// Function to display payment status
function getPaymentStatusText($status) {
    switch ($status) {
        case 'pending':
            return 'Waiting for payment';
        case 'paid':
            return 'Paid';
        default:
            return 'Payment status unknown';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-pending { color: #dc3545; }
        .status-preparing { color: #ffc107; }
        .status-completed { color: #28a745; }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['admin_logged_in'])): ?>
        <?php include '../admin/layout/navbar.php'; ?>
    <?php endif; ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Employee Dashboard</h1>

        <?php if (isset($_SESSION['staff_logged_in']) || isset($_SESSION['admin_logged_in'])): ?>
            <div class="alert alert-info text-center">Login by: <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?> (<?php echo isset($_SESSION['staff_logged_in']) ? 'employee' : 'Admin'; ?>)</div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">All Orders</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Table number</th>
                                <th>Food menu</th>
                                <th>time</th>
                                <th>status</th>
                                <th>Payment status</th>
                                <th>management</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedOrders as $table) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                                    <td>
                                        <?php
                                        $totalPrice = 0;
                                        foreach ($table['orders'] as $order) {
                                            foreach ($order['items'] as $item) {
                                                $itemTotal = $item['quantity'] * $item['price'];
                                                $totalPrice += $itemTotal;
                                                echo htmlspecialchars($item['name']) . ' x' . htmlspecialchars($item['quantity']) . ' (' . number_format($itemTotal, 2) . ' TWD)<br>';
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $orderTimes = array_column($table['orders'], 'order_time');
                                        echo htmlspecialchars(min($orderTimes));
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $orderStatuses = array_unique(array_column($table['orders'], 'status'));
                                        foreach ($orderStatuses as $status) {
                                            echo '<span class="status-' . htmlspecialchars($status) . '">' . getOrderStatusText($status) . '</span><br>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $paymentStatuses = array_unique(array_column($table['orders'], 'payment_status'));
                                        foreach ($paymentStatuses as $status) {
                                            echo '<span class="payment-status-' . htmlspecialchars($status) . '">' . getPaymentStatusText($status) . '</span><br>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-sm pay-bill-btn" data-table-id="<?php echo htmlspecialchars($table['table_id']); ?>">Pay bill</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="manage_table_status.php" class="btn btn-secondary">Manage table status</a>
        </div>
    </div>

    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.pay-bill-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tableId = this.getAttribute('data-table-id');
                if (confirm('Do you want to pay for this order??')) {
                    
                    fetch('pay_bill.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ table_id: tableId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Bill has been paid');
                            location.reload();
                        } else {
                            alert('An error occurred: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('There was an error while paying the bill');
                    });
                }
            });
        });
    </script>
</body>
</html>
