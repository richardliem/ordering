<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $status_id = $_POST['status_id'];
    $image = $_FILES['image']['name'];
    $target = "../upload/img/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("INSERT INTO menus (name, description, price, image, status_id, category) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $target, $status_id, $category]);
        header('Location: manage_menus.php');
        exit();
    } else {
        $error = "Image upload failed";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add menu</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../admin/layout/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center">Add menu</h1>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <form action="add_menu.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Menu name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">price</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">type</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="single_dish">One-dish meal</option>
                    <option value="side_dish">side dish</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status_id" class="form-label">status</label>
                <select class="form-select" id="status_id" name="status_id" required>
                    <option value="1">Available</option>
                    <option value="2">Unavailable</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">picture</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary">record</button>
        </form>
    </div>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
