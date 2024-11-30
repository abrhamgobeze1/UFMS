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

// Get the HR manager's employee ID from the session
$employee_id = $_SESSION["user_id"];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the form data
    $assigned_for = $_POST["assigned_for"];
    $details = $_POST["details"];

    // Check if the HR manager has a pending request
    $sql = "SELECT * FROM HRRequests WHERE employee_id = ? AND status = 'Pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the HR manager has a pending request, redirect with an error message
        header("Location: send_request.php?message=Pending_request_exists");
        exit;
    } else {
        // Prepare the SQL statement to insert the new request
        $stmt = $conn->prepare("INSERT INTO HRRequests (employee_id, assigned_for, request_date, details) VALUES (?, ?, CURRENT_DATE(), ?)");
        $stmt->bind_param("iss", $employee_id, $assigned_for, $details);

        // Execute the SQL statement
        if ($stmt->execute()) {
            // Redirect to the HR dashboard with a success message
            header("Location: hr_view_request_result.php?message=Request_sent_successfully");
            exit;
        } else {
            // Redirect to the HR dashboard with an error message
            header("Location: hr_view_request_result.php?message=Error_sending_request");
            exit;
        }

        // Close the statement
        $stmt->close();
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Change Assignment Request</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Send Change Assignment Request</h2>
        <?php if (isset($_GET["message"])): ?>
            <div class="alert alert-<?php echo $_GET["message"] === "Request_sent_successfully" ? "success" : "danger"; ?>" role="alert">
                <?php
                    if ($_GET["message"] === "Request_sent_successfully") {
                        echo "Request sent successfully!";
                    } elseif ($_GET["message"] === "Pending_request_exists") {
                        echo "You have a pending request. Please wait for it to be processed before submitting a new request.";
                    } else {
                        echo "Error sending request.";
                    }
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-group">
                <label for="assigned_for">New Assigned Department:</label>
                <select class="form-control" id="assigned_for" name="assigned_for" required>
                    <option value="">Select a department</option>
                    <option value="Teacher">Teacher</option>
                    <option value="Cleaner">Cleaner</option>
                    <option value="Dormitory Proctors">Dormitory Proctors</option>
                    <option value="Libraries">Libraries</option>
                    <option value="Lab Assistants">Lab Assistants</option>
                    <option value="Security">Security</option>
                    <option value="Cafe Workers">Cafe Workers</option>
                    <option value="Cashier">Cashier</option>
                    <option value="Finance Managers">Finance Managers</option>
                    <option value="Accountant">Accountant</option>
                    <option value="HR Managers">HR Managers</option>
                </select>
            </div>
            <div class="form-group">
                <label for="details">Request Details:</label>
                <textarea class="form-control" id="details" name="details" rows="3" required></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Send Request</button>
            </div>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>