<?php
session_start();
include '../includes/db.php';
include 'functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}


$menuCount = $pdo->query("SELECT COUNT(*) FROM menus")->fetchColumn();
$orderPendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$orderTotalCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM receipts")->fetchColumn();
$tableCount = $pdo->query("SELECT COUNT(*) FROM tables WHERE status = 'occupied'")->fetchColumn();
$employeeCount = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
$recentActivities = $pdo->query("SELECT * FROM activity_logs ORDER BY timestamp DESC LIMIT 5")->fetchAll();
$orderHistories = $pdo->query("SELECT * FROM order_history ORDER BY completion_time DESC LIMIT 10")->fetchAll();

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 12px;
        }
        .card-title {
            font-size: 1.2rem;
        }
        .card-text {
            font-size: 1rem;
        }
        .main-content {
            margin-top: 20px;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
        }
        .scrollable-list {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

    <!-- แถบเมนู -->
    <?php include 'layout/navbar.php'; ?>

    <div class="main-content">
        <div class="container mt-3">
            <h1 class="text-center">Admin Dashboard</h1>
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Number of food menus</h5>
                            <p class="card-text"><?php echo $menuCount; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Pending orders</h5>
                            <p class="card-text"><?php echo $orderPendingCount; ?> Purchase Order</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">All Orders</h5>
                            <p class="card-text"><?php echo $orderTotalCount; ?> Purchase Order</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total income</h5>
                            <p class="card-text"><?php echo number_format($totalRevenue, 2); ?> TWD</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Number of tables in use</h5>
                            <p class="card-text"><?php echo $tableCount; ?> Table</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-secondary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Number of employees</h5>
                            <p class="card-text"><?php echo $employeeCount; ?> person</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <h4 class="text-center">Recent activities</h4>
                    <ul class="list-group scrollable-list">
                        <?php foreach ($recentActivities as $activity): ?>
                            <li class="list-group-item">
                                <?php echo $activity['timestamp'] . " - " . $activity['action']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4 class="text-center">Recent Order History</h4>
                    <ul class="list-group scrollable-list">
                        <?php foreach ($orderHistories as $history): ?>
                            <li class="list-group-item">
                            Purchase Order #<?php echo $history['order_id']; ?> 
                                - status: <?php echo $history['status']; ?>
                                - time: <?php echo $history['completion_time']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../node_modules/@fortawesome/fontawesome-free/js/all.min.js"></script>

</body>
</html>
