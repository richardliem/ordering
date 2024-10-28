<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table_number = intval($_POST['table_number']);
    $max_capacity = intval($_POST['max_capacity']);
    $status = $_POST['status'];

    try {
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tables WHERE table_number = ?");
        $stmt->execute([$table_number]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            throw new Exception('This table number already exists');
        }

        
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../index.php';
        $tableData = $baseUrl . '?table_number=' . $table_number;
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($tableData);

        
        $stmt = $pdo->prepare("INSERT INTO tables (table_number, max_capacity, status, qr_code) VALUES (?, ?, ?, ?)");
        $stmt->execute([$table_number, $max_capacity, $status, $qrUrl]);

        header('Location: manage_tables.php');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a table</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../admin/layout/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Add a table</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form action="add_table.php" method="POST">
            <div class="mb-3">
                <label for="table_number" class="form-label">Table number</label>
                <input type="number" class="form-control" id="table_number" name="table_number" required>
            </div>
            <div class="mb-3">
                <label for="max_capacity" class="form-label">Maximum capacity</label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="available">available</option>
                    <option value="occupied">unavailable</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">record</button>
        </form>
    </div>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
