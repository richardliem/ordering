<?php
include 'includes/db.php';
include 'includes/functions.php';


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
    $stmt = $pdo->prepare("SELECT m.*, ms.status_name FROM menus m JOIN menu_statuses ms ON m.status_id = ms.id");
    $stmt->execute();
    $menus = $stmt->fetchAll();
} catch (PDOException $e) {
    die("There was an error retrieving menu data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Menu - IndoFood</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card img {
            max-height: 200px;
            object-fit: cover;
        }
        .quantity-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }
        .quantity-container input {
            width: 50px;
            text-align: center;
        }
        .cart-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            padding: 15px;
            cursor: pointer;
            z-index: 1000;
            display: block;
        }

        @media (max-width: 768px) {
            .cart-icon {
                right: 34px;
                bottom: 23px;
            }
        }
        .cart-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
        }
        .modal-content {
            max-width: 600px;
            margin: auto;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999; 
            padding: 3px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">IndoFood</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php?table_number=<?php echo htmlspecialchars($table_number); ?>">Home page</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="menu.php?table_number=<?php echo htmlspecialchars($table_number); ?>">menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">contact</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Food Menu - Table: <?php echo htmlspecialchars($table_number); ?></h1>

        <div class="search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Find a menu">
        </div>

        <div class="row" id="menuCards">
            <?php foreach ($menus as $menu): ?>
                <div class="col-md-4 mb-3 menu-item">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($menu['image']); ?>" alt="<?php echo htmlspecialchars($menu['name']); ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($menu['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($menu['description']); ?></p>
                            <p class="card-text text-primary"><?php echo htmlspecialchars($menu['price']); ?> TWD</p>
                            <p class="card-text"><small class="<?php echo (strtolower($menu['status_name']) == 'available' ? 'text-success' : 'text-danger'); ?>"> status: <?php echo htmlspecialchars($menu['status_name']); ?></small></p>
                            <div class="quantity-container">
                                <button class="btn btn-outline-secondary btn-sm" onclick="decreaseQuantity(this)">-</button>
                                <input type="number" class="form-control quantity-input" value="1" min="1">
                                <button class="btn btn-outline-secondary btn-sm" onclick="increaseQuantity(this)">+</button>
                            </div>
                            <button class="btn btn-primary add-to-cart mt-2" data-id="<?php echo htmlspecialchars($menu['id']); ?>" data-name="<?php echo htmlspecialchars($menu['name']); ?>" data-price="<?php echo htmlspecialchars($menu['price']); ?>">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="cart-icon" data-bs-toggle="modal" data-bs-target="#cartModal">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge" id="cart-count">0</span>
    </div>

    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Menu name</th>
                                <th>quantity</th>
                                <th>price</th>
                                <th>add</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            
                        </tbody>
                    </table>
                    <div class="text-end">
                        <h5>Total price: <span id="total-price">0</span> TWD</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">close</button>
                    <button type="button" class="btn btn-primary" id="confirm-order">Confirm order</button>
                    <a href="index.php?table_number=<?php echo htmlspecialchars($table_number); ?>" id="go-home" class="btn btn-success" style="display: none;">Go to home page</a>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Item has been added to the cart.
            </div>
        </div>
    </div>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="node_modules/@fortawesome/fontawesome-free/js/all.min.js"></script>
    <script>
    function increaseQuantity(button) {
        let input = button.previousElementSibling;
        input.value = parseInt(input.value) + 1;
    }

    function decreaseQuantity(button) {
        let input = button.nextElementSibling;
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    document.getElementById('searchInput').addEventListener('input', function() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let menuItems = document.querySelectorAll('.menu-item');

        menuItems.forEach(function(item) {
            let itemName = item.querySelector('.card-title').innerText.toLowerCase();
            if (itemName.includes(input)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = this.dataset.price;
            const quantity = this.previousElementSibling.querySelector('.quantity-input').value;
            const item = { id, name, price, quantity: parseInt(quantity) };

            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            const index = cart.findIndex(cartItem => cartItem.id === id);
            if (index === -1) {
                cart.push(item);
            } else {
                cart[index].quantity += item.quantity;
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            showToast(`${name} quantity ${quantity} Added to cart`);
            updateCartIcon();
        });
    });

    function showToast(message) {
        const toastElement = document.getElementById('liveToast');
        const toastBody = toastElement.querySelector('.toast-body');
        toastBody.textContent = message;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }

    function updateCartIcon() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
        document.getElementById('cart-count').textContent = cartCount;
    }

    function renderCartItems() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartItems = document.getElementById('cart-items');
        cartItems.innerHTML = '';
        let totalPrice = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            totalPrice += itemTotal;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>
                    <button class="btn btn-outline-secondary btn-sm" onclick="decreaseCartItemQuantity('${item.id}')">-</button>
                    <span>${item.quantity}</span>
                    <button class="btn btn-outline-secondary btn-sm" onclick="increaseCartItemQuantity('${item.id}')">+</button>
                </td>
                <td>${item.price}</td>
                <td>${itemTotal}</td>
                <td><button class="btn btn-danger btn-sm" onclick="removeCartItem('${item.id}')">delete</button></td>
            `;
            cartItems.appendChild(row);
        });

        document.getElementById('total-price').textContent = totalPrice.toFixed(2);
    }

    function increaseCartItemQuantity(id) {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const index = cart.findIndex(item => item.id === id);
        if (index !== -1) {
            cart[index].quantity++;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCartItems();
            updateCartIcon();
        }
    }

    function decreaseCartItemQuantity(id) {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const index = cart.findIndex(item => item.id === id);
        if (index !== -1 && cart[index].quantity > 1) {
            cart[index].quantity--;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCartItems();
            updateCartIcon();
        }
    }

    function removeCartItem(id) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item => item.id !== id);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCartItems();
        updateCartIcon();
    }

    document.querySelector('.cart-icon').addEventListener('click', renderCartItems);

    document.addEventListener('DOMContentLoaded', updateCartIcon);

    document.getElementById('confirm-order').addEventListener('click', function() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart.length > 0) {
            
            const tableId = <?php echo $table_id; ?>;
            
            fetch('confirm_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ table_id: tableId, items: cart })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('cart');
                    updateCartIcon();
                    renderCartItems();
                    showToast('Confirm successful order');
                    
                    document.getElementById('go-home').style.display = 'block';
                } else {
                    showToast('There was an error confirming the order: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('There was an error confirming the order.');
            });
        } else {
            showToast('The shopping cart is empty');
        }
    });
    </script>
</body>
</html>
