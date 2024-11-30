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
$employee_sql = "SELECT employee_id, name, role, salary FROM employee WHERE role = ?";
$stmt = $conn->prepare($employee_sql);
$stmt->bind_param("s", $assigned_for);
$stmt->execute();
$employee_result = $stmt->get_result();
$employees = [];
while ($row = $employee_result->fetch_assoc()) {
    $employees[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Net Salary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<?php include '../includes/header.php';?>

    <div class="container-fluid mt-5">
        <h1>Calculate Net Salary</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Base Salary</th>
                    <th>Total Created Attendance</th>
                    <th>Present</th>
                    <th>Uncreated Present</th>
                    <th>Total Present Attendance</th>
                    <th>Deductions</th>
                    <th>Bonuses</th>
                    <th>Net Salary</th>
                    <th>Calculated Date</th>
                    <th>Payment</th>
                    <th>Approval</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Calculate net salary for each employee
                foreach ($employees as $employee) {
                    $employee_id = $employee['employee_id'];
                    $base_salary = $employee['salary'];

                    // Fetch attendance records for the current month
                    $current_month = date('Y-m');
                    $attendance_sql = "SELECT date, status, created FROM attendance WHERE employee_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?";
                    $stmt = $conn->prepare($attendance_sql);
                    $stmt->bind_param("is", $employee_id, $current_month);
                    $stmt->execute();
                    $attendance_result = $stmt->get_result();
                    $total_present = 0;
                    $total_created = 0;
                    $total_non_created_present = 0;
                    while ($row = $attendance_result->fetch_assoc()) {
                        if ($row['created'] == 'Created') {
                            $total_created++;
                            if ($row['status'] == 'Present') {
                                $total_present++;
                            }
                        } elseif ($row['status'] == 'Present') {
                            $total_non_created_present++;
                        }
                    }
                    $stmt->close();

                    // Calculate deductions and bonuses
                    $deductions = 0;
                    $bonuses = 0;
                    if ($total_created > 0) {
                        $attendance_percentage = ($total_present / $total_created) * 100;
                        if ($attendance_percentage < 100) {
                            $deductions = $base_salary * (1 - $attendance_percentage / 100);
                        }
                    } else {
                        $deductions = $base_salary; // Deduct full base salary if no attendance is created
                    }

                    // Calculate bonus for non-created attendance
                    if ($total_non_created_present > 0) {
                        $bonuses = $base_salary * 0.05; // 5% bonus for non-created attendance presents
                    }

                    $net_salary = $base_salary - $deductions + $bonuses;

                    // Insert or update salary record
                    $salary_sql = "SELECT salary_id, calculated_date ,payment ,approval FROM Salaries WHERE employee_id = ? AND DATE_FORMAT(calculated_date, '%Y-%m') = ?";
                    $stmt = $conn->prepare($salary_sql);
                    $stmt->bind_param("is", $employee_id, $current_month);
                    $stmt->execute();
                    $salary_result = $stmt->get_result();
                    if ($salary_result->num_rows > 0) {
                        $salary_record = $salary_result->fetch_assoc();
                        $salary_id = $salary_record['salary_id'];
                        $calculated_date = $salary_record['calculated_date'];
                        $payment = $salary_record['payment'];
                        $approval = $salary_record['approval'];
                        $update_sql = "UPDATE Salaries SET base_salary = ?, deductions = ?, bonuses = ?, net_salary = ? WHERE salary_id = ?";
                        $stmt = $conn->prepare($update_sql);
                        $stmt->bind_param("ddddi", $base_salary, $deductions, $bonuses, $net_salary, $salary_id);
                        $stmt->execute();
                    } else {
                        $insert_sql = "INSERT INTO Salaries (employee_id, base_salary, deductions, bonuses, net_salary, calculated_date) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($insert_sql);
                        $calculated_date = date('Y-m-d');
                        $stmt->bind_param("idddds", $employee_id, $base_salary, $deductions, $bonuses, $net_salary, $calculated_date);
                        $stmt->execute();
                    }
                    $stmt->close();
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                        <td><?php echo htmlspecialchars($employee['role']); ?></td>
                        <td><?php echo number_format($base_salary, 2); ?></td>
                        <td><?php echo $total_created; ?></td>
                        <td><?php echo $total_present; ?></td>
                        <td><?php echo $total_non_created_present; ?></td>
                        <td><?php echo $total_present + $total_non_created_present; ?></td>
                        <td><?php echo number_format($deductions, 2); ?></td>
                        <td><?php echo number_format($bonuses, 2); ?></td>
                        <td><?php echo number_format($net_salary, 2); ?></td>
                        <td><?php echo $calculated_date; ?></td>
                        <td><?php echo $payment; ?></td>
                        <td><?php echo $approval; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php include '../includes/footer.php';?>

</body>

</html>