<?php
session_start();

function starReview($stars) {
    $stars = (int)$stars;
    $count = 1;
    $result = "<div class='star-review'>";

    for($i = 1; $i <= 5; $i++){
        if($stars >= $count){
            $result .= "<span>&#x2605</span>";
        } else {
            $result .= "<span>&#x2606</span>";
        }
        $count++;
    }
    $result .= "</div>";
    return $result;
}

require_once('../database/init.php');
require_once('../database/review.php');

try { // try catch for error handling
    $reviews = getPublicReviews();
} catch (Exception $e) {
    $_SESSION['msg_error'] = "Connection Error.";
    exit();
}


?>


    <?php
    include('../templates/header_tpl.php');
    ?>
    <main id="content">

        <?php
        if (isset($_SESSION["msg_success"])) {
            echo "<p class='msg_success'>{$_SESSION["msg_success"]}</p>";
            unset($_SESSION["msg_success"]);
        } 
        if (isset($_SESSION["msg_error"])) {
            echo "<p class='msg_error'>{$_SESSION["msg_error"]}</p>";
            unset($_SESSION["msg_error"]);
        }?>
        <article>
            <div class="imageContainer">
                <img src="../images/assets/initial_page_img.jpeg" alt="A cat and two dogs.">
                <p class="overlayText">The platform for your 4-paw besties</p>
            </div>
        </article>
        <article id="services">
            <h2>Book with us</h2>
            <div class="services-container">
                <a href="bookingRequest.php?service_type=petsitting" class="service-card">
                    <h3>PET SITTING</h3>
                    <p>Reliable and loving care for your pets at home.</p>
                </a>
                <a href="bookingRequest.php?service_type=petwalking" class="service-card">
                    <h3>PET WALKING</h3>
                    <p>Healthy and fun walks for your furry friends.</p>
                </a>
                <a href="serviceProvider.php" class="service-card">
                    <h3>BECOME A SITTER/WALKER</h3>
                    <p>Join our community and earn doing what you love.</p>
                </a>
            </div>
        </article>
        <?php if(!empty($reviews)) : ?>
        <article id="reviews">
            <h2>What our clients say</h2>
            <div class="reviews-container">
            <?php // Retrieve last 3 reviews
            $reviewCount = count($reviews);
            $lastReviews = array_slice($reviews, max(0, $reviewCount - 3));

            foreach ($lastReviews as $review) : ?>
                <article class="review-card">
                    <?php echo starReview($review['rating']); ?>
                    <p class="review-desc">"<?php echo $review['description']; ?>"</p>
                    <p class="review-author">- <?php echo $review['owner_name']; ?>, owner of <?php echo $review['pet_name']; ?></p>
                    <p class="review-date"><?php echo $review['date_review']; ?></p>
                </article>
            <?php endforeach; ?>
            </div>

        </article>
        <?php endif; ?>
    </main>
    <?php include('../templates/footer_tpl.php'); ?>
