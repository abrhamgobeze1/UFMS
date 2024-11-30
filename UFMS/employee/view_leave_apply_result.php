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

// Fetch the leave applications for the logged-in employee
$sql = "SELECT leave_id, reason, status FROM LeaveApplications WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$leave_applications = [];
while ($row = $result->fetch_assoc()) {
    $leave_applications[] = $row;
}
$stmt->close();

// Include header
include_once '../includes/header.php';
?>

<main class="container my-5">
    <h1>View Leave Application Results</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Application ID</th>
                <th>Reason</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leave_applications as $app): ?>
                <tr>
                    <td><?php echo htmlspecialchars($app['leave_id']); ?></td>
                    <td><?php echo htmlspecialchars($app['reason']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $app['status'] == 'Approved' ? 'success' : ($app['status'] == 'Disaproved' ? 'danger' : 'warning'); ?>">
                            <?php echo htmlspecialchars($app['status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>