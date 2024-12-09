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
    <?php include('footer.php'); ?>
</body>

</html>