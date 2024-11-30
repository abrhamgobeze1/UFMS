<?php
// Include database connection file
include_once '../includes/db_connection.php';

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as Finance Managers, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "Finance Managers") {
    header("Location: ../login.php");
    exit;
}


$employee_id = $_SESSION["user_id"];
$current_date = date("Y-m-d");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Check if the selected date is today
    if ($date === $current_date) {
        // Check if the employee has already marked attendance for today
        $sql_check_attendance = "SELECT * FROM attendance WHERE employee_id = ? AND date = ?";
        $stmt_check_attendance = $conn->prepare($sql_check_attendance);
        $stmt_check_attendance->bind_param("is", $employee_id, $date);
        $stmt_check_attendance->execute();
        $result_check_attendance = $stmt_check_attendance->get_result();

        if ($result_check_attendance->num_rows > 0) {
            // If attendance record already exists, update it
            $sql_update_attendance = "UPDATE attendance SET status = ? WHERE employee_id = ? AND date = ?";
            $stmt_update_attendance = $conn->prepare($sql_update_attendance);
            $stmt_update_attendance->bind_param("sis", $status, $employee_id, $date);
            $stmt_update_attendance->execute();
            $stmt_update_attendance->close();
        } else {
            // If attendance record does not exist, insert a new record
            $sql_insert_attendance = "INSERT INTO attendance (employee_id, date, status) VALUES (?, ?, ?)";
            $stmt_insert_attendance = $conn->prepare($sql_insert_attendance);
            $stmt_insert_attendance->bind_param("iss", $employee_id, $date, $status);
            $stmt_insert_attendance->execute();
            $stmt_insert_attendance->close();
        }
    }
}

// Get the current month and year
$current_month = date("m");
$current_year = date("Y");

// Get the total number of days in the current month
$total_days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

// Initialize an array to store attendance data for the current month
$attendance_month = [];

// Loop through each day of the current month
for ($day = 1; $day <= $total_days_in_month; $day++) {
    // Format the date in 'YYYY-MM-DD' format
    $date = date("Y-m-d", mktime(0, 0, 0, $current_month, $day, $current_year));

    // Check if the date is in the future
    if ($date > $current_date) {
        // If the date is in the future, set the status to 'UnMarked'
        $status = 'UnMarked';
    } else {
        // If the date is today or in the past, fetch the attendance status from the database
        $sql_attendance = "SELECT status FROM attendance WHERE employee_id = ? AND date = ?";
        $stmt_attendance = $conn->prepare($sql_attendance);
        $stmt_attendance->bind_param("is", $employee_id, $date);
        $stmt_attendance->execute();
        $result_attendance = $stmt_attendance->get_result();
        $attendance_data = $result_attendance->fetch_assoc();
        $status = $attendance_data ? $attendance_data['status'] : 'UnMarked';
    }

    // Add the attendance data to the array
    $attendance_month[$day] = $status;
}

$stmt_attendance->close();
?>

<?php include '../includes/header.php'; ?>
<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">Attendance for <?php echo date("F Y"); ?></h2>
        <div class="row mb-4">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_month as $day => $status): ?>
                            <tr>
                                <td><?php echo date("Y-m-d", mktime(0, 0, 0, $current_month, $day, $current_year)); ?></td>
                                <td>
                                    <?php if ($day == date("j")): ?>
                                        <!-- Display form to change status for today -->
                                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <input type="hidden" name="date" value="<?php echo date("Y-m-d", mktime(0, 0, 0, $current_month, $day, $current_year)); ?>">
                                            <select class="form-control" name="status">
                                                <option value="Present" <?php echo ($status == 'Present') ? 'selected' : ''; ?>>Present</option>
                                                <option value="Absent" <?php echo ($status == 'Absent') ? 'selected' : ''; ?>>Absent</option>
                                                <option value="UnMarked" <?php echo ($status == 'UnMarked') ? 'selected' : ''; ?>>UnMarked</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm mt-1">Save</button>
                                        </form>
                                    <?php else: ?>
                                        <?php echo $status; ?>
                                    <?php endif; ?>
                                </td>
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


