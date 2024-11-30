<?php
// Include database connection file
include_once 'includes/db_connection.php';



// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $user_type = $_POST["user_type"];

    // Validate user type
    if ($user_type === "admin") {
        $sql = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to admin dashboard
                session_start();
                $_SESSION["user_id"] = $row["admin_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "admin";
                header("Location: admin/admin_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    }    // Validate user type
    elseif ($user_type === "employee") {
        $sql = "SELECT * FROM employee WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to admin dashboard
                session_start();
                $_SESSION["user_id"] = $row["employee_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "employee";
                header("Location: employee/system_admin_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } elseif ($user_type === "HRManagers") {
        $sql = "SELECT * FROM HRManagers WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Start session and set session variables
                session_start();
                $_SESSION["user_id"] = $row["hr_manager_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "HRManagers";
                $_SESSION["dormitory_id"] = $row["dormitory_id"]; // Add dormitory_id to session
                // Redirect to HRManagers dashboard
                header("Location: hr-Manager/hr_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    }
    elseif ($user_type === "accountant") {
        $sql = "SELECT * FROM accountant WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables including the block ID and redirect to accountant dashboard
                session_start();
                $_SESSION["user_id"] = $row["accountant_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "accountant";
                $_SESSION["block_id"] = $row["block_id"]; // Storing block ID in the session
                header("Location: accountant/accountant_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    }
     elseif ($user_type === "FinanceManagers") {
        $sql = "SELECT * FROM FinanceManagers WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to FinanceManagers dashboard
                session_start();
                $_SESSION["user_id"] = $row["finance_manager_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "FinanceManagers";
                header("Location: FinanceManagers/finance_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } 
    elseif ($user_type === "cashier") {
        $sql = "SELECT * FROM cashier WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to FinanceManagers dashboard
                session_start();
                $_SESSION["user_id"] = $row["cashier_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "cashier";
                header("Location: cashier/cashier_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid user type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - City   ID Card Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php } ?>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="user_type">User Type</label>
                                <select class="form-control" id="user_type" name="user_type" required>
                                    <option value="">Select User Type</option>
                                    <option value="admin">Admin</option>
                                    <option value="employee">Employee</option>
                                    <option value="HRManagers">HRManagers</option>
                                    <option value="accountant">Accountant</option>
                                    <option value="FinanceManagers">FinanceManagers</option>
                                    <option value="cashier">Cashier</option>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>