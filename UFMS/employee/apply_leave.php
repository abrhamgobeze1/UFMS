<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as an employee, if not redirect to login page
if (!isset($_SESSION["user_type"]) || ($_SESSION["user_type"] !== "Teacher" && $_SESSION["user_type"] !== "Cleaner" && $_SESSION["user_type"] !== "Dormitory Proctors" && $_SESSION["user_type"] !== "Libraries" && $_SESSION["user_type"] !== "Lab Assistants" && $_SESSION["user_type"] !== "Security" && $_SESSION["user_type"] !== "Cafe Workers")) {
    header("Location: ../login.php");
    exit;
}

// Get the employee ID from the session
$employee_id = $_SESSION["user_id"];

// Handle leave application form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $reason = $_POST["reason"];

    // Validate input
    if (empty($reason)) {
        $error_message = "Please fill in all the required fields.";
    } else {
        // Insert the leave application into the database
        $sql = "INSERT INTO LeaveApplications (employee_id,  reason) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $employee_id, $reason);

        if ($stmt->execute()) {
            $success_message = "Leave application submitted successfully.";
        } else {
            $error_message = "Error submitting leave application. Please try again.";
        }

        $stmt->close();
    }
}

// Include header
include_once '../includes/header.php';
?>

<main class="container my-5">
    <h1>Apply for Leave</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="reason">Reason:</label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>