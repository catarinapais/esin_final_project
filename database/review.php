<?php
function getPublicReviews() {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
        ProviderReview.rating, 
        ProviderReview.description, 
        ProviderReview.date_review, 
        Person.name AS owner_name, 
        Pet.name AS pet_name 
        FROM Booking 
        JOIN Review AS ProviderReview ON Booking.providerReview = ProviderReview.id 
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


function updateOwnerRating($service_id)
{
    global $dbh;

    $stmt = $dbh-> prepare ('SELECT Pet.owner AS owner_id FROM Booking JOIN Pet ON Booking.pet = Pet.id WHERE Booking.id = ?');
    $stmt->execute([$service_id]);
    $owner_id = $stmt->fetchColumn();

    if ($owner_id) {
        // Calculate the new average rating for the owner
        $stmt = $dbh->prepare(
            'SELECT ROUND(AVG(Review.rating),1 )AS avg_rating 
             FROM Booking 
             JOIN Review ON Booking.ownerReview = Review.id 
             WHERE Booking.pet IN (SELECT id FROM Pet WHERE owner = ?)'
        );
        $stmt->execute([$owner_id]);
        $avg_rating = $stmt->fetchColumn();

        // Update the owner's average rating
        $stmt = $dbh->prepare('UPDATE PetOwner SET avg_rating = ? WHERE person = ?');
        $stmt->execute([$avg_rating, $owner_id]);
    }
}

function updateProviderRating($service_id) {
    global $dbh;

    $stmt = $dbh->prepare('SELECT provider FROM Booking WHERE id = ?');
    $stmt->execute([$service_id]);
    $provider_id = $stmt->fetchColumn();

    if ($provider_id) {
        $stmt = $dbh->prepare(
            'SELECT Review.rating AS provider_rating 
             FROM Booking 
             JOIN Review ON Booking.providerReview = Review.id 
             WHERE Booking.provider = ?'
        );
        $stmt->execute([$provider_id]);
        $ratings = $stmt->fetchAll();
        $avg_rating = 0;
        foreach ($ratings as $rating) {
            $avg_rating += $rating['provider_rating'];
        }
        $stmt = $dbh->prepare('UPDATE ServiceProvider SET avg_rating = ? WHERE person = ?');
        $stmt->execute([$avg_rating/count($ratings), $provider_id]);
    }
}

function insertReview($rating, $description, $service_id, $role) {
    global $dbh;

    $stmt = $dbh->prepare('INSERT INTO Review (rating, description, date_review) VALUES (?, ?, ?)');
    $stmt->execute([$rating, $description, date('Y-m-d')]);
    $review_id = $dbh->lastInsertId();

    if ($role == 'owner') {
        $stmt = $dbh->prepare('UPDATE Booking SET ownerReview = ? WHERE id = ?');
        $stmt->execute([$review_id, $service_id]);
        updateOwnerRating($service_id);
    } else {
        $stmt = $dbh->prepare('UPDATE Booking SET providerReview = ? WHERE id = ?');
        $stmt->execute([$review_id, $service_id]);
        updateProviderRating($service_id);
    }
}
?>