<?php
// Start session if it's not already started
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

// Handle form submission to mark attendance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_attendance'])) {
    $date = $_POST['date'];
    $employee_id = $_POST['employee_id'];
    $status = $_POST['status'];
    $created = 'Created';

    // Check if an attendance record already exists for this employee and date
    $sql_check = "SELECT * FROM Attendance WHERE employee_id = ? AND date = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("is", $employee_id, $date);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Update the existing record
        $sql_update = "UPDATE Attendance SET status = ?, created = ? WHERE employee_id = ? AND date = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssis", $status, $created, $employee_id, $date);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Insert a new record
        $sql_insert = "INSERT INTO Attendance (employee_id, date, status, created) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isss", $employee_id, $date, $status, $created);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    $stmt_check->close();
}

// Fetch all employees
$sql_employees = "SELECT employee_id, name FROM Employee";
$result_employees = $conn->query($sql_employees);
$employees = [];
if ($result_employees->num_rows > 0) {
    while ($row = $result_employees->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Mark Attendance</h2>
        </div>
        <div class="card-body">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="employee_id">Employee</label>
                    <select class="form-control" id="employee_id" name="employee_id" required>
                        <?php foreach ($employees as $employee) : ?>
                            <option value="<?php echo $employee['employee_id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="mark_attendance">Mark Attendance</button>
            </form>
        </div>
    </section>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>