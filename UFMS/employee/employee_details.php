<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as an employee, if not redirect to login page
if (!isset($_SESSION["user_type"]) || ($_SESSION["user_type"] !== "Teacher" && $_SESSION["user_type"] !== "Cleaner" && $_SESSION["user_type"] !== "Dormitory Proctors" && $_SESSION["user_type"] !== "Libraries" && $_SESSION["user_type"] !== "Lab Assistants" && $_SESSION["user_type"] !== "Security" && $_SESSION["user_type"] !== "Cafe Workers" && $_SESSION["user_type"] !== "Cashier" && $_SESSION["user_type"] !== "Finance Managers" && $_SESSION["user_type"] !== "Accountant")) {
    header("Location: ../login.php");
    exit;
}

$employee_id = $_SESSION["user_id"];

// Fetch employee account details
$sql = "SELECT a.account_id, a.account_number, a.balance 
        FROM Accounts a
        WHERE a.employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$account_details = $result->fetch_assoc();

// Fetch employee personal details
$sql = "SELECT name, phone, role, assigned_for, hire_date, salary, username 
        FROM employee
        WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee_details = $result->fetch_assoc();

// Fetch transaction history
$sql = "SELECT t.type, t.amount, t.timestamp
        FROM Transactions t
        WHERE t.from_account = ? OR t.to_account = ?
        ORDER BY t.timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $account_details['account_id'], $account_details['account_id']);
$stmt->execute();
$result = $stmt->get_result();
$transaction_history = [];
while ($row = $result->fetch_assoc()) {
    $transaction_history[] = $row;
}

// Fetch cash payment details
$sql = "SELECT cp.amount, cp.payment_date
        FROM CashPayments cp
        WHERE cp.employee_id = ?
        ORDER BY cp.payment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$cash_payments = [];
while ($row = $result->fetch_assoc()) {
    $cash_payments[] = $row;
}

$stmt->close();

// Include header
include_once '../includes/header.php';
?>

<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Employee Account Details</h2>
        <div class="row mb-4">
            <div class="col-md-6">
                <h4>Personal Details</h4>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Name:</th>
                            <td><?php echo htmlspecialchars($employee_details['name']); ?></td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td><?php echo htmlspecialchars($employee_details['phone']); ?></td>
                        </tr>
                        <tr>
                            <th>Role:</th>
                            <td><?php echo htmlspecialchars($employee_details['role']); ?></td>
                        </tr>
                        <tr>
                            <th>Assigned For:</th>
                            <td><?php echo htmlspecialchars($employee_details['assigned_for']); ?></td>
                        </tr>
                        <tr>
                            <th>Hire Date:</th>
                            <td><?php echo htmlspecialchars($employee_details['hire_date']); ?></td>
                        </tr>
                        <tr>
                            <th>Salary:</th>
                            <td><?php echo htmlspecialchars(number_format($employee_details['salary'], 2)); ?></td>
                        </tr>
                        <tr>
                            <th>Username:</th>
                            <td><?php echo htmlspecialchars($employee_details['username']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Account Details</h4>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Account ID:</th>
                            <td><?php echo htmlspecialchars($account_details['account_id']); ?></td>
                        </tr>
                        <tr>
                            <th>Account Number:</th>
                            <td><?php echo htmlspecialchars($account_details['account_number']); ?></td>
                        </tr>
                        <tr>
                            <th>Balance:</th>
                            <td><?php echo htmlspecialchars(number_format($account_details['balance'], 2)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <h4>Cash Payments</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cash_payments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>