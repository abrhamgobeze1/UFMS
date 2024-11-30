<!-- Footer -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="text-uppercase font-weight-bold mb-4 text-primary">About Wallaga University Employee Management System</h5>
                <p class="text-muted">The Wallaga University Employee Management System is a comprehensive platform that helps manage the employee records, payroll, and other HR-related processes for the university. We strive to provide efficient and secure services to the employees of Wallaga University.</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="text-uppercase font-weight-bold mb-4 text-primary">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="/public_html/index.php" class="text-white hover-effect">Home</a></li>
                    <li><a href="/public_html/about.php" class="text-white hover-effect">About</a></li>
                    <li><a href="/public_html/contact.php" class="text-white hover-effect">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-uppercase font-weight-bold mb-4 text-primary">Contact Us</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt mr-3 text-primary"></i>Wallaga University, Nekemte Ethiopia</li>
                    <li><i class="fas fa-phone mr-3 text-primary"></i>+251 945-4497-53</li>
                    <li><i class="fas fa-envelope mr-3 text-primary"></i>wallagauniversity@gmail.com</li>
                </ul>
                <div class="social-icons">
                    <a href="#" class="text-white mr-3 hover-effect"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white mr-3 hover-effect"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white mr-3 hover-effect"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white mr-3 hover-effect"><i class="fab fa-pinterest-p"></i></a>
                    <a href="#" class="text-white mr-3 hover-effect"><i class="fab fa-snapchat-ghost"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-darker-dark py-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; <span id="date-time"></span> Wallaga University Employee Management System.
                        All rights reserved.</p>
                    <script>
                        function updateDateTime() {
                            var currentDate = new Date();
                            var dateTimeString = currentDate.getFullYear();
                            document.getElementById("date-time").innerHTML = dateTimeString;
                        }
                        setInterval(updateDateTime, 1000);
                    </script>
                </div>
                <div class="col-md-6 text-md-right">
                    <a href="#" class="text-white mr-3 hover-effect">Privacy Policy</a>
                    <a href="#" class="text-white mr-3 hover-effect">Terms of Service</a>
                    <a href="#" class="text-white hover-effect">Sitemap</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JavaScript dependencies -->
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/chart.js"></script>
<script src="../js/jquery-3.6.0.min.js"></script>



<!-- Barcode Generator Library -->
<script src="../js/JsBarcode.all.min.js"></script>
<!-- QR Code Library -->
<script src="../js/qrious.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>

<style>
    .hover-effect {
        transition: color 0.3s ease-in-out;
    }

    .hover-effect:hover {
        color: #007bff !important;
    }

    .bg-darker-dark {
        background-color: #1c1c1c;
    }
</style>