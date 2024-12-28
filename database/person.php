<?php
function getPersonInfo($user_id) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
            Person.name, 
            Person.email, 
            Person.phone_number, 
            Person.address, 
            Person.city, 
            PetOwner.avg_rating,
            Pet.id AS pet_id, 
            Pet.name AS pet_name, 
            Pet.species AS pet_species, 
            Pet.size AS pet_size, 
            Pet.birthdate AS pet_age,
            Pet.profile_picture AS pet_profile_picture
        FROM 
            PetOwner 
        JOIN 
            Person ON PetOwner.person = Person.id 
        LEFT JOIN 
            Pet ON Pet.owner = Person.id 
        WHERE 
            PetOwner.person = :id'
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProviderInfo($user_id) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT *  
        FROM ServiceProvider 
        WHERE ServiceProvider.person = :id'
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function deleteUser($user_id) {
    global $dbh;
    $stmt = $dbh->prepare('DELETE FROM PetOwner WHERE person = ?');
    $stmt->execute([$user_id]);
    $stmt = $dbh->prepare('DELETE FROM ServiceProvider WHERE person = ?');
    $stmt->execute([$user_id]);
    $stmt = $dbh->prepare('DELETE FROM Person WHERE id = ?');
    $stmt->execute([$user_id]);
}


function searchAvailableProviders($service_date, $start_time, $end_time, $service_type, $owner_city, $id) {
    global $dbh;
    $stmt = $dbh->prepare(
        "SELECT 
            FreeProviders.provider_id, 
            FreeProviders.provider_name, 
            FreeProviders.provider_phone_number, 
            FreeProviders.provider_avg_rating, 
            FreeProviders.provider_email, 
             FreeProviders.provider_address,
            AvailableProviders.schedule_day_week, 
            AvailableProviders.schedule_start_time, 
            AvailableProviders.schedule_end_time 
        FROM (
            SELECT  
                ServiceProvider.person AS provider_id, 
                Person.name AS provider_name, 
                Person.phone_number AS provider_phone_number, 
                Person.email AS provider_email, 
                   Person.address AS provider_address,
                ServiceProvider.avg_rating AS provider_avg_rating 
            FROM ServiceProvider 
            JOIN Person ON Person.id = ServiceProvider.person 
            LEFT JOIN Booking 
                ON Booking.provider = ServiceProvider.person  
                AND Booking.date = :date 
                AND (Booking.start_time < :end_time AND Booking.end_time > :start_time) 
            WHERE Booking.id IS NULL
            AND ServiceProvider.person != :id -- Exclude the specific provider ID here
        ) AS FreeProviders 
        JOIN (
            SELECT 
                Schedule.service_provider AS provider_id, 
                Schedule.day_week AS schedule_day_week, 
                Schedule.start_time AS schedule_start_time, 
                Schedule.end_time AS schedule_end_time 
            FROM Schedule 
            JOIN ServiceProvider ON ServiceProvider.person = Schedule.service_provider 
            JOIN Person ON Person.id = ServiceProvider.person 
            WHERE 
                schedule_start_time <= :start_time 
                AND schedule_end_time >= :end_time 
                AND schedule_day_week = :date 
                AND (ServiceProvider.service_type = :service_type OR ServiceProvider.service_type = 'both') 
                AND Person.city = :owner_city 
                AND ServiceProvider.person != :id -- Exclude the specific provider ID here
        ) AS AvailableProviders 
        ON FreeProviders.provider_id = AvailableProviders.provider_id;" 
    );
    $stmt->bindValue(':date', $service_date, PDO::PARAM_STR);
    $stmt->bindValue(':start_time', $start_time, PDO::PARAM_STR);
    $stmt->bindValue(':end_time', $end_time, PDO::PARAM_STR);
    $stmt->bindValue(':service_type', $service_type, PDO::PARAM_STR);
    $stmt->bindValue(':owner_city', $owner_city, PDO::PARAM_STR);   
    $stmt->bindValue(':id', $id, PDO::PARAM_INT); 

    $stmt->execute();
    return $stmt->fetchAll();
}

function loginSuccess($email, $password) {// Check if email and password are correct
    global $dbh;
    $stmt = $dbh->prepare(
      'SELECT p.id, p.name, p.email, p.phone_number, p.city, p.address, sp.iban, sp.service_type
      FROM Person p
      LEFT JOIN ServiceProvider sp ON p.id = sp.person
      WHERE p.email = ? AND p.password = ?');
    $stmt->execute(array($email, hash('sha256', $password)));
    return $stmt->fetch(); // Fetch will return the row if credentials are valid
}

function insertPerson($name, $phone_number, $address, $email, $city, $iban, $password, $service_type) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Person (name, phone_number, address, email, city, password) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute(array($name, $phone_number, $address, $email, $city, hash('sha256', $password)));
    $personId = $dbh->lastInsertId();
    insertProvider($personId, $iban, $service_type);
    insertOwner($personId);
    return $personId; // Retorna o ID do novo usuário
}

function insertProvider($person, $iban, $service_type) {
    global $dbh;
    if (!empty($iban) && !empty($service_type)) {
        $stmt = $dbh->prepare('INSERT INTO ServiceProvider (person, iban, service_type) VALUES (?, ?, ?)');
        $stmt->execute(array($person, $iban, $service_type));
    }
}

function insertOwner($person) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO PetOwner (person) VALUES (?)');
    $stmt->execute(array($person));
}

?>