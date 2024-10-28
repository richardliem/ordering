<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $job_title = $_POST['job_title'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $status = $_POST['status'];
    $salary = $_POST['salary'];

    $stmt = $pdo->prepare("INSERT INTO employees (name, lastname, job_title, username, password, status, salary) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $lastname, $job_title, $username, $password, $status, $salary]);

    header('Location: manage_employees.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add employees</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include '../admin/layout/navbar.php'; ?>
    <div class="container mt-5">
    <h1 class="text-center">Add employees</h1>
        <form action="add_employee.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">surname</label>
                <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <div class="mb-3">
                <label for="job_title" class="form-label">position</label>
                <select class="form-select" id="job_title" name="job_title" required>
                    <option value="staff">employee</option>
                    <option value="kitchen">kitchen</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active">Use</option>
                    <option value="inactive">Not in use</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="salary" class="form-label">salary</label>
                <input type="number" step="0.01" class="form-control" id="salary" name="salary" required>
            </div>
            <button type="submit" class="btn btn-primary">record</button>
        </form>
    </div>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
