<?php
session_start();
?>


    <?php
    include('templates/header_tpl.php');
    ?>
    <main id="content">
        <?php
        if (isset($_SESSION["msg_success"])) {
            echo "<p class='msg_success'>{$_SESSION["msg_success"]}</p>";
            unset($_SESSION["msg_success"]);
        } ?>
        <article>
            <div class="imageContainer">
                <img src="images/assets/initial_page_img.jpeg" alt="A cat and two dogs.">
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
    <?php include('templates/footer_tpl.php'); ?>
