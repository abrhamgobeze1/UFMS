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

// Fetch HR manager's assigned_for value from the session
$assigned_for = $_SESSION["assigned_for"];
$name = $_SESSION["name"];
$username = $_SESSION["username"];

// Fetch system statistics
$total_employee = 0;
$total_HRManagers = 0;
$total_Cashier = 0;
$total_FinanceManagers = 0;
$total_cashier = 0;
$total_admin = 0;

// Query to fetch total employee
$sql = "SELECT COUNT(*) AS total_employee FROM employee  WHERE role = '$assigned_for'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_employee = $result->fetch_assoc()["total_employee"];
} else {
    echo "Error fetching total employee: " . $conn->error;
}

// Query to fetch total employees with 'HR Managers' role
$sql = "SELECT COUNT(*) AS total_HRManagers FROM employee WHERE role = 'HR Managers' AND assigned_for = '$assigned_for'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_HRManagers = $result->fetch_assoc()["total_HRManagers"];
} else {
    echo "Error fetching total HR Managers: " . $conn->error;
}

// Query to fetch total employees with 'Cashier' role
$sql = "SELECT COUNT(*) AS total_Cashier FROM employee WHERE role = 'Cashier' AND assigned_for = '$assigned_for'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_Cashier = $result->fetch_assoc()["total_Cashier"];
} else {
    echo "Error fetching total Cashiers: " . $conn->error;
}

// Query to fetch total employees with 'Finance Managers' role
$sql = "SELECT COUNT(*) AS total_FinanceManagers FROM employee WHERE role = 'Finance Managers' AND assigned_for = '$assigned_for'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_FinanceManagers = $result->fetch_assoc()["total_FinanceManagers"];
} else {
    echo "Error fetching total Finance Managers: " . $conn->error;
}

$assigned_for = $_SESSION["assigned_for"];

// Query to fetch total employees with 'Cashier' role
$sql = "SELECT COUNT(*) AS total_cashier 
        FROM employee 
        WHERE role = 'Cashier' AND assigned_for = '$assigned_for'";
$result = $conn->query($sql);
if ($result !== false && $result->num_rows > 0) {
    $total_cashier = $result->fetch_assoc()["total_cashier"];
} else {
    echo "Error fetching total Cashiers: " . $conn->error;
}

// Query to fetch total employees with 'Cashier' role
$sql = "SELECT COUNT(*) AS total_admin FROM employee WHERE role = 'Cashier'";
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
    <title>Cashier Dashboard - Online Dormitory Placement System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Cashier Dashboard</h2>
        <h4 class="text-center mb-4">Managing employees assigned for: <?php echo htmlspecialchars($assigned_for); ?></h4>
        <div class="row  mb-4">
            <div class="col-auto">
                <h4> Your Cashier Name: <?php echo htmlspecialchars($name); ?></h4>
                <h4>Username: <?php echo htmlspecialchars($username); ?></h4>
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total <?php echo htmlspecialchars($assigned_for); ?></div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_employee; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total HR Managers</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_HRManagers; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total Cashiers</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_Cashier; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Finance Managers</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_FinanceManagers; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Cashiers</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_cashier; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Total HR Requests</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_HRRequests; ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>