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
$assigned_for = $_SESSION["assigned_for"];
// Fetch employees
// Fetch employees
$sql = "SELECT employee_id, name, phone, role, hire_date, salary, username 
        FROM employee 
        WHERE assigned_for = '$assigned_for'
        AND role = 'Cashier'";
$result = $conn->query($sql);
$employees = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
// Check if form is submitted for adding, editing, or deleting an employee
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new employee
    if (isset($_POST['add_finance_manager'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $role = 'Cashier';
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Insert the new cashier into the 'employee' table
        $sql = "INSERT INTO employee (name, phone, role, assigned_for, hire_date, salary, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $name, $phone, $role, $assigned_for, $hire_date, $salary, $username, $password);
        $stmt->execute();
        
        // Fetch the ID of the inserted employee
        $employee_id = $conn->insert_id;
        $stmt->close();

        // Generate the next available account number
        $last_account_number = $conn->query("SELECT MAX(CAST(account_number AS SIGNED)) AS last_account_number FROM accounts")->fetch_assoc()['last_account_number'];
        $next_account_number = str_pad((int) ($last_account_number ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        // Insert the account for the new cashier into the 'accounts' table
        $sql = 'INSERT INTO accounts (employee_id, account_number) VALUES (?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('is', $employee_id, $next_account_number);
        $stmt->execute();
        $stmt->close();
    }


    // Edit existing employee
    if (isset($_POST['edit_finance_manager'])) {
        $employee_id = $_POST['employee_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $username = $_POST['username'];

        $sql = "UPDATE employee SET name = ?, phone = ?, assigned_for = ?, hire_date = ?, salary = ?, username = ? WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $phone, $assigned_for, $hire_date, $salary, $username, $employee_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_finance_manager'])) {
        // Delete existing employee
        $employee_id = $_POST['employee_id'];
        $sql = "DELETE FROM employee WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch HR managers
$sql = "SELECT employee_id, name, phone,role, assigned_for, hire_date, salary, username FROM employee WHERE role = 'Cashier'";
$result = $conn->query($sql);
$employees = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Include header
include_once '../includes/header.php';
?>

<main class="container mt-5">
    <section class="dashboard card">
        <div class="card-header">
            <h2 class="mb-0">Manage CashierAssigned for  :   <span class="bg-info"> <?php echo htmlspecialchars($assigned_for); ?></span>
            </h2>
        </div>
        <div class="card-body">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addEmployeeModal">
                Add Cashier
            </button>

            <!-- Modal for adding Employee -->
            <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addEmployeeModalLabel">Add New Cashier</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">Cashier Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>

                                <div class="form-group">
                                    <label for="hire_date">Hire Date</label>
                                    <input type="date" class="form-control" id="hire_date" name="hire_date">
                                </div>
                                <div class="form-group">
                                    <label for="salary">Salary</label>
                                    <input type="number" step="0.01" class="form-control" id="salary" name="salary">
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_finance_manager">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display HR Managers -->
            <div class="row">
                <?php foreach ($employees as $employee) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $employee['name']; ?></h5>
                                <p class="card-text">Username: <?php echo $employee['username']; ?></p>

                                <p class="card-text">Role: <?php echo $employee['role']; ?></p>
                                <p class="card-text">Assigned For: <?php echo $employee['assigned_for']; ?></p>
                                <p class="card-text">Hire Date: <?php echo date('Y-m-d', strtotime($employee['hire_date'])); ?></p>
                                <p class="card-text">Salary: $<?php echo $employee['salary']; ?></p>

                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editEmployeeModal<?php echo $employee['employee_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteEmployeeModal<?php echo $employee['employee_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for editing Cashier -->
                    <div class="modal fade" id="editEmployeeModal<?php echo $employee['employee_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editEmployeeModalLabel<?php echo $employee['employee_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editEmployeeModalLabel<?php echo $employee['employee_id']; ?>">Edit Cashier</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Cashier Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $employee['name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $employee['phone']; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="hire_date">Hire Date</label>
                                            <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo $employee['hire_date']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="salary">Salary</label>
                                            <input type="number" step="0.01" class="form-control" id="salary" name="salary" value="<?php echo $employee['salary']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $employee['username']; ?>" required>
                                        </div>
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_finance_manager">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal for deleting Cashier -->
                    <div class="modal fade" id="deleteEmployeeModal<?php echo $employee['employee_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteEmployeeModalLabel<?php echo $employee['employee_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteEmployeeModalLabel<?php echo $employee['employee_id']; ?>">Delete Cashier</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete the Cashier "<?php echo $employee['name']; ?>"?
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_finance_manager">Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php
// Include footer
include_once '../includes/footer.php';
?>