<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Fetch all tables
$tables = $pdo->query("SELECT id, table_number, status FROM tables")->fetchAll(PDO::FETCH_ASSOC);

// Function to display table status
function getTableStatusText($status) {
    switch ($status) {
        case 'available':
            return 'available';
        case 'occupied':
            return 'unavailable';
        default:
            return 'Unknown status';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Table Status</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .table-available { color: #28a745; }
        .table-occupied { color: #dc3545; }
        .table-status-icon {
            cursor: pointer;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Manage Table Status</h1>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h2 class="h4 mb-0">All Table Status</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Table number</th>
                                <th>Table status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $table) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($table['table_number']); ?></td>
                                    <td class="text-center">
                                        <i class="bi <?php echo $table['status'] == 'available' ? 'bi-check-circle-fill table-available' : 'bi-x-circle-fill table-occupied'; ?> table-status-icon" data-table-id="<?php echo htmlspecialchars($table['id']); ?>" data-status="<?php echo htmlspecialchars($table['status']); ?>"></i>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.table-status-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const tableId = this.getAttribute('data-table-id');
                const currentStatus = this.getAttribute('data-status');
                const newStatus = currentStatus === 'available' ? 'occupied' : 'available';
                if (confirm(`You want to change the status of this table to ${newStatus === 'available' ? 'available' : 'unavailable'} Or not?`)) {
                    
                    fetch('update_table_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ table_id: tableId, status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.toggle('bi-check-circle-fill');
                            this.classList.toggle('bi-x-circle-fill');
                            this.classList.toggle('table-available');
                            this.classList.toggle('table-occupied');
                            this.setAttribute('data-status', newStatus);
                            alert('The table status has been changed successfully.');
                        } else {
                            alert('An error occurred: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while changing the table status');
                    });
                }
            });
        });
    </script>
</body>
</html>
