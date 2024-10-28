<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<section id="cart" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Shopping Cart</h2>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Menu name</th>
                            <th>price</th>
                            <th>quantity</th>
                            <th>together</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                        
                    </tbody>
                </table>
                <div class="text-end">
                    <h4>Total price: <span id="totalPrice">0</span> TWD</h4>
                    <button class="btn btn-success" id="checkout">Confirm order</button>
                   
                    <a href="user_orders.php" id="viewOrder" class="btn btn-primary mt-2">Go to my orders</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('checkout').addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Empty basket');
            return;
        }

        const order = {
            table_id: <?php echo isset($_GET['table_id']) ? intval($_GET['table_id']) : 0; ?>,
            items: cart,
            total: totalPrice
        };

        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(order)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                localStorage.removeItem('cart');
                alert('Your order has been confirmed.');
            } else {
                alert('There was an error confirming the order: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error confirming the order.');
        });
    });
</script>
</body>
</html>
