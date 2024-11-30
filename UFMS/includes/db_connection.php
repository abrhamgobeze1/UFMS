<?php

// Database connection parameters
$host = "localhost"; // Change to your host name if necessary
$username = "root"; // Change to your database username if necessary
$password = ""; // Change to your database password if necessary
$database = "ems1"; // Change to your desired database name

// Create database connection
$conn = new mysqli($host, $username, $password);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$create_db_query = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($create_db_query) !== TRUE) {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database
$conn->select_db($database);

// Create tables if they do not exist
$table_creation_queries = [
    "CREATE TABLE IF NOT EXISTS employee (
        employee_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        name TEXT NOT NULL,
        phone TEXT,
        role ENUM('Teacher', 'Cleaner', 'Dormitory Proctors', 'Libraries', 'Lab Assistants', 'Security', 'Cafe Workers', 'Cashier', 'Finance Managers', 'Accountant', 'Admin', 'HR Managers') NOT NULL,
        assigned_for ENUM('Teacher', 'Cleaner', 'Dormitory Proctors', 'Libraries', 'Lab Assistants', 'Security', 'Cafe Workers', 'Cashier', 'Finance Managers', 'Accountant', 'HR Managers') ,
        hire_date DATE,
        salary DECIMAL(10, 2),
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS Attendance (
        attendance_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        date DATE,
        created ENUM('Created', 'NotCreated') NOT NULL DEFAULT 'NotCreated',
        status ENUM('Present', 'Absent','UnMarked') NOT NULL DEFAULT 'UnMarked',
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS Salaries (
        salary_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        base_salary DECIMAL(10, 2),
        deductions DECIMAL(10, 2),
        bonuses DECIMAL(10, 2),
        net_salary DECIMAL(10, 2),
        approval ENUM('Pending','Approved') NOT NULL DEFAULT 'pending',
        payment ENUM('Payed', 'NotPayed') NOT NULL DEFAULT 'NotPayed',
        calculated_date DATE,
        pay_date DATE,
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",

    "CREATE TABLE IF NOT EXISTS Accounts (
    account_id INTEGER PRIMARY KEY AUTO_INCREMENT, 
    employee_id INTEGER,
    balance DECIMAL(20, 2) NOT NULL DEFAULT 0.00,
    account_number VARCHAR(6) NOT NULL,
    UNIQUE KEY (account_number),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id) ON DELETE CASCADE
)",

    "CREATE TABLE IF NOT EXISTS Transactions (
    transaction_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    from_account INTEGER NOT NULL,
    to_account INTEGER NOT NULL,
    type ENUM('deposit', 'withdrawal', 'cash') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_account) REFERENCES Accounts(account_id) ON DELETE CASCADE,
    FOREIGN KEY (to_account) REFERENCES Accounts(account_id) ON DELETE CASCADE
)",
    "CREATE TABLE IF NOT EXISTS LeaveApplications (
        leave_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        reason TEXT,
        status ENUM('Pending', 'Approved','Disaproved') NOT NULL DEFAULT 'Pending',
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS AdminRequests (
        request_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        admin_id INTEGER,
        request_type TEXT,
        request_date DATE,
        details TEXT,
        status TEXT DEFAULT 'Pending',
        FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
    )",
    "CREATE TABLE IF NOT EXISTS Comments (
        comment_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        admin_id INTEGER,
        comment_date DATE,
        comment_text TEXT,
        FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
    )",
    "CREATE TABLE IF NOT EXISTS HRRequests (
        hr_request_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        assigned_for ENUM('Teacher', 'Cleaner', 'Dormitory Proctors', 'Libraries', 'Lab Assistants', 'Security', 'Cafe Workers', 'Cashier', 'Finance Managers', 'Accountant', 'HR Managers') ,
        request_date DATE,
        details TEXT,
        status ENUM('Pending', 'Approved','Disaproved') NOT NULL DEFAULT 'Pending',
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS AttendanceReports (
        report_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        report_date DATE,
        attendance_summary TEXT,
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS SalaryApprovals (
        approval_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        salary_id INTEGER,
        approved_by INTEGER,
        approval_date DATE,
        status TEXT DEFAULT 'Pending',
        FOREIGN KEY (salary_id) REFERENCES Salaries(salary_id),
        FOREIGN KEY (approved_by) REFERENCES FinanceManagers(finance_manager_id)
    )",
    "CREATE TABLE IF NOT EXISTS CashPayments (
        payment_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        amount DECIMAL(10, 2),
        payment_date DATE,
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )"
];

// Execute table creation queries
foreach ($table_creation_queries as $query) {
    $conn->query($query);
}

// Add default admin if not available
$check_admin_query = "SELECT * FROM employee WHERE username = ?";
$stmt = $conn->prepare($check_admin_query);
$stmt->bind_param("s", $default_admin_username);
$default_admin_username = "adem";
$stmt->execute();
$admin_result = $stmt->get_result();

if ($admin_result->num_rows == 0) {
    $default_admin_password = password_hash("adem123", PASSWORD_DEFAULT);

    $add_default_admin_query = "INSERT INTO employee (name,  phone, role,  hire_date, salary, username, password) 
                                VALUES (?, ?, ?, ?, ?, ?,  ?)";
    $stmt = $conn->prepare($add_default_admin_query);
    $default_admin_name = "Adem Abdrei";
    $default_admin_phone = "1234567890";
    $default_admin_position = "Admin";
    $default_admin_hire_date = "2023-01-01";
    $default_admin_salary = 5000.00;
    $stmt->bind_param("sssssss", $default_admin_name, $default_admin_phone, $default_admin_position, $default_admin_hire_date, $default_admin_salary, $default_admin_username, $default_admin_password);
    $stmt->execute();
}










// Add default admin if not available
$check_admin_query = "SELECT * FROM employee WHERE username = ?";
$stmt = $conn->prepare($check_admin_query);
$stmt->bind_param("s", $default_admin_username);
$default_admin_username = "wu";
$stmt->execute();
$admin_result = $stmt->get_result();

if ($admin_result->num_rows == 0) {
    $default_admin_password = password_hash("wu123", PASSWORD_DEFAULT);
    $default_admin_name = "Wallaga University";
    $default_admin_phone = "0923365046";

    $add_default_admin_query = "INSERT INTO employee (username, password, name, phone) 
                                VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($add_default_admin_query);
    $stmt->bind_param("ssss", $default_admin_username, $default_admin_password, $default_admin_name, $default_admin_phone);
    $stmt->execute();
}





// Check if a default admin account exists
$check_default_admin_query = "SELECT * FROM accounts WHERE employee_id = ?";
$stmt = $conn->prepare($check_default_admin_query);
$default_admin_username = "2";
$stmt->bind_param("s", $default_admin_username);
$stmt->execute();
$default_admin_result = $stmt->get_result();

// If the default admin account doesn't exist, create it
if ($default_admin_result->num_rows == 0) {
    $default_admin_password = "1000000000";
    $default_admin_account_number = "000001";

    $add_default_admin_query = "INSERT INTO accounts (employee_id, balance, account_number) 
                                VALUES (?, ?, ?)";
    $stmt = $conn->prepare($add_default_admin_query);
    $stmt->bind_param("sss", $default_admin_username, $default_admin_password, $default_admin_account_number);
    $stmt->execute();
}
