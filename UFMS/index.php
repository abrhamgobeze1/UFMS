<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="shortcut icon" href="/ems/images/favicon/favicon.jpg" type="image/x-icon">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .hero-section {
            background-image: url('images/slider/campus1.jpg');
            background-size: cover;
            color: #fff;
            text-align: center;
            padding: 100px 0;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 40px;
        }

        .features-section {
            padding: 80px 0;
            background-color: #fff;
            text-align: center;
        }

        .features-section h2 {
            font-size: 2.5rem;
            margin-bottom: 50px;
        }

        .feature {
            margin-bottom: 30px;
        }

        .feature h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .testimonial-section {
            padding: 80px 0;
            background-color: #f8f9fa;
            text-align: center;
        }

        .testimonial {
            margin-bottom: 50px;
        }

        .testimonial img {
            width: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .testimonial p {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .testimonial span {
            font-weight: bold;
            font-style: italic;
        }

        .carousel-item img {
            max-height: 400px;
            object-fit: cover;
        }
        .features-section .feature img {
    max-width: 100px;
    height: auto;
}
    </style>
</head>

<body>
    <?php include 'includes/header.php' ?>
    <section class="hero-section">
        <div class="container">
            <h1>Welcome to the Employee Management System</h1>
            <p>Streamline your employee management with ease</p>
            <a href="login.php" class="btn btn-success btn-lg">Login</a>
        </div>
    </section>
    <section class="features-section">
    <div class="container">
        <h2>Key Features</h2>
        <div class="row">
            <div class="col-md-4 feature">
                <img src="images/features/Saly-25.png" alt="Attendance Tracking" class="img-fluid mb-3">
                <h3>Attendance Tracking</h3>
                <p>Easily monitor employee attendance and leave management.</p>
            </div>
            <div class="col-md-4 feature">
                <img src="images/features/Money-bag-on-transparent-background-PNG.png" alt="Payroll Management" class="img-fluid mb-3">
                <h3>Payroll Management</h3>
                <p>Streamline your payroll processing with our robust system.</p>
            </div>
            <div class="col-md-4 feature">
                <img src="images/features/business-3d.png" alt="Performance Evaluation" class="img-fluid mb-3">
                <h3>Performance Evaluation</h3>
                <p>Conduct comprehensive employee performance reviews.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 feature">
                <img src="images/features/Saly-10.png" alt="Training Management" class="img-fluid mb-3">
                <h3>Training Management</h3>
                <p>Manage employee training and development programs.</p>
            </div>
            <div class="col-md-4 feature">
                <img src="images/features/marketing-employee-announcing-product-sale-3021096.png" alt="Reporting" class="img-fluid mb-3">
                <h3>Reporting</h3>
                <p>Generate comprehensive reports for informed decision-making.</p>
            </div>
            <div class="col-md-4 feature">
                <img src="images/features/Vector (1).png" alt="Recruitment" class="img-fluid mb-3">
                <h3>Recruitment</h3>
                <p>Streamline your employee recruitment process.</p>
            </div>
        </div>
    </div>
</section>
    
    <?php include 'includes/footer.php' ?> <!-- jQuery and Bootstrap JS --> <!-- Bootstrap JavaScript dependencies -->
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/chart.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
</body>

</html>