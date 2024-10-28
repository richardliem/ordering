<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit();
}


$id = $_GET['id'];
$table = $pdo->prepare("SELECT * FROM tables WHERE id = ?");
$table->execute([$id]);
$table = $table->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $max_capacity = $_POST['max_capacity'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tables SET max_capacity = ?, status = ? WHERE id = ?");
    $stmt->execute([$max_capacity, $status, $id]);

    header('Location: manage_tables.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit table</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../admin/layout/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Edit table</h1>
        <form action="edit_table.php?id=<?php echo $id; ?>" method="POST">
            <div class="mb-3">
                <label for="max_capacity" class="form-label">Maximum capacity</label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity" value="<?php echo htmlspecialchars($table['max_capacity']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="available" <?php if ($table['status'] == 'available') echo 'selected'; ?>>available</option>
                    <option value="occupied" <?php if ($table['status'] == 'occupied') echo 'selected'; ?>>unavailable</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Change log</button>
        </form>
    </div>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
