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

// Initialize variables
$error_msg = $success_msg = '';

// Check if form is submitted for paying salary by cash
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['pay_salary_by_cash'])) {
        $employee_id = $_POST['employee_id'];

        // Fetch net salary for the employee
        $fetch_salary = 'SELECT net_salary FROM Salaries WHERE employee_id = ? AND approval = "Approved" AND payment = "NotPayed"';
        $stmt = $conn->prepare($fetch_salary);
        $stmt->bind_param('i', $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $amount = $row['net_salary'];

            // Mark salary as paid
            $update_salary = 'UPDATE Salaries SET payment = "Payed", pay_date = ? WHERE employee_id = ? AND approval = "Approved" AND payment = "NotPayed"';
            $pay_date = date('Y-m-d');
            $stmt = $conn->prepare($update_salary);
            $stmt->bind_param('si', $pay_date, $employee_id);
            if ($stmt->execute()) {
                // Record the payment in cash payment history
                $insert_cash_payment = "INSERT INTO CashPayments (employee_id, amount, payment_date) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_cash_payment);
                $stmt->bind_param('ids', $employee_id, $amount, $pay_date);
                if ($stmt->execute()) {
                    // Subtract salary from the company account
                    $update_balance_company = 'UPDATE Accounts SET balance = balance - ? WHERE account_id = ?';
                    $company_account_id = 1; // Assuming company account has ID 1
                    $stmt = $conn->prepare($update_balance_company);
                    $stmt->bind_param('di', $amount, $company_account_id);
                    $stmt->execute();

                    $success_msg = 'Salary paid successfully to employee ID ' . $employee_id . '!';
                } else {
                    $error_msg = 'Error recording cash payment for employee ID ' . $employee_id . ': ' . $stmt->error;
                }
            } else {
                $error_msg = 'Error paying salary to employee ID ' . $employee_id . ': ' . $stmt->error;
            }
        } else {
            $error_msg = 'No approved, unpaid salary found for employee ID ' . $employee_id;
        }
    }
}

// Fetch employees with their approved salary information
$sql = "SELECT e.employee_id, e.name, e.username, s.net_salary
        FROM employee e
        LEFT JOIN Salaries s ON e.employee_id = s.employee_id
        WHERE s.approval = 'Approved' AND s.payment = 'NotPayed'";
$result = $conn->query($sql);

// Check for errors in fetching employees
if ($result === false) {
    $error_msg = "Error fetching employees: " . mysqli_error($conn);
} else {
    // Fetch the result only if it's not false
    $employees = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance - Pay Salary by Cash</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Finance - Pay Salary by Cash</h2>
        <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error_msg ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success" role="alert">
            <?= $success_msg ?>
        </div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="employee">Select Employee:</label>
                <select class="form-control" id="employee" name="employee_id">
                    <option value="">Select an employee</option>
                    <?php foreach ($employees as $employee): ?>
                    <option value="<?= $employee['employee_id'] ?>"><?= $employee['name'] ?> - <?= $employee['username'] ?> - <?= $employee['net_salary'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" name="pay_salary_by_cash" class="btn btn-primary">Pay Salary</button>
            </div>
        </form>
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