<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Pet Care</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
</head>
<body>
    <?php include('header.php'); ?>

    <main id="about-content">
    <div class="about-container">
        <div class="about-section">
            <h2>Our Story</h2>
            <p>We are three university students who share a passion for animals and the well-being of pets. Our journey began with the idea of creating a platform that connects pet owners with reliable and loving pet sitters and walkers. As students with busy schedules, we realized the need for trustworthy services that could provide pets with the care they deserve while their owners are away.</p>
            <p>We wanted to build something that would help both pet owners and pet care providers find each other easily and reliably. After working hard on this idea, we launched Pet Patrol, a platform where pet lovers can offer their services as sitters and walkers, ensuring pets get the best care possible.</p>
        </div>

        <div class="about-section">
            <h2>Our Mission</h2>
            <p>Our mission is simple: to make sure every pet gets the love, attention, and care they deserve. We aim to connect pet owners with trusted and compassionate pet sitters and walkers in a way that’s easy, efficient, and reliable. By providing a space where pet lovers can come together, we hope to foster a community of pet owners and caregivers who share the same values of trust, safety, and respect for animals.</p>
        </div>
    </div>
    

    <section id="ceos">
        <h2>Our CEOs and Their Furry Friends</h2>
        <p>Meet the visionary CEOs and their beloved pets who inspired the PetPatrol project!</p>
        <div class="ceo-container">
            <div class="ceo">
                <img src="images/rui.jpg" alt="CEO 1">
                <p>Rui with Nero</p>
                
            </div>
            <div class="ceo">
                <img src="images/cat.jpg" alt="CEO 2">
                <p>Catarina with Leia, Fernando and Madalena</p>
                
            </div>
            <div class="ceo">
                <img src="images/ema.jpg" alt="CEO 3">
                <p>Ema with Ziggy</p>
                
            </div>
        </div>
    </section>
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
