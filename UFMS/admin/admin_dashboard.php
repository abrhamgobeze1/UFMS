<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch system statistics
$total_employee = 0;
$total_HRManagers = 0;
$total_accountant = 0;
$total_FinanceManagers = 0;
$total_cashier = 0;
$total_admin = 0;

// Query to fetch total employee
$sql = "SELECT COUNT(*) AS total_employee FROM employee";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_employee = $result->fetch_assoc()["total_employee"];
} else {
    echo "Error fetching total employee: " . $conn->error;
}

// Query to fetch total employees with 'HR Managers' role
$sql = "SELECT COUNT(*) AS total_HRManagers FROM employee WHERE role = 'HR Managers'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_HRManagers = $result->fetch_assoc()["total_HRManagers"];
} else {
    echo "Error fetching total HR Managers: " . $conn->error;
}

// Query to fetch total employees with 'Accountant' role
$sql = "SELECT COUNT(*) AS total_accountant FROM employee WHERE role = 'Accountant'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_accountant = $result->fetch_assoc()["total_accountant"];
} else {
    echo "Error fetching total Accountants: " . $conn->error;
}

// Query to fetch total employees with 'Finance Managers' role
$sql = "SELECT COUNT(*) AS total_FinanceManagers FROM employee WHERE role = 'Finance Managers'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_FinanceManagers = $result->fetch_assoc()["total_FinanceManagers"];
} else {
    echo "Error fetching total Finance Managers: " . $conn->error;
}

// Query to fetch total employees with 'Cashier' role
$sql = "SELECT COUNT(*) AS total_cashier FROM employee WHERE role = 'Cashier'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_cashier = $result->fetch_assoc()["total_cashier"];
} else {
    echo "Error fetching total Cashiers: " . $conn->error;
}

// Query to fetch total employees with 'Admin' role
$sql = "SELECT COUNT(*) AS total_admin FROM employee WHERE role = 'Admin'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_admin = $result->fetch_assoc()["total_admin"];
} else {
    echo "Error fetching total Admins: " . $conn->error;
}

// Query to fetch total HR Requests
$sql = "SELECT COUNT(*) AS total_HRRequests FROM HRRequests";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_HRRequests = $result->fetch_assoc()["total_HRRequests"];
} else {
    echo "Error fetching total HR Requests: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Online Dormitory Placement System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Admin Dashboard</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Employees</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_employee; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total HR Manager</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_HRManagers; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Accountant</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_accountant; ?></h5>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Finance Manager</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_FinanceManagers; ?></h5>
                    </div>
                </div>
            </div>



            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Cashier </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_cashier; ?></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total HR Request</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_HRRequests; ?></h5>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</html>