<?php
include 'includes/db.php';
include 'includes/functions.php';


$table_number = isset($_GET['table_number']) ? intval($_GET['table_number']) : 0;

if ($table_number <= 0) {
    
    die("Invalid Table number");
}


$stmt = $pdo->prepare("SELECT id FROM tables WHERE table_number = ?");
$stmt->execute([$table_number]);
$table = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$table) {
    die("Invalid Table number");
}

$table_id = $table['id'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IndoFood</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero text-white text-center bg-dark py-5">
        <div class="container">
            <h1 class="display-4">Welcome to IndoFood</h1>
            <p class="lead">What would you like to eat ?</p>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="container mt-5">
        <div class="table">
            <h1>Table: <?php echo htmlspecialchars($table_number); ?></h1> 
        </div>

        <div class="menu-buttons my-4 text-center">
            <a href="menu.php?table_number=<?php echo htmlspecialchars($table_number); ?>" class="btn btn-primary mx-2">
                <i class="fas fa-utensils"></i> Order food
            </a>
            <button class="btn btn-secondary mx-2">
                <i class="fas fa-bell"></i> Call staff
            </button>
            <a href="user_orders.php?table_number=<?php echo htmlspecialchars($table_number); ?>" class="btn btn-success mx-2">
                <i class="fas fa-money-bill-wave"></i> Show bill
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <p>&copy; IndoFood </p>
        </div>
    </footer>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="node_modules/@fortawesome/fontawesome-free/js/all.min.js"></script>
</body>
</html>
