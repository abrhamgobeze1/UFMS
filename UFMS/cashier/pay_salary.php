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

// Check if form is submitted for paying salary
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['pay_salary_single'])) {
        // Process paying salary to selected employees
        $customer_ids = $_POST['customer_ids'];

        $successful_payments = 0;
        foreach ($customer_ids as $customer_id) {
            // Fetch net salary for the employee
            $fetch_salary = 'SELECT net_salary FROM Salaries WHERE employee_id = ? AND approval = "Approved" AND payment = "NotPayed"';
            $stmt = $conn->prepare($fetch_salary);
            $stmt->bind_param('i', $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $amount = $row['net_salary'];

                // Update balance for the employee
                $update_balance_employee = 'UPDATE Accounts SET balance = balance + ? WHERE employee_id = ?';
                $stmt = $conn->prepare($update_balance_employee);
                $stmt->bind_param('di', $amount, $customer_id);
                if ($stmt->execute()) {
                    // Mark salary as paid
                    $update_salary = 'UPDATE Salaries SET payment = "Payed", pay_date = ? WHERE employee_id = ? AND approval = "Approved" AND payment = "NotPayed"';
                    $pay_date = date('Y-m-d');
                    $stmt = $conn->prepare($update_salary);
                    $stmt->bind_param('si', $pay_date, $customer_id);
                    $stmt->execute();
                    
                    // Record the payment in salary history
                    $insert_transaction_employee = "INSERT INTO Transactions (from_account, to_account, type, amount) VALUES (?, ?, 'deposit', ?)";
                    $from_account_employee = 1; // Assuming company account has ID 1
                    $stmt = $conn->prepare($insert_transaction_employee);
                    $stmt->bind_param('iid', $from_account_employee, $customer_id, $amount);
                    $stmt->execute();
                    
                    // Subtract salary from the company account
                    $update_balance_company = 'UPDATE Accounts SET balance = balance - ? WHERE account_id = ?';
                    $stmt = $conn->prepare($update_balance_company);
                    $stmt->bind_param('di', $amount, $from_account_employee);
                    $stmt->execute();
                    
                    $successful_payments++;
                } else {
                    $error_msg .= 'Error paying salary to employee ID ' . $customer_id . ': ' . $stmt->error . '<br>';
                }
            } else {
                $error_msg .= 'No approved, unpaid salary found for employee ID ' . $customer_id . '<br>';
            }
        }
        if ($successful_payments > 0) {
            $success_msg = 'Salary paid successfully to ' . $successful_payments . ' employee(s)!';
        }
    } elseif (isset($_POST['pay_salary_all'])) {
        // Process paying salary to all employees

        // Fetch all approved, unpaid salaries
        $fetch_salaries = 'SELECT employee_id, net_salary FROM Salaries WHERE approval = "Approved" AND payment = "NotPayed"';
        $result = $conn->query($fetch_salaries);

        if ($result && $result->num_rows > 0) {
            $successful_payments = 0;
            while ($row = $result->fetch_assoc()) {
                $customer_id = $row['employee_id'];
                $amount = $row['net_salary'];

                // Update balance for the employee
                $update_balance_employee = 'UPDATE Accounts SET balance = balance + ? WHERE employee_id = ?';
                $stmt = $conn->prepare($update_balance_employee);
                $stmt->bind_param('di', $amount, $customer_id);
                if ($stmt->execute()) {
                    // Mark salary as paid
                    $update_salary = 'UPDATE Salaries SET payment = "Payed", pay_date = ? WHERE employee_id = ? AND approval = "Approved" AND payment = "NotPayed"';
                    $pay_date = date('Y-m-d');
                    $stmt = $conn->prepare($update_salary);
                    $stmt->bind_param('si', $pay_date, $customer_id);
                    $stmt->execute();
                    
                    // Record the payment in salary history
                    $insert_transaction_employee = "INSERT INTO Transactions (from_account, to_account, type, amount) VALUES (?, ?, 'deposit', ?)";
                    $from_account_employee = 1; // Assuming company account has ID 1
                    $stmt = $conn->prepare($insert_transaction_employee);
                    $stmt->bind_param('iid', $from_account_employee, $customer_id, $amount);
                    $stmt->execute();
                    
                    // Subtract salary from the company account
                    $update_balance_company = 'UPDATE Accounts SET balance = balance - ? WHERE account_id = ?';
                    $stmt = $conn->prepare($update_balance_company);
                    $stmt->bind_param('di', $amount, $from_account_employee);
                    $stmt->execute();
                    
                    $successful_payments++;
                } else {
                    $error_msg .= 'Error paying salary to employee ID ' . $customer_id . ': ' . $stmt->error . '<br>';
                }
            }
            if ($successful_payments > 0) {
                $success_msg = 'Salary paid successfully to ' . $successful_payments . ' employee(s)!';
            }
        } else {
            $error_msg = 'No approved, unpaid salaries found.';
        }
    }
}



// Fetch employees with their approved salary information
$sql = "SELECT e.employee_id, e.name, e.username, e.phone, e.role, e.assigned_for, e.hire_date, e.salary, a.account_number, a.balance, s.base_salary, s.deductions, s.bonuses, s.net_salary
        FROM employee e
        LEFT JOIN Accounts a ON e.employee_id = a.employee_id
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
    <title>Finance - Pay Salary</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Finance - Pay Salary</h2>
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
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Assigned For</th>
                            <th>Hire Date</th>
                            <th>Base Salary</th>
                            <th>Deductions</th>
                            <th>Bonuses</th>
                            <th>Net Salary</th>
                            <th>Account Number</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="employee<?= $employee['employee_id'] ?>" name="customer_ids[]" value="<?= $employee['employee_id'] ?>">
                                </div>
                            </td>
                            <td><?= $employee['name'] ?></td>
                            <td><?= $employee['username'] ?></td>
                            <td><?= $employee['phone'] ?></td>
                            <td><?= $employee['role'] ?></td>
                            <td><?= $employee['assigned_for'] ?></td>
                            <td><?= $employee['hire_date'] ?></td>
                            <td><?= $employee['base_salary'] ?></td>
                            <td><?= $employee['deductions'] ?></td>
                            <td><?= $employee['bonuses'] ?></td>
                            <td><?= $employee['net_salary'] ?></td>
                            <td><?= $employee['account_number'] ?></td>
                            <td><?= $employee['balance'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" name="pay_salary_single" class="btn btn-primary">Pay Selected</button>
                <button type="submit" name="pay_salary_all" class="btn btn-success ml-2">Pay All</button>
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
