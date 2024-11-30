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
// Check if form is submitted for adding, editing, or deleting an employee
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new employee
    if (isset($_POST['add_employee'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $role = $_POST['role'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO employee (name, phone, role,assigned_for, hire_date, salary, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $name, $phone, $role, $assigned_for, $hire_date, $salary, $username, $password);
        $stmt->execute();
        $stmt->close();
    }

    // Edit existing employee
    if (isset($_POST['edit_employee'])) {
        $employee_id = $_POST['employee_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $role = $_POST['role'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $username = $_POST['username'];

        $sql = "UPDATE employee SET name = ?, phone = ?, role = ?, hire_date = ?, salary = ?, username = ? WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $phone, $role, $assigned_for, $hire_date, $salary, $username, $employee_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_employee'])) {
        // Delete existing employee
        $employee_id = $_POST['employee_id'];
        $sql = "DELETE FROM employee WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch employees
$sql = "SELECT employee_id, name, phone, role, assigned_for, hire_date, salary, username 
       FROM employee 
       WHERE assigned_for = '$assigned_for' 
         AND role <> 'HR Managers'";
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
            <h2 class="mb-0">Manage Employees</h2>
        </div>
        <div class="card-body">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addEmployeeModal">
                Add Employee
            </button>

            <!-- Modal for adding Employee -->
            <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addEmployeeModalLabel">Add New Employee</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">Employee Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="Cashier">Cashier</option>
                                        <option value="Cashier">Cashier</option>
                                        <option value="Accountant">Accountant</option>
                                        <option value="HR Managers">HR Managers</option>
                                    </select>
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
                                <button type="submit" class="btn btn-primary" name="add_employee">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display employees -->
            <div class="row">
                <?php foreach ($employees as $employee) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">

                                <h5 class="card-title"><?php echo $employee['name']; ?></h5>
                                <p class="card-text">Role: <?php echo $employee['role']; ?></p>
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

                    <!-- Modal for editing employee -->
                    <div class="modal fade" id="editEmployeeModal<?php echo $employee['employee_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editEmployeeModalLabel<?php echo $employee['employee_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editEmployeeModalLabel<?php echo $employee['employee_id']; ?>">Edit Employee</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="name">Employee Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $employee['name']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $employee['phone']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="role">Role</label>
                                            <select class="form-control" id="role" name="role" required>

                                                <option value="Cashier" <?php if ($employee['role'] == "Cashier") echo 'selected'; ?>>Cashier</option>
                                                <option value="Cashier" <?php if ($employee['role'] == "Cashier") echo 'selected'; ?>>Cashier</option>
                                                <option value="Accountant" <?php if ($employee['role'] == "Accountant") echo 'selected'; ?>>Accountant</option>
                                                <option value="HR Managers" <?php if ($employee['role'] == "HR Managers") echo 'selected'; ?>>HR Managers</option>
                                            </select>
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
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_employee">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for deleting employee -->
                    <div class="modal fade" id="deleteEmployeeModal<?php echo $employee['employee_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteEmployeeModalLabel<?php echo $employee['employee_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteEmployeeModalLabel<?php echo $employee['employee_id']; ?>">Delete Employee</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this employee?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-danger" name="delete_employee">Delete</button>
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