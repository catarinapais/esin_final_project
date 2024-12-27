<?php
function getPublicReviews() {
    global $dbh;
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
        WHERE Booking.review_consent = "YES" AND Booking.ownerReview IS NOT "0";'
    ); 
    $stmt->execute();
    return $stmt->fetchAll();
}

function getReviewByBookingId($booking_id) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
            Booking.type AS type,
            Booking.date AS date, 
            Booking.start_time AS start_time, 
            Booking.duration AS duration,
            Booking.ownerReview AS ownerReview,
            Booking.providerReview AS providerReview, 
            Pet.name AS pet_name, 
            Pet.id AS pet_id, 
            Pet.species AS species,
            Owner.name AS owner_name,
            Provider.name AS provider_name
        FROM Booking 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN PetOwner ON Pet.owner = PetOwner.person 
        JOIN Person AS Owner ON PetOwner.person = Owner.id 
        JOIN Person AS Provider ON Booking.provider = Provider.id 
        WHERE Booking.id = :id'
    );
    $stmt->bindValue(':id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function insertReview($rating, $description, $service_id, $role) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Review (rating, description, date_review) VALUES (?, ?, ?)');
    $stmt->execute([$rating, $description, date('Y-m-d')]);
    $review_id = $dbh->lastInsertId();
    if ($role == 'owner') {
        $stmt = $dbh->prepare('UPDATE Booking SET ownerReview = ? WHERE id = ?');
    } else {
        $stmt = $dbh->prepare('UPDATE Booking SET providerReview = ? WHERE id = ?');
    }
    $stmt->execute([$review_id, $service_id]);
    $_SESSION['msg_success'] = "Review added successfully.";
}
?>