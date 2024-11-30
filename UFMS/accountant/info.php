<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as Accountant, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "Accountant") {
    header("Location: ../login.php");
    exit;
}

// Fetch Accountant's information from the session
$cashier_id = $_SESSION["user_id"];

// Query to fetch Accountant's information
$sql = "SELECT e.name, e.phone, e.role, e.hire_date, e.salary, a.account_number, a.balance 
        FROM employee e
        INNER JOIN Accounts a ON e.employee_id = a.employee_id
        WHERE e.employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cashier_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query executed successfully
if ($result->num_rows > 0) {
    $cashier_info = $result->fetch_assoc();
} else {
    echo "No cashier found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accountant Information</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Accountant Information</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th>Name:</th>
                        <td><?php echo htmlspecialchars($cashier_info['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?php echo htmlspecialchars($cashier_info['phone']); ?></td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td><?php echo htmlspecialchars($cashier_info['role']); ?></td>
                    </tr>
                    <tr>
                        <th>Hire Date:</th>
                        <td><?php echo htmlspecialchars($cashier_info['hire_date']); ?></td>
                    </tr>
                    <tr>
                        <th>Salary:</th>
                        <td><?php echo htmlspecialchars($cashier_info['salary']); ?></td>
                    </tr>
                    <tr>
                        <th>Account Number:</th>
                        <td><?php echo htmlspecialchars($cashier_info['account_number']); ?></td>
                    </tr>
                    <tr>
                        <th>Balance:</th>
                        <td><?php echo htmlspecialchars($cashier_info['balance']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>