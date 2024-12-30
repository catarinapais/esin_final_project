<?php
session_start();

$service_id = $_POST['service_id']; // id do serviço que se vai fazer review
$role = $_POST['role']; // a quem se vai dar a review

if ($role == 'owner') {
    $service = 'Service';
} else {
    $service = 'Booking';
}
// role = owner - o provider vai dar review ao owner (pelo SERVICE feito)
// role = provider - o owner vai dar review ao provider (pelo BOOKING feito)

require_once('../database/init.php');
require_once('../database/bookings.php');

try {
    $bookingInfo = getBookingById($service_id);
} catch (PDOException $e) {
    // Tratamento de erro
    echo "Erro de conexão: " . $e->getMessage();
}
?>

<?php include('../templates/header_tpl.php'); ?>

   
<section id="review-page">
    <form action="../actions/action_review.php" method="post">
        <h2>Past <?= $service ?></h2>
        <h3>Pet <?= $bookingInfo[0]['type'] ?> to <?= $bookingInfo[0]['pet_name'] ?></h3>
        <p><?= ucfirst($role) ?>: <?= $bookingInfo[0][$role . '_name'] ?></p>
        <p>Date: <?= $bookingInfo[0]['date'] ?> @ <?= $bookingInfo[0]['start_time'] ?></p>

        <div class="review-title-stars">
            <h3>Review this <?= ucfirst($role) ?>:</h3>
            <div id="starReview">
                <input type="radio" id="star5" name="review" value="5" required="required">
                <label for="star5" title="5 stars">&#9733;</label>
                <input type="radio" id="star4" name="review" value="4" required="required">
                <label for="star4" title="4 stars">&#9733;</label>
                <input type="radio" id="star3" name="review" value="3" required="required">
                <label for="star3" title="3 stars">&#9733;</label>
                <input type="radio" id="star2" name="review" value="2" required="required">
                <label for="star2" title="2 stars">&#9733;</label>
                <input type="radio" id="star1" name="review" value="1" required="required">
                <label for="star1" title="1 star">&#9733;</label>
            </div>
        </div>

        <label>
            <textarea id="reviewDescription" name="reviewDescription" rows="3" cols="30" placeholder="Describe your experience!" required="required"></textarea>
        </label>
        <input type="hidden" name="service_id" value="<?= $service_id ?>">
        <input type="hidden" name="role" value="<?= $role ?>">
        <input type="submit" value="Submit Review">
    </form>
</section>


<?php include('../templates/footer_tpl.php'); ?>