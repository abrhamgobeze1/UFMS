<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as an employee, if not redirect to login page
if (!isset($_SESSION["user_type"]) || ($_SESSION["user_type"] !== "Teacher" && $_SESSION["user_type"] !== "Cleaner" && $_SESSION["user_type"] !== "Dormitory Proctors" && $_SESSION["user_type"] !== "Libraries" && $_SESSION["user_type"] !== "Lab Assistants" && $_SESSION["user_type"] !== "Security" && $_SESSION["user_type"] !== "Cafe Workers")) {
    // All user types will have access to this page
   header("Location: ../login.php");
    exit;
}

$employee_id = $_SESSION["user_id"];
$name = $_SESSION["name"];
$username = $_SESSION["username"];
$role = $_SESSION["user_type"];

// Fetch attendance records for the logged-in employee
$sql_attendance = "SELECT date, status FROM attendance WHERE employee_id = ?";
$stmt_attendance = $conn->prepare($sql_attendance);
$stmt_attendance->bind_param("i", $employee_id);
$stmt_attendance->execute();
$result_attendance = $stmt_attendance->get_result();
$attendance_records = [];
while ($row = $result_attendance->fetch_assoc()) {
    $attendance_records[] = $row;
}
$stmt_attendance->close();



?>

<?php include '../includes/header.php'; ?>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Employee Dashboard</h2>
        <div class="row mb-4">
            <div class="col-auto">
                <h4>Your Employee Name: <?php echo htmlspecialchars($name); ?></h4>
                <h4>Username: <?php echo htmlspecialchars($username); ?></h4>
                <h4>Role: <?php echo htmlspecialchars($role); ?></h4>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <h4>Attendance Records</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                <td><?php echo htmlspecialchars($record['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
  
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
