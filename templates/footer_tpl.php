<?php
session_start();
?>

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