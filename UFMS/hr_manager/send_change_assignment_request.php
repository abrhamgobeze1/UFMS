<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as HR Manager, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "HR Managers") {
    header("Location: ../login.php");
    exit;
}

// Get the employee ID from the session
$employee_id = $_SESSION["user_id"];

// Get the form data
$assigned_for = $_POST["assigned_for"];
$details = $_POST["details"];

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO HRRequests (employee_id, assigned_for, request_date, details) VALUES (?, ?, CURRENT_DATE(), ?)");
$stmt->bind_param("iss", $employee_id, $assigned_for, $details);

// Execute the SQL statement
if ($stmt->execute()) {
    // Redirect to the HR dashboard with a success message
    header("Location: hr_dashboard.php?message=Request_sent_successfully");
    exit;
} else {
    // Redirect to the HR dashboard with an error message
    header("Location: hr_dashboard.php?message=Error_sending_request");
    exit;
}

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();
?>