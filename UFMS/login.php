<?php
// Include database connection file
include_once 'includes/db_connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Query to fetch user based on user type
    $sql = "SELECT * FROM employee WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            // Set session variables and redirect based on user type
            session_start();
            $_SESSION["user_id"] = $row["employee_id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["user_type"] = $row["role"];
            $_SESSION["assigned_for"] = $row["assigned_for"];
            $_SESSION["name"] = $row["name"];
          


            // Redirect based on user type
            switch ($row["role"]) {
                case "Admin":
                    header("Location: admin/admin_dashboard.php");
                    break;
                case "HR Managers":
                    header("Location: hr_manager/hr_dashboard.php");
                    break;
                case "Accountant":
                    header("Location: accountant/accountant_dashboard.php");
                    break;
                case "Finance Managers":
                    header("Location: finance_manager/finance_dashboard.php");
                    break;
                    case "Cashier":
                        header("Location: cashier/cashier_dashboard.php");
                        break;
                default:
                    header("Location: employee/employee_dashboard.php");
                    break;
            }
            exit;
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - City ID Card Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
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