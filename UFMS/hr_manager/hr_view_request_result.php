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

// Handle request deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_request"])) {
    $hr_request_id = $_POST["hr_request_id"];

    // Delete the HR request from the database
    $sql = "DELETE FROM HRRequests WHERE hr_request_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hr_request_id);

    if ($stmt->execute()) {
        // Redirect to the hr_view_request_result.php page with a success message
        header("Location: hr_view_request_result.php?message=Request_deleted_successfully");
        exit;
    } else {
        // Redirect to the hr_view_request_result.php page with an error message
        header("Location: hr_view_request_result.php?message=Error_deleting_request");
        exit;
    }

    $stmt->close();
}
include '../includes/header.php'; 

// Fetch the HR manager's user ID from the session
$user_id = $_SESSION["user_id"];

// Fetch the HR requests made by the HR manager
$sql = "SELECT * FROM HRRequests WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display the HR requests in a table
    echo "<h2 class='text-center mb-4'>Your HR Requests</h2>";
    echo "<table class='table table-striped'>";
    echo "<thead><tr><th>Request ID</th><th>Assigned For</th><th>Request Date</th><th>Status</th><th>Action</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        $hr_request_id = $row["hr_request_id"];
        $assigned_for = $row["assigned_for"];
        $request_date = $row["request_date"];
        $status = $row["status"];

        echo "<tr>";
        echo "<td>" . htmlspecialchars($hr_request_id) . "</td>";
        echo "<td>" . htmlspecialchars($assigned_for) . "</td>";
        echo "<td>" . htmlspecialchars($request_date) . "</td>";
        echo "<td>" . htmlspecialchars($status) . "</td>";
        echo "<td>
                <form method='POST' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                    <input type='hidden' name='hr_request_id' value='" . htmlspecialchars($hr_request_id) . "'>
                    <button type='submit' name='delete_request' class='btn btn-danger btn-sm'>Delete</button>
                </form>
             </td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<h2 class='text-center mb-4'>You have no HR requests.</h2>";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your HR Requests - Online Dormitory Placement System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <?php
        // Display the HR requests
        if (isset($_GET["message"])) {
            $message_type = $_GET["message"] === "Request_deleted_successfully" ? "success" : "danger";
            echo "<div class='alert alert-$message_type' role='alert'>";
            echo $_GET["message"] === "Request_deleted_successfully" ? "Request deleted successfully!" : "Error deleting request.";
            echo "</div>";
        }
        ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>

</html>