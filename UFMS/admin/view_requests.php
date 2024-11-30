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

// Handle request management
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Accept request
    if (isset($_POST['accept_request'])) {
        $hr_request_id = $_POST['hr_request_id'];
        $new_assigned_for = $_POST['new_assigned_for'];

        $sql = "UPDATE HRRequests SET status = 'Approved', assigned_for = ? WHERE hr_request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_assigned_for, $hr_request_id);

        if ($stmt->execute()) {
            // Update the employee's assigned_for value
            $sql = "UPDATE employee SET assigned_for = ? WHERE employee_id = (SELECT employee_id FROM HRRequests WHERE hr_request_id = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_assigned_for, $hr_request_id);
            $stmt->execute();
        }

        $stmt->close();
    } elseif (isset($_POST['reject_request'])) {
        // Reject request
        $hr_request_id = $_POST['hr_request_id'];

        $sql = "UPDATE HRRequests SET status = 'Disaproved' WHERE hr_request_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $hr_request_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all HR requests
$sql = "SELECT hr.hr_request_id, hr.employee_id, e.name, hr.assigned_for, hr.request_date, hr.details, hr.status 
        FROM HRRequests hr
        JOIN employee e ON hr.employee_id = e.employee_id";
$result = $conn->query($sql);
$hr_requests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hr_requests[] = $row;
    }
}

// Include header
include_once '../includes/header.php';
?>

<main class="container my-5">
    <h1>Manage HR Requests</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Employee Name</th>
                <th>Assigned For</th>
                <th>Request Date</th>
                <th>Details</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hr_requests as $request): ?>
                <tr>
                    <td><?php echo $request['hr_request_id']; ?></td>
                    <td><?php echo $request['name']; ?></td>
                    <td><?php echo $request['assigned_for']; ?></td>
                    <td><?php echo $request['request_date']; ?></td>
                    <td><?php echo $request['details']; ?></td>
                    <td><?php echo $request['status']; ?></td>
                    <td>
                        <?php if ($request['status'] == 'Pending'): ?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="hr_request_id" value="<?php echo $request['hr_request_id']; ?>">
                                <input type="hidden" name="new_assigned_for" value="<?php echo $request['assigned_for']; ?>">
                                <button type="submit" class="btn btn-success btn-sm" name="accept_request">Accept</button>
                                <button type="submit" class="btn btn-danger btn-sm" name="reject_request">Reject</button>
                            </form>
                        <?php else: ?>
                            <span class="badge badge-<?php echo $request['status'] == 'Approved' ? 'success' : 'danger'; ?>">
                                <?php echo $request['status']; ?>
                            </span>
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