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

// Get the first and last day of the current month
$first_day_of_month = date('Y-m-01');
$last_day_of_month = date('Y-m-t');

// Fetch salary history for the current month
$sql = "SELECT e.name, e.username, e.role, e.assigned_for, s.pay_date, s.net_salary
        FROM employee e
        INNER JOIN Salaries s ON e.employee_id = s.employee_id
        WHERE s.payment = 'Payed' AND s.pay_date BETWEEN ? AND ?
        ORDER BY s.pay_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $first_day_of_month, $last_day_of_month);
$stmt->execute();
$result = $stmt->get_result();

// Check for errors in fetching salary history
if ($result === false) {
    $error_msg = "Error fetching salary history: " . $conn->error;
} else {
    // Fetch the result only if it's not false
    $salary_history = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance - Salary History (Current Month)</title>
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
            #printableTable, #printableTable * {
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
        <h2 class="text-center mb-4">Finance - Salary History (Current Month)</h2>
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
                        <th>Pay Date</th>
                        <th>Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($salary_history) && count($salary_history) > 0): ?>
                        <?php foreach ($salary_history as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['name']) ?></td>
                            <td><?= htmlspecialchars($record['username']) ?></td>
                            <td><?= htmlspecialchars($record['role']) ?></td>
                            <td><?= htmlspecialchars($record['assigned_for']) ?></td>
                            <td><?= htmlspecialchars($record['pay_date']) ?></td>
                            <td><?= htmlspecialchars($record['net_salary']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No salary history available for this month.</td>
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
