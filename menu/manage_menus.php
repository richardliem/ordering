<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}




$menus = $pdo->query("SELECT m.*, ms.status_name FROM menus m JOIN menu_statuses ms ON m.status_id = ms.id")->fetchAll();
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage food menu</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin/css/style.css">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card img {
            max-height: 200px;
            object-fit: cover;
        }
        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .search-container input {
            max-width: 300px;
        }
        .status-available {
            color: green !important;
        }
        .status-unavailable {
            color: red !important;
        }
        .toggle-status-container {
            display: flex;
            align-items: center;
        }
        .toggle-status-label {
            margin-left: 10px;
        }
    </style>
</head>
<body>

    
    <nav class="sidebar">
        <?php include '../admin/layout/navbar.php'; ?>
    </nav>

    <?php 
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
?>

    <div class="main-content">
        <div class="container mt-3">
            <h1 class="text-center">Manage food menu</h1>
            <div class="row mt-4">
                <div class="col-md-12 search-container mb-3">
                    <a href="add_menu.php" class="btn btn-success">Add food menu</a>
                    <input type="text" id="searchInput" class="form-control" placeholder="Find a menu">
                </div>
                <div class="col-md-12">
                    <div class="row" id="menuCards">
                        <?php foreach ($menus as $menu): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <img src="<?php echo htmlspecialchars($menu['image']); ?>" class="card-img-top" alt="Image">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($menu['name']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($menu['description']); ?></p>
                                        <p class="card-text text-primary"><?php echo htmlspecialchars($menu['price']); ?> TWD</p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                status:
                                                <div class="toggle-status-container">
                                                    <input type="checkbox" class="status-toggle" data-id="<?php echo $menu['id']; ?>" <?php echo (strtolower($menu['status_name']) == 'available') ? 'checked' : ''; ?> data-toggle="toggle" data-on="Available" data-off="Unavailable" data-onstyle="success" data-offstyle="danger">
                                                    <span class="toggle-status-label <?php echo (strtolower($menu['status_name']) == 'available') ? 'status-available' : 'status-unavailable'; ?>"><?php echo (strtolower($menu['status_name']) == 'available') ? 'Available' : 'Unavailable'; ?></span>
                                                </div>
                                            </small>
                                        </p>
                                        <a href="edit_menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-primary btn-sm">correct</a>
                                        <a href="delete_menu.php?id=<?php echo $menu['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this menu?');">delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>
    <script>
        
        document.getElementById('searchInput').addEventListener('input', function() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let menuCards = document.querySelectorAll('#menuCards .col-md-4');

            menuCards.forEach(function(card) {
                let itemName = card.querySelector('.card-title').innerText.toLowerCase();
                if (itemName.includes(input)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        
        document.querySelectorAll('.status-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                let menuId = this.dataset.id;
                let status = this.checked ? 'Available' : 'Unavailable';
                let label = this.nextElementSibling;
                label.textContent = status;
                label.className = this.checked ? 'toggle-status-label status-available' : 'toggle-status-label status-unavailable';

                fetch('update_menu_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: menuId, status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('There was an error updating the menu status.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error updating the menu status.');
                });
            });
        });
    </script>
</body>
</html>
