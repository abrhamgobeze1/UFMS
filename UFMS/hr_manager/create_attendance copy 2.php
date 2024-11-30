<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as HR Manager, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "HR Managers") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Fetch HR manager's assigned_for value from the session
$assigned_for = $_SESSION["assigned_for"];

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if form is submitted for creating attendance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_attendance'])) {
    $date = sanitize_input($_POST['date']);
    
    // Fetch all employees
    $employee_sql = "SELECT employee_id FROM employee WHERE role = ?";
    $stmt = $conn->prepare($employee_sql);
    $stmt->bind_param("s", $assigned_for);
    $stmt->execute();
    $employee_result = $stmt->get_result();
    $employees = [];
    while ($row = $employee_result->fetch_assoc()) {
        $employees[] = $row['employee_id'];
    }
    $stmt->close();

    // Insert attendance records for each employee
    $sql = "INSERT INTO attendance (employee_id, date, status) VALUES (?, ?, 'UnMarked')";
    $stmt = $conn->prepare($sql);
    foreach ($employees as $employee_id) {
        // Check if attendance record already exists for the given date and employee
        $check_sql = "SELECT * FROM attendance WHERE employee_id = ? AND date = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $employee_id, $date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            $stmt->bind_param("is", $employee_id, $date);
            $stmt->execute();
        }
        $check_stmt->close();
    }
    $stmt->close();

    $success_message = "Attendance records created for date: $date.";
}

// Fetch employees
$employee_sql = "SELECT employee_id, name FROM employee WHERE role = ?";
$stmt = $conn->prepare($employee_sql);
$stmt->bind_param("s", $assigned_for);
$stmt->execute();
$employee_result = $stmt->get_result();
$employees = [];
while ($row = $employee_result->fetch_assoc()) {
    $employees[] = $row;
}
$stmt->close();

// Fetch attendance records
$attendance_sql = "SELECT a.attendance_id, e.name, a.date, a.status 
                   FROM attendance a 
                   JOIN employee e ON a.employee_id = e.employee_id 
                   WHERE e.role = ?";
$stmt = $conn->prepare($attendance_sql);
$stmt->bind_param("s", $assigned_for);
$stmt->execute();
$attendance_result = $stmt->get_result();
$attendance_records = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_records[] = $row;
}
$stmt->close();

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage Attendance: <span class="bg-info"><?php echo htmlspecialchars($assigned_for); ?></span></h2>
        </div>
        <div class="card-body">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <!-- Form to create blank attendance -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-4">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <button type="submit" class="btn btn-primary" name="create_attendance">Create Attendance</button>
            </form>

            <!-- Display attendance records -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['name']); ?></td>
                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                            <td><?php echo htmlspecialchars($record['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>
