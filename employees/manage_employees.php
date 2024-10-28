<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit();
}


$employees = $pdo->query("SELECT * FROM employees")->fetchAll();

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage employees</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include '../admin/layout/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Manage employees</h1>
        <div class="text-end mb-3">
            <a href="add_employee.php" class="btn btn-primary">Add employees</a>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Position</th>
                <th>Username</th>
                <th>Status</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                        <td><?php echo htmlspecialchars($employee['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($employee['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($employee['username']); ?></td>
                        <td><?php echo htmlspecialchars($employee['status']); ?></td>
                        <td>
                            <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" class="btn btn-warning btn-sm">correct</a>
                            <a href="delete_employee.php?id=<?php echo $employee['id']; ?>" class="btn btn-danger btn-sm">delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
