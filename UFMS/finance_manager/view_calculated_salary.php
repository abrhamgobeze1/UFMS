<?php
// Start session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as Finance Managers, if not redirect to login page
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "Finance Managers") {
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once '../includes/db_connection.php';

// Handle approval action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $salary_id = $_POST['salary_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $update_sql = "UPDATE Salaries SET approval = 'Approved' WHERE salary_id = ?";
    } elseif ($action === 'reject') {
        $update_sql = "UPDATE Salaries SET approval = 'Pending' WHERE salary_id = ?";
    }

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $salary_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all calculated salaries
$salary_sql = "SELECT s.salary_id, e.name, e.role, s.base_salary, s.deductions, s.bonuses, s.net_salary, s.calculated_date, s.payment, s.approval, s.pay_date 
                FROM Salaries s
                JOIN employee e ON s.employee_id = e.employee_id
                ORDER BY s.calculated_date DESC";
$stmt = $conn->prepare($salary_sql);
$stmt->execute();
$salary_result = $stmt->get_result();
$salaries = [];
while ($row = $salary_result->fetch_assoc()) {
    $salaries[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Calculated Salaries</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<?php include '../includes/header.php';?>

    <div class="container-fluid mt-5">
        <h1>View Calculated Salaries</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Base Salary</th>
                    <th>Deductions</th>
                    <th>Bonuses</th>
                    <th>Net Salary</th>
                    <th>Calculated Date</th>
                    <th>Payment</th>
                    <th>Approval</th>
                    <th>Pay Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salaries as $salary) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($salary['name']); ?></td>
                        <td><?php echo htmlspecialchars($salary['role']); ?></td>
                        <td><?php echo number_format($salary['base_salary'], 2); ?></td>
                        <td><?php echo number_format($salary['deductions'], 2); ?></td>
                        <td><?php echo number_format($salary['bonuses'], 2); ?></td>
                        <td><?php echo number_format($salary['net_salary'], 2); ?></td>
                        <td><?php echo $salary['calculated_date']; ?></td>
                        <td><?php echo $salary['payment']; ?></td>
                        <td><?php echo $salary['approval']; ?></td>
                        <td><?php echo $salary['pay_date'] ?? 'Pending'; ?></td>
                        <td>
                            <?php if ($salary['approval'] === 'Pending') { ?>
                                <form action="" method="post">
                                    <input type="hidden" name="salary_id" value="<?php echo $salary['salary_id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-primary btn-sm">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php include '../includes/footer.php';?>

</body>

</html>