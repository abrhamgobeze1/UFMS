// Create tables if they do not exist
$table_creation_queries = [
    "CREATE TABLE IF NOT EXISTS employee (
        employee_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        phone TEXT,
        position ENUM('Teacher', 'Cleaner', 'Dormitory Proctors', 'Libraries', 'Lab Assistants', 'Security', 'Cafe Workers', 'Cashier', 'Finance Managers', 'Accountant', 'Admin', 'HR Managers') NOT NULL,
        level ENUM('Entry', 'Intermediate', 'Senior') NOT NULL,
        hire_date DATE,
        salary DECIMAL(10, 2)
    )",
    "CREATE TABLE IF NOT EXISTS admin (
        admin_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        employee_id INTEGER,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS HRManagers (
        hr_manager_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        employee_id INTEGER,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS accountant (
        accountant_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        employee_id INTEGER,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS FinanceManagers (
        finance_manager_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        employee_id INTEGER,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS cashier (
        cashier_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        employee_id INTEGER,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS Attendance (
        attendance_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        date DATE,
        status TEXT,
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS Salaries (
        salary_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        base_salary DECIMAL(10, 2),
        deductions DECIMAL(10, 2),
        bonuses DECIMAL(10, 2),
        net_salary DECIMAL(10, 2),
        pay_date DATE,
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
    )",
    "CREATE TABLE IF NOT EXISTS LeaveApplications (
        leave_id INTEGER PRIMARY KEY AUTO_INCREMENT,
        employee_id INTEGER,
        start_date DATE,
        end_date DATE,
        reason TEXT,
        status TEXT DEFAULT 'Pending',
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
        hr_manager_id INTEGER,
        request_type TEXT,
        request_date DATE,
        details TEXT,
        status TEXT DEFAULT 'Pending',
        FOREIGN KEY (hr_manager_id) REFERENCES HRManagers(hr_manager_id)
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