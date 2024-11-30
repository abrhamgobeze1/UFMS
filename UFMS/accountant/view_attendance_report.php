<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as Accountant, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "Accountant") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Fetch HR manager's assigned_for value from the session
$assigned_for = $_SESSION["assigned_for"];

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

// Fetch attendance records if an employee is selected
$selected_employee_id = null;
$attendance_records = [];
if (isset($_GET['employee_id'])) {
    $selected_employee_id = intval($_GET['employee_id']);
    $current_month = date('Y-m');
    
    $attendance_sql = "SELECT date, created, status FROM attendance WHERE employee_id = ? AND DATE_FORMAT(date, '%Y-%m') = ? AND created = 'Created' ORDER BY date ASC";
    $stmt = $conn->prepare($attendance_sql);
    $stmt->bind_param("is", $selected_employee_id, $current_month);
    $stmt->execute();
    $attendance_result = $stmt->get_result();
    while ($row = $attendance_result->fetch_assoc()) {
        $attendance_records[$row['date']] = array(
            'created' => $row['created'],
            'status' => $row['status']
        );
    }
    $stmt->close();
}

// Pagination
$records_per_page = 30;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$total_records = count($attendance_records);
$total_pages = ceil($total_records / $records_per_page);
$start_index = ($current_page - 1) * $records_per_page;
$end_index = $start_index + $records_per_page;
$paginated_attendance_records = array_slice($attendance_records, $start_index, $records_per_page, true);

// Include header
include_once '../includes/header.php';
?>

<main class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <h4>Employees</h4>
                <?php foreach ($employees as $employee): ?>
                    <a href="?employee_id=<?php echo $employee['employee_id']; ?>" class="list-group-item list-group-item-action <?php echo ($selected_employee_id == $employee['employee_id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($employee['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-9">
            <section class="card">
                <div class="card-header">
                    <h2 class="mb-0">Attendance Report</h2>
                    <?php if ($selected_employee_id !== null): ?>
                        <button class="btn btn-primary float-right" onclick="printAttendanceReport()">Print Report</button>
                    <?php endif; ?>
                </div>
                <div class="card-body" id="attendance-report">
                    <?php if ($selected_employee_id !== null): ?>
                        <h4>Attendance for: <?php echo htmlspecialchars($employees[array_search($selected_employee_id, array_column($employees, 'employee_id'))]['name']); ?></h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($paginated_attendance_records) > 0): ?>
                                    <?php foreach ($paginated_attendance_records as $date => $record): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($date); ?></td>
                                            <td><?php echo htmlspecialchars($record['created']); ?></td>
                                            <td><?php echo htmlspecialchars($record['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No attendance records found for this employee.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <nav aria-label="Attendance records pagination">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($current_page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?employee_id=<?php echo $selected_employee_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php else: ?>
                        <p>Please select an employee to view their attendance report.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</main>

<?php
// Include footer
include_once '../includes/footer.php';
?>

<script>
function printAttendanceReport() {
    var printContents = document.getElementById('attendance-report').innerHTML;
    var originalContents = document.body.innerHTML;
    
    document.body.innerHTML = printContents;
    
    window.print();
    
    document.body.innerHTML = originalContents;
}
</script>