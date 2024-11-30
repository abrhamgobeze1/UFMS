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

// Check if form is submitted for adding or editing a collage
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new employee
    if (isset($_POST['add_collage'])) {
        $username = $_POST['username'];
        $position = $_POST['position'];
        $sql = "INSERT INTO employee (username, position) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $position);
        $stmt->execute();
        $stmt->close();
    }

    // Edit existing employee
    if (isset($_POST['edit_collage'])) {
        $username = $_POST['username'];
        $position = $_POST['position'];
        $employee_id = $_POST['employee_id'];
        $sql = "UPDATE employee SET username = ?, position = ? WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $position, $employee_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_collage'])) {
        // Delete existing collage
        $employee_id = $_POST['employee_id'];
        $sql = "DELETE FROM employee WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch employee
$sql = "SELECT employee_id, username,position FROM employee";
$result = $conn->query($sql);
$employee = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employee[] = $row;
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
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addCollageModal">
                Add Employee
            </button>

            <!-- Modal for adding Employee -->
            <div class="modal fade" id="addCollageModal" tabindex="-1" role="dialog"
                aria-labelledby="addCollageModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCollageModalLabel">Add New Employee</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="username">Employee Name</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="position">Employee position</label>
                                    <select class="form-control" id="position" name="position" required>
                                        <option value="Natural">Natural</option>
                                        <option value="Social">Social</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="add_collage">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display employee -->
            <div class="row">
                <?php foreach ($employee as $employee): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                            <h5 class="card-title"><?php echo $employee['username']; ?></h5>
                            <h3 class="card-title"><?php echo $employee['position']; ?></h5>
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editCollageModal<?php echo $employee['employee_id']; ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteCollageModal<?php echo $employee['employee_id']; ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for editing employee -->
                    <div class="modal fade" id="editCollageModal<?php echo $employee['employee_id']; ?>" tabindex="-1"
                        role="dialog" aria-labelledby="editCollageModalLabel<?php echo $employee['employee_id']; ?>"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="editCollageModalLabel<?php echo $employee['employee_id']; ?>">Edit Employee
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="username">Employee Name</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="<?php echo $employee['username']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="position">Employee position</label>
                                            <select class="form-control" id="position" name="position" required>
                                                <option value="Natural">Natural</option>
                                                <option value="Social">Social</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="employee_id"
                                            value="<?php echo $employee['employee_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="edit_collage">Save
                                            changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for deleting employee -->
                    <div class="modal fade" id="deleteCollageModal<?php echo $employee['employee_id']; ?>" tabindex="-1"
                        role="dialog" aria-labelledby="deleteCollageModalLabel<?php echo $employee['employee_id']; ?>"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="deleteCollageModalLabel<?php echo $employee['employee_id']; ?>">Delete Employee
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete the employee
                                        "<?php echo $employee['username']; ?>"?
                                        <input type="hidden" name="employee_id"
                                            value="<?php echo $employee['employee_id']; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger" name="delete_collage">Delete</button>
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