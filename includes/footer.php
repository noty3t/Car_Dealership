
    <!-- Footer -->
    <?php
    $common_url = "/car-dealership";
    ?>
    <footer class="bg-dark text-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4">
                    <h5>About CarDeal</h5>
                    <p>Your trusted partner for buying new and used cars. We offer the best deals on a wide range of vehicles.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Home</a></li>
                        <li><a href="<?php echo $common_url ?> /listings.php" class="text-white">Car Listings</a></li>
                        <li><a href="<?php echo $common_url ?> /news.php" class="text-white">Car News</a></li>
                        <li><a href="<?php echo $common_url ?> /about.php" class="text-white">About Us</a></li>
                        <?php if(!isAdmin()):?>
                        <li><a href="<?php echo $common_url ?> /contact.php" class="text-white">Contact</a></li>
                        <?php endif?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt"></i> Main Street, Taunggyi City</p>
                        <p><i class="fas fa-phone"></i> 0951971090</p>
                        <p><i class="fas fa-envelope"></i> info@cardeal.com</p>
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> CarDeal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Include SweetAlert JS -->
    <!-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> -->
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script> -->
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="/assets/js/script.js"></script>
    


</body>
</html>