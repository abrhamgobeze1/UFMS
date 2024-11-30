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
    <title>Online Dormitory Placement System</title> <!-- Bootstrap CSS -->
    <link rel="shortcut icon" href="/public_html/images/logo.jpg" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/public_html/CSS/styles.css"> <!-- Font Awesome -->
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">


    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f5f5f5;
        }


        .navbar {
            background-color: #007bff !important;
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
                <a class="navbar-brand" href="/public_html/index.php">
                    <img src="/public_html/images/logo.jpg" alt="UEE1 Logo" class="img-fluid" style="max-height: 50px;">
                    Online Dormitory Placement System <!-- Bootstrap CSS -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION["user_type"])) { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user"></i> <?php echo $_SESSION["username"]; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <?php if ($_SESSION["user_type"] == "admin") { ?>
                                        <a class="dropdown-item" href="/public_html/admin/admin_dashboard.php">Admin Dashboard</a>
                                        <a class="dropdown-item" href="/public_html/admin/manage_colleges.php">Manage Colleges</a>
                                        <a class="dropdown-item" href="/public_html/admin/manage_departments.php">Manage
                                            Departments</a>
                                        <a class="dropdown-item" href="/public_html/admin/manage_students.php">Manage Students</a>

                                        <a class="dropdown-item" href="/public_html/admin/print_assignments.php">Print All
                                            Assignment</a>

                                        <a class="dropdown-item" href="/public_html/admin/generate_reports.php">Generate Reports</a>

                                        <a class="dropdown-item" href="/public_html/admin/manage_notices.php">Manage Notices</a>
                                        <a class="dropdown-item" href="/public_html/admin/manage_comments.php">Manage Comments</a>
                                    <?php } elseif ($_SESSION["user_type"] == "system_admin") { ?>
                                        <a class="dropdown-item" href="/public_html/system_admin/system_admin_dashboard.php">System
                                            Admin Dashboard</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/manage_dormitories.php">Manage
                                            Dormitories</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/manage_manager.php">Manage Manager</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/manage_blocks.php">Manage Blocks</a>

                                        <a class="dropdown-item" href="/public_html/system_admin/manage_proctor.php">Manage Proctor</a>

                                        <a class="dropdown-item" href="/public_html/system_admin/manage_rooms.php">Manage Rooms</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/manage_beds.php">Manage Beds</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/manage_assign.php">Manage
                                            Assignment</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/print_assignments.php">Print All
                                            Assignment</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/view_assignments.php">View
                                            Assignment</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/allocate_rooms.php">Allocate Rooms</a>

                                        <a class="dropdown-item" href="/public_html/system_admin/generate_reports.php">Generate
                                            Reports</a>

                                        <a class="dropdown-item" href="/public_html/system_admin/manage_notices.php">Manage Notices</a>
                                        <a class="dropdown-item" href="/public_html/system_admin/manage_comments.php">Manage
                                            Comments</a>
                                    <?php } elseif ($_SESSION["user_type"] == "manager") { ?>
                                        <a class="dropdown-item" href="/public_html/manager/manager_dashboard.php"> ManagerDashboard<a>
                                                <a class="dropdown-item" href="/public_html/manager/manage_blocks.php"> Manage
                                                    Blocks</a>
                                                <a class="dropdown-item" href="/public_html/manager/manage_proctor.php"> Manage
                                                    Proctor</a>

                                                <a class="dropdown-item" href="/public_html/manager/manage_rooms.php"> Manage
                                                    Rooms</a>
                                                <a class="dropdown-item" href="/public_html/manager/manage_notices.php">Manage
                                                    Notices<a>
                                                        <a class="dropdown-item" href="/public_html/manager/manage_comments.php">Manage
                                                            Comments<a>
                                                                <!-- Add more manager-specific options -->
                                                            <?php } elseif ($_SESSION["user_type"] == "proctor") { ?>
                                                                <a class="dropdown-item"
                                                                    href="/public_html/proctor/proctor_dashboard.php">Proctor
                                                                    Dashboard</a>
                                                                <a class="dropdown-item"
                                                                    href="/public_html/proctor/manage_rooms.php">Manage Rooms</a>
                                                                <a class="dropdown-item"
                                                                    href="/public_html/proctor/manage_beds.php">Manage Beds</a>
                                                                    <a class="dropdown-item"
                                                                    href="/public_html/proctor/view_student_profiles.php"> View Student
                                                                    Profile</a>   
                                                                     <a class="dropdown-item"
                                                                    href="/public_html/proctor/manage_notices.php">  Manage Notice
                                                                    </a>  
                                                                      <a class="dropdown-item"
                                                                    href="/public_html/proctor/manage_comments.php"> Manage Comments 
                                                                    </a>
                                                                <a class="dropdown-item"
                                                                    href="/public_html/proctor/manage_students.php"> Manage Assigned
                                                                    Student</a>
                                                                <a class="dropdown-item"
                                                                    href="/public_html/proctor/proctor_dashboard.php"> Dashboard</a>



                                                                <!-- Add more proctor-specific options -->
                                                            <?php } elseif ($_SESSION["user_type"] == "student") { ?>
                                                                <a class="dropdown-item"
                                                                    href="/public_html/student/student_dashboard.php">Student
                                                                    Dashboard<a>
                                                                        <a class="dropdown-item"
                                                                            href="/public_html/student/view_notices.php">View
                                                                            Notices</a>



                                                                    <?php } ?>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item"
                                                                        href="/public_html/logout.php">Logout</a>
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