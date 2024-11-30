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

// Check if form is submitted for adding, editing, or deleting an employee
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new employee
    if (isset($_POST['add_employee'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $position = $_POST['position'];
        $level = $_POST['level'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO employee (name, phone, position, level, hire_date, salary, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdss", $name, $phone, $position, $level, $hire_date, $salary, $username, $password);
        $stmt->execute();
        $stmt->close();
    }

    // Edit existing employee
    if (isset($_POST['edit_employee'])) {
        $employee_id = $_POST['employee_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $position = $_POST['position'];
        $level = $_POST['level'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $username = $_POST['username'];

        $sql = "UPDATE employee SET name = ?, phone = ?, position = ?, level = ?, hire_date = ?, salary = ?, username = ? WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdsi", $name, $phone, $position, $level, $hire_date, $salary, $username, $employee_id);
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
$sql = "SELECT employee_id, name, phone, position, level, hire_date, salary, username FROM employee";
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
                                    <label for="position">Position</label>
                                    <select class="form-control" id="position" name="position" required>
                                        <option value="Teacher">Teacher</option>
                                        <option value="Cleaner">Cleaner</option>
                                        <option value="Dormitory Proctors">Dormitory Proctors</option>
                                        <option value="Libraries">Libraries</option>
                                        <option value="Lab Assistants">Lab Assistants</option>
                                        <option value="Security">Security</option>
                                        <option value="Cafe Workers">Cafe Workers</option>
                                        <option value="Cashier">Cashier</option>
                                        <option value="Finance Managers">Finance Managers</option>
                                        <option value="Accountant">Accountant</option>
                                        <option value="Admin">Admin</option>
                                        <option value="HR Managers">HR Managers</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control" id="level" name="level" required>
                                        <option value="Entry">Entry</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Senior">Senior</option>
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
                <?php foreach ($employees as $employee): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">

                                <h5 class="card-title"><?php echo $employee['name']; ?></h5>
                                <p class="card-text">Position: <?php echo $employee['position']; ?></p>
                                <p class="card-text">Level: <?php echo $employee['level']; ?></p>
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
                                            <label for="position">Position</label>
                                            <select class="form-control" id="position" name="position" required>
                                                <option value="Teacher" <?php if($employee['position'] == 'Teacher') echo 'selected'; ?>>Teacher</option>
                                                <option value="Cleaner" <?php if($employee['position'] == 'Cleaner') echo 'selected'; ?>>Cleaner</option>
                                                <option value="Dormitory Proctors" <?php if($employee['position'] == 'Dormitory Proctors') echo 'selected'; ?>>Dormitory Proctors</option>
                                                <option value="Libraries" <?php if($employee['position'] == 'Libraries') echo 'selected'; ?>>Libraries</option>
                                                <option value="Lab Assistants" <?php if($employee['position'] == 'Lab Assistants') echo 'selected'; ?>>Lab Assistants</option>
                                                <option value="Security" <?php if($employee['position'] == 'Security') echo 'selected'; ?>>Security</option>
                                                <option value="Cafe Workers" <?php if($employee['position'] == 'Cafe Workers') echo 'selected'; ?>>Cafe Workers</option>
                                                <option value="Cashier" <?php if($employee['position'] == 'Cashier') echo 'selected'; ?>>Cashier</option>
                                                <option value="Finance Managers" <?php if($employee['position'] == 'Finance Managers') echo 'selected'; ?>>Finance Managers</option>
                                                <option value="Accountant" <?php if($employee['position'] == 'Accountant') echo 'selected'; ?>>Accountant</option>
                                                <option value="Admin" <?php if($employee['position'] == 'Admin') echo 'selected'; ?>>Admin</option>
                                                <option value="HR Managers" <?php if($employee['position'] == 'HR Managers') echo 'selected'; ?>>HR Managers</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="level">Level</label>
                                            <select class="form-control" id="level" name="level" required>
                                                <option value="Entry" <?php if($employee['level'] == 'Entry') echo 'selected'; ?>>Entry</option>
                                                <option value="Intermediate" <?php if($employee['level'] == 'Intermediate') echo 'selected'; ?>>Intermediate</option>
                                                <option value="Senior" <?php if($employee['level'] == 'Senior') echo 'selected'; ?>>Senior</option>
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
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
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
                                        Are you sure you want to delete the employee "<?php echo $employee['name']; ?>"?
                                        <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
