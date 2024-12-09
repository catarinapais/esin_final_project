<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pet Patrol - Home</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--define this for responsive design-->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
</head>

<body>
    <?php
    include('header.php');
    ?>
    <main id="content">
        <?php
        if (isset($_SESSION["msg_success"])) {
            echo "<p class='msg_success'>{$_SESSION["msg_success"]}</p>";
            unset($_SESSION["msg_success"]);
        } ?>
        <article>
            <div class="imageContainer">
                <img src="images/initial_page_img.jpeg" alt="A cat and two dogs.">
                <p class="overlayText">The platform for your 4-paw besties</p>
            </div>
        </article>
        <article id="services">
            <h2>Book with us</h2>
            <div class="services-container">
                <a href="bookingRequest.php?service=petsitting" class="service-card">
                    <h3>PET SITTING</h3>
                    <p>Reliable and loving care for your pets at home.</p>
                </a>
                <a href="bookingRequest.php?service=petwalking" class="service-card">
                    <h3>PET WALKING</h3>
                    <p>Healthy and fun walks for your furry friends.</p>
                </a>
                <a href="serviceProvider.html" class="service-card">
                    <h3>BECOME A SITTER/WALKER</h3>
                    <p>Join our community and earn doing what you love.</p>
                </a>
            </div>
        </article>


    </main>



    <footer>
        <div class="footer-container">

            <!-- Quick Links Section -->
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="bookingRequest.html">Book a service</a></li>
                    <li><a href="serviceProvider.html">Become a PetPatroller</a></li>
                    <li><a href="aboutus.html">About Us</a></li>
                    <li><a href="FAQs.html">FAQs</a></li>
                </ul>
            </div>

            <!-- Contact Section -->
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: contact@petpatrol.com</p>
                <p>Phone: +351 225362821 </p>
            </div>

            <!-- Address Section -->
            <div class="footer-section">
                <h3>Our Office</h3>
                <p>PetPatrol HQ</p>
                <p>Rua Dr. Roberto Frias, 4200-465 Porto</p>
                <p>Open Hours: Mon-Fri, 9am - 6pm</p>
            </div>

            <!-- Newsletter Section -->
            <div class="footer-section">
                <h3>Subscribe to our Newsletter</h3>
                <form action="/subscribe" method="post">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>

        </div>

        <!-- Legal Section -->
        <div class="footer-legal">
            <p>&copy; 2024 PetPatrol. All rights reserved.</p>

        </div>
    </footer>
</body>

</html>