<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as Cashier, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "Cashier") {
    header("Location: ../login.php");
    exit;
}

// Initialize error message
$error_msg = '';

// Fetch cash salary history
$sql = "SELECT e.name, e.username, e.role, e.assigned_for, c.amount, c.payment_date
        FROM employee e
        INNER JOIN CashPayments c ON e.employee_id = c.employee_id
        ORDER BY c.payment_date DESC";
$result = $conn->query($sql);

// Check for errors in fetching cash salary history
if ($result === false) {
    $error_msg = "Error fetching cash salary history: " . $conn->error;
} else {
    // Fetch the result only if it's not false
    $cash_salary_history = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance - Cash Salary History</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printableTable,
            #printableTable * {
                visibility: visible;
            }

            #printableTable {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Finance - Cash Salary History</h2>
        <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error_msg ?>
        </div>
        <?php endif; ?>
        <button class="btn btn-primary btn-print mb-3" onclick="window.print()">Print</button>
        <div class="table-responsive" id="printableTable">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Assigned For</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($cash_salary_history) && count($cash_salary_history) > 0): ?>
                    <?php foreach ($cash_salary_history as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['name']) ?></td>
                        <td><?= htmlspecialchars($record['username']) ?></td>
                        <td><?= htmlspecialchars($record['role']) ?></td>
                        <td><?= htmlspecialchars($record['assigned_for']) ?></td>
                        <td><?= htmlspecialchars($record['amount']) ?></td>
                        <td><?= htmlspecialchars($record['payment_date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No cash salary history available.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>