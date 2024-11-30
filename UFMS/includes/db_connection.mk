
### Table Structures

```sql
CREATE TABLE Employees (
    employee_id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    phone TEXT,
    department TEXT,
    position TEXT,
    hire_date DATE,
    salary DECIMAL(10, 2)
);

CREATE TABLE Admins (
    admin_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    first_name TEXT,
    last_name TEXT
);

CREATE TABLE HRManagers (
    hr_manager_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    first_name TEXT,
    last_name TEXT
);

CREATE TABLE Accountants (
    accountant_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    first_name TEXT,
    last_name TEXT
);

CREATE TABLE FinanceManagers (
    finance_manager_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    first_name TEXT,
    last_name TEXT
);

CREATE TABLE Cashiers (
    cashier_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    first_name TEXT,
    last_name TEXT
);

CREATE TABLE Attendance (
    attendance_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER,
    date DATE,
    status TEXT,
    FOREIGN KEY (employee_id) REFERENCES Employees(employee_id)
);

CREATE TABLE Salaries (
    salary_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER,
    base_salary DECIMAL(10, 2),
    deductions DECIMAL(10, 2),
    bonuses DECIMAL(10, 2),
    net_salary DECIMAL(10, 2),
    pay_date DATE,
    FOREIGN KEY (employee_id) REFERENCES Employees(employee_id)
);

CREATE TABLE LeaveApplications (
    leave_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER,
    start_date DATE,
    end_date DATE,
    reason TEXT,
    status TEXT DEFAULT 'Pending',
    FOREIGN KEY (employee_id) REFERENCES Employees(employee_id)
);

CREATE TABLE AdminRequests (
    request_id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_id INTEGER,
    request_type TEXT,
    request_date DATE,
    details TEXT,
    status TEXT DEFAULT 'Pending',
    FOREIGN KEY (admin_id) REFERENCES Admins(admin_id)
);

CREATE TABLE Comments (
    comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_id INTEGER,
    comment_date DATE,
    comment_text TEXT,
    FOREIGN KEY (admin_id) REFERENCES Admins(admin_id)
);

CREATE TABLE HRRequests (
    hr_request_id INTEGER PRIMARY KEY AUTOINCREMENT,
    hr_manager_id INTEGER,
    request_type TEXT,
    request_date DATE,
    details TEXT,
    status TEXT DEFAULT 'Pending',
    FOREIGN KEY (hr_manager_id) REFERENCES HRManagers(hr_manager_id)
);

CREATE TABLE AttendanceReports (
    report_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER,
    report_date DATE,
    attendance_summary TEXT,
    FOREIGN KEY (employee_id) REFERENCES Employees(employee_id)
);

CREATE TABLE SalaryApprovals (
    approval_id INTEGER PRIMARY KEY AUTOINCREMENT,
    salary_id INTEGER,
    approved_by INTEGER,
    approval_date DATE,
    status TEXT DEFAULT 'Pending',
    FOREIGN KEY (salary_id) REFERENCES Salaries(salary_id),
    FOREIGN KEY (approved_by) REFERENCES FinanceManagers(finance_manager_id)
);

CREATE TABLE CashPayments (
    payment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER,
    amount DECIMAL(10, 2),
    payment_date DATE,
    FOREIGN KEY (employee_id) REFERENCES Employees(employee_id)
);
```

### Explanation of Each Table

**Employees**
- `employee_id`: Unique identifier for each employee.
- `first_name`: Employee's first name.
- `last_name`: Employee's last name.
- `email`: Employee's email address (unique).
- `phone`: Employee's phone number.
- `department`: Department where the employee works.
- `position`: Job position of the employee.
- `hire_date`: Date when the employee was hired.
- `salary`: Base salary of the employee.

**Admins**
- `admin_id`: Unique identifier for each admin.
- `username`: Admin's username for login.
- `password`: Admin's password for login.
- `first_name`: Admin's first name.
- `last_name`: Admin's last name.

**HRManagers**
- `hr_manager_id`: Unique identifier for each HR manager.
- `username`: HR manager's username for login.
- `password`: HR manager's password for login.
- `first_name`: HR manager's first name.
- `last_name`: HR manager's last name.

**Accountants**
- `accountant_id`: Unique identifier for each accountant.
- `username`: Accountant's username for login.
- `password`: Accountant's password for login.
- `first_name`: Accountant's first name.
- `last_name`: Accountant's last name.

**FinanceManagers**
- `finance_manager_id`: Unique identifier for each finance manager.
- `username`: Finance manager's username for login.
- `password`: Finance manager's password for login.
- `first_name`: Finance manager's first name.
- `last_name`: Finance manager's last name.

**Cashiers**
- `cashier_id`: Unique identifier for each cashier.
- `username`: Cashier's username for login.
- `password`: Cashier's password for login.
- `first_name`: Cashier's first name.
- `last_name`: Cashier's last name.

**Attendance**
- `attendance_id`: Unique identifier for each attendance record.
- `employee_id`: Reference to the employee.
- `date`: Date of attendance.
- `status`: Attendance status (e.g., present, absent).

**Salaries**
- `salary_id`: Unique identifier for each salary record.
- `employee_id`: Reference to the employee.
- `base_salary`: Base salary amount.
- `deductions`: Total deductions.
- `bonuses`: Total bonuses.
- `net_salary`: Calculated net salary.
- `pay_date`: Date when the salary is paid.

**LeaveApplications**
- `leave_id`: Unique identifier for each leave application.
- `employee_id`: Reference to the employee.
- `start_date`: Start date of the leave.
- `end_date`: End date of the leave.
- `reason`: Reason for the leave.
- `status`: Status of the leave application (e.g., pending, approved).

**AdminRequests**
- `request_id`: Unique identifier for each admin request.
- `admin_id`: Reference to the admin who made the request.
- `request_type`: Type of request.
- `request_date`: Date of the request.
- `details`: Details of the request.
- `status`: Status of the request (e.g., pending, approved).

**Comments**
- `comment_id`: Unique identifier for each comment.
- `admin_id`: Reference to the admin who made the comment.
- `comment_date`: Date of the comment.
- `comment_text`: Text of the comment.

**HRRequests**
- `hr_request_id`: Unique identifier for each HR request.
- `hr_manager_id`: Reference to the HR manager who made the request.
- `request_type`: Type of request.
- `request_date`: Date of the request.
- `details`: Details of the request.
- `status`: Status of the request (e.g., pending, approved).

**AttendanceReports**
- `report_id`: Unique identifier for each attendance report.
- `employee_id`: Reference to the employee.
- `report_date`: Date of the report.
- `attendance_summary`: Summary of attendance details.

**SalaryApprovals**
- `approval_id`: Unique identifier for each salary approval.
- `salary_id`: Reference to the salary record.
- `approved_by`: Reference to the finance manager who approved the salary.
- `approval_date`: Date of the approval.
- `status`: Status of the approval (e.g., pending, approved).

**CashPayments**
- `payment_id`: Unique identifier for each cash payment.
- `employee_id`: Reference to the employee.
- `amount`: Amount of cash paid.
- `payment_date`: Date of the payment.

These tables and their relationships form a comprehensive database structure for the Academic Staff Employee Management System, supporting the detailed interactions and roles of each actor in the system.