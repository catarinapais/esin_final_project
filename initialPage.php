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

function retrieveReviews() {
    global $reviews, $error_msg, $dbh;
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //association fetching
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

    try { // try catch for error handling
        $stmt = $dbh->prepare(
            'SELECT 
            OwnerReview.rating, 
            OwnerReview.description, 
            OwnerReview.date_review, 
            Person.name AS owner_name, 
            Pet.name AS pet_name 
            FROM Booking 
            JOIN Review AS OwnerReview ON Booking.ownerReview = OwnerReview.id 
            JOIN Pet ON Booking.pet = Pet.id 
            JOIN Person ON Person.id = Pet.owner 
            WHERE Booking.photo_consent = "yes" AND Booking.ownerReview IS NOT "0";'
        ); // prepared statement
        $stmt->execute();
        $reviews = $stmt->fetchAll(); //fetching all schedules by the user (array of arrays)
    } catch (Exception $e) {
        $error_msg = $e->getMessage(); // ir buscar a mensagem de erro e guardar em $error_msg
    }
    return $reviews;
}


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
        <?php $reviews = retrieveReviews(); ?>
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
    <?php include('templates/footer_tpl.php'); ?>
