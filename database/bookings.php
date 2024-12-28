<?php
function getPastBookings($user_id) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
            Booking.id AS service_id,
            Booking.type AS type,
            Booking.date AS date, 
            Booking.start_time AS start_time, 
            Booking.duration AS duration,
            Booking.ownerReview AS ownerReview,
            Booking.providerReview AS providerReview, 
            Booking.payment AS payment,
            Pet.name AS pet_name, 
            Pet.id AS pet_id, 
            Pet.species AS species, 
            OwnerReview.rating AS owner_review,
            ProviderReview.rating AS provider_review
        FROM Booking 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN PetOwner ON Pet.owner = PetOwner.person 
        LEFT JOIN Review AS OwnerReview ON Booking.ownerReview = OwnerReview.id 
        LEFT JOIN Review AS ProviderReview ON Booking.providerReview = ProviderReview.id 
        WHERE PetOwner.person = :id AND Booking.date < CURRENT_TIMESTAMP'
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(); // Get all past bookings
}

function getPastServices($user_id) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
            Booking.id AS service_id,
            Booking.type AS type,
            Booking.date AS service_date, 
            Booking.start_time AS service_time, 
            Booking.duration AS service_duration, 
            Booking.payment AS payment,
            OwnerReview.rating AS owner_review,
            ProviderReview.rating AS provider_review,
            Pet.name AS pet_name 
        FROM Booking 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN ServiceProvider ON Booking.provider = ServiceProvider.person 
        LEFT JOIN Review AS OwnerReview ON Booking.ownerReview = OwnerReview.id 
        LEFT JOIN Review AS ProviderReview ON Booking.providerReview = ProviderReview.id 
        WHERE Booking.provider = :id  AND Booking.date < CURRENT_TIMESTAMP'
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(); // Get all past services
}

function getFutureBookings($user_id) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
        Booking.date, 
        Booking.start_time, 
        Booking.end_time, 
        Booking.address_collect, 
        Booking.type AS service_type, 
        Booking.address_collect AS address, 
        Provider.id AS provider_id, 
        Provider.name AS provider_name, 
        ServiceProvider.avg_rating AS provider_rating,
        Owner.id AS owner_id, 
        Owner.city AS owner_city, 
        Pet.name AS pet_name, 
        Pet.profile_picture AS pet_picture
        FROM Booking 
        JOIN Person AS Provider ON Booking.provider = Provider.id 
        JOIN ServiceProvider ON ServiceProvider.person = Provider.id 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN Person AS Owner ON Pet.owner = Owner.id 
        WHERE Booking.date >= ? AND Owner.id = ?;'
    ); // prepared statement
    $stmt->execute([date('Y-m-d'), $user_id]);
    return $stmt->fetchAll();
}

function getFutureServices($user_id) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
            Booking.date AS service_date, 
            Booking.start_time AS service_start_time,  
            Booking.end_time AS service_end_time,  
            Booking.type AS service_type, 
            Booking.address_collect AS address, 
            Owner.id AS owner_id, 
            Owner.name AS owner_name, 
            Owner.city AS owner_city, 
            Pet.name AS pet_name, 
            Pet.species AS pet_species, 
            Pet.profile_picture AS pet_picture, 
            MedicalNeed.description AS medical_needs 
        FROM Booking 
        JOIN Person AS Provider ON Booking.provider = Provider.id 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN Person AS Owner ON Pet.owner = Owner.id 
        LEFT JOIN PetMedicalNeed ON Pet.id = PetMedicalNeed.pet
        LEFT JOIN MedicalNeed ON PetMedicalNeed.medicalNeed = MedicalNeed.id
        WHERE Booking.date >= ? AND Booking.provider = ?;'
    ); // prepared statement
    $stmt->execute([date('Y-m-d'), $user_id]);
    return $stmt->fetchAll();
}

function getIban($booking_id) {
    global $dbh;
    $stmt = $dbh->prepare('
        SELECT 
            ServiceProvider.iban
        FROM 
            Booking
        INNER JOIN ServiceProvider ON Booking.provider = ServiceProvider.person
        WHERE 
            Booking.id = :booking_id;
    ');
    $stmt->bindValue(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBookingById($booking_id) {
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

function insertBooking($date, $start_time, $end_time, $duration, $location, $photo_consent, $review_consent, $provider_id, $service_type, $pet_id, $payment) {
    global $dbh;
    $stmt = $dbh->prepare('
        INSERT INTO Booking 
        (date, start_time, end_time, duration, address_collect, photo_consent, review_consent, provider, type, pet, payment) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $date,
        $start_time,
        $end_time,
        $duration,
        $location,
        $photo_consent,
        $review_consent,
        $provider_id,
        $service_type,
        $pet_id,
        $payment
    ]);
}

function deleteBookingsFromUser($user_id) {
    global $dbh;
    $stmt = $dbh->prepare('DELETE FROM Booking WHERE pet IN (SELECT id FROM Pet WHERE owner = ?)');
    $stmt->execute([$user_id]);
    $stmt = $dbh->prepare('DELETE FROM Payment WHERE id IN (SELECT payment FROM Booking WHERE pet IN (SELECT id FROM Pet WHERE owner = ?))');
    $stmt->execute([$user_id]);
}
?>