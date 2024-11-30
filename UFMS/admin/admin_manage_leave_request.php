<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as admin, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "Admin") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Handle leave application management
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Accept request
    if (isset($_POST['accept_request'])) {
        $leave_id = $_POST['leave_id'];

        $sql = "UPDATE LeaveApplications SET status = 'Approved' WHERE leave_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $leave_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['reject_request'])) {
        // Reject request
        $leave_id = $_POST['leave_id'];

        $sql = "UPDATE LeaveApplications SET status = 'Disaproved' WHERE leave_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $leave_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all leave applications
$sql = "SELECT la.leave_id, e.name, la.reason, la.status 
        FROM LeaveApplications la
        JOIN employee e ON la.employee_id = e.employee_id";
$result = $conn->query($sql);
$leave_applications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $leave_applications[] = $row;
    }
}

// Include header
include_once '../includes/header.php';
?>

<main class="container my-5">
    <h1>Manage Leave Applications</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Application ID</th>
                <th>Employee Name</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leave_applications as $application): ?>
                <tr>
                    <td><?php echo $application['leave_id']; ?></td>
                    <td><?php echo $application['name']; ?></td>
                    <td><?php echo $application['reason']; ?></td>
                    <td>
                        <span class="badge badge-<?php echo $application['status'] == 'Approved' ? 'success' : ($application['status'] == 'Disaproved' ? 'danger' : 'warning'); ?>">
                            <?php echo $application['status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($application['status'] == 'Pending'): ?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="leave_id" value="<?php echo $application['leave_id']; ?>">
                                <button type="submit" class="btn btn-success btn-sm" name="accept_request">Accept</button>
                                <button type="submit" class="btn btn-danger btn-sm" name="reject_request">Reject</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
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