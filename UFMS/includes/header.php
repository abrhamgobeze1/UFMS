<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link rel="shortcut icon" href="/ems/images/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="/ems/CSS/styles.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <style>
        body {
            background-color: #f5f5f5;
        }

        .navbar {
            background-color: green !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            color: #fff !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #f5f5f5 !important;
        }

        .navbar-brand img {
            border-radius: 30px;
        }

        .dropdown-menu {
            background-color: #007bff;
            border-color: #007bff;
        }

        .dropdown-item {
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #0056b3;
            color: #f5f5f5;
        }

        .main-content {
            padding: 2rem;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="/ems/index.php">
                    <img src="/ems/images/logo.jpg" alt="Company Logo" class="img-fluid" style="max-height: 50px;">
                    Employee Management System
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION["user_type"])) { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user"></i> <?php echo $_SESSION["username"]; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <?php if ($_SESSION["user_type"] == "Admin") { ?>
                                        <a class="dropdown-item" href="/ems/admin/admin_dashboard.php">Admin Dashboard</a>
                                        <a class="dropdown-item" href="/ems/admin/manage_employees.php">Manage Employees</a>
                                        <a class="dropdown-item" href="/ems/admin/manage_hr_manager.php">Manage HR Manager</a>
                                        <a class="dropdown-item" href="/ems/admin/view_requests.php">Manage HR Manager  Requests</a>
                                        <a class="dropdown-item" href="/ems/admin/admin_manage_leave_request.php">Manage Leave  Requests</a>
                                        <a class="dropdown-item" href="/ems/admin/view_comments.php">View Comments</a>
                                    <?php } elseif ($_SESSION["user_type"]   == "Teacher" || $_SESSION["user_type"]   == "Cleaner" || $_SESSION["user_type"]   == "Dormitory Proctors" || $_SESSION["user_type"]   == "Libraries" || $_SESSION["user_type"]   == "Lab Assistants" || $_SESSION["user_type"]   == "Security" || $_SESSION["user_type"]   == "Cafe Workers") { ?>



                                        <a class="dropdown-item" href="/ems/employee/employee_dashboard.php">Employee Dashboard</a>
                                        <a class="dropdown-item" href="/ems/employee/mark_attendance.php">Mark Attendance</a>
                                        <a class="dropdown-item" href="/ems/employee/employee_details.php">Account Details</a>
                                        <a class="dropdown-item" href="/ems/employee/apply_leave.php">Apply for Leave</a>
                                        <a class="dropdown-item" href="/ems/employee/view_leave_apply_result.php"> View Leave Result</a>
                                    <?php } elseif ($_SESSION["user_type"] == "HR Managers") { ?>
                                        <a class="dropdown-item" href="/ems/hr_manager/hr_dashboard.php">HR Dashboard</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/info.php">HR Info</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/mark_attendance_for_own.php">Mark Atendance For Own</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/mark_attendance_for_employee.php">Mark Atendance For Employee</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/hr_manage_employees.php">Manage Employee</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/hr_manage_finance_manager.php">Manage Finance Manager</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/hr_manage_cashier.php">Manage Cashier</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/hr_manage_accountant.php">Manage Accountant</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/send_request.php">Send Request</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/hr_view_request_result.php">View Request Result</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/create_attendance.php">Create Attendance</a>
                                        <a class="dropdown-item" href="/ems/hr_manager/report_attendance.php">Report Attendance</a>
                                    <?php } elseif ($_SESSION["user_type"] == "Accountant") { ?>
                                        <a class="dropdown-item" href="/ems/accountant/accountant_dashboard.php">Accountant Dashboard</a>
                                        <a class="dropdown-item" href="/ems/accountant/info.php">Accountant Info</a>
                                        <a class="dropdown-item" href="/ems/accountant/mark_attendance.php">Mark Attendance</a>
                                        <a class="dropdown-item" href="/ems/accountant/view_attendance_report.php">View Attendance Report</a>
                                        <a class="dropdown-item" href="/ems/accountant/calculate_net_salary.php">Calculate Net Salary</a>
                                        <a class="dropdown-item" href="/ems/accountant/view_calculated_salary.php">View Calculated Salary</a>
                                        <a class="dropdown-item" href="/ems/accountant/send_calculated_salary.php">Send Calculated Salary</a>
                                    <?php } elseif ($_SESSION["user_type"] == "Finance Managers") { ?>
                                        <a class="dropdown-item" href="/ems/finance_manager/finance_dashboard.php">Finance Dashboard</a>
                                        <a class="dropdown-item" href="/ems/finance_manager/info.php">Finance Info</a>
                                        <a class="dropdown-item" href="/ems/finance_manager/mark_attendance.php">Mark Attendance</a>
                                        <a class="dropdown-item" href="/ems/finance_manager/view_calculated_salary.php">View Calculated Salary</a>
                                        <a class="dropdown-item" href="/ems/finance_manager/approve_salary.php">Approve Salary</a>
                                    <?php } elseif ($_SESSION["user_type"] == "Cashier") { ?>
                                        <a class="dropdown-item" href="/ems/cashier/cashier_dashboard.php">Cashier Dashboard</a>
                                        <a class="dropdown-item" href="/ems/cashier/info.php">Cashier Info</a>
                                        <a class="dropdown-item" href="/ems/cashier/mark_attendance.php">Mark Attendance</a>
                                        <a class="dropdown-item" href="/ems/cashier/pay_salary.php"> Pay Salary</a>
                                        <a class="dropdown-item" href="/ems/cashier/salary_history.php">Salary History</a>
                                        <a class="dropdown-item" href="/ems/cashier/cash_salary_history.php">Cash Salary History</a>
                                        <a class="dropdown-item" href="/ems/cashier/pay_salary_by_cash.php">Pay Salary by Cash</a>
                                    <?php } ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/ems/logout.php">Logout</a>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <!-- Bootstrap JavaScript -->
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/jquery-3.6.0.min.js"></script>

</body>

</html>