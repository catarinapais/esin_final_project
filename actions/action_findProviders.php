<?php
session_start();

$id = $_SESSION['id'];
$owner_city = $_SESSION['city']; 

$pet_names = $_POST['pet_name'];
$service_type = $_POST['service_type']; // walking ou sitting
$service_date = $_POST['date'];
$start_time = $_POST['starttime'];
$end_time = $_POST['endtime'];
$location = $_POST['location'];
$other_address = $_POST['other_address'] ?? null;
if($location == 'other') {
    $location = $other_address;
}
$photo_consent = $_POST['photo_consent'] ?? 'NO';
$review_consent = $_POST['review_consent'] ?? 'NO';

if (empty($pet_names)) {
    $_SESSION['msg_error'] = "Select at least one of your pets.";
    header('Location: ../views/bookingRequest.php');
    exit;
} else if ($location == 'other' && empty($_POST['other_address'])) {
    $_SESSION['msg_error'] = "Enter the address.";
    header('Location: ../views/bookingRequest.php');
    exit;
} else if ($service_date < date('Y-m-d')) {
    $_SESSION['msg_error'] = "Date must be in the future.";
    header('Location: ../views/bookingRequest.php');
    exit;
} else if ($start_time >= $end_time) {
    $_SESSION['msg_error'] = "Start time must be before end time.";
    header('Location: ../views/bookingRequest.php');
    exit;
} else if ($start_time < '06:00' || $end_time > '22:00') {
    $_SESSION['msg_error'] = "Time must be between 06:00 and 22:00.";
    header('Location: ../views/bookingRequest.php');
    exit;
}

require_once('../database/init.php');
try {

    // query for getting available providers - AvailableProviders
    // that are not doing other services at the same time - FreeProviders
    $stmt = $dbh->prepare(
        "SELECT 
            FreeProviders.provider_id, 
            FreeProviders.provider_name, 
            FreeProviders.provider_phone_number, 
            FreeProviders.provider_avg_rating, 
            FreeProviders.provider_email, 
            AvailableProviders.schedule_day_week, 
            AvailableProviders.schedule_start_time, 
            AvailableProviders.schedule_end_time 
        FROM (
            SELECT  
                ServiceProvider.person AS provider_id, 
                Person.name AS provider_name, 
                Person.phone_number AS provider_phone_number, 
                Person.email AS provider_email, 
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
    // só falta verificar que o provider não tem nenhum serviço marcado para a mesma hora
    // TODO: o problema com a query é o "Schedule.day_week = strftime('%w', :date)" - ver isto
    /* no último WHERE
    Schedule.day_week = strftime('%w', :date) 
                AND 
    */
    $stmt->bindValue(':date', $service_date, PDO::PARAM_STR);
    $stmt->bindValue(':start_time', $start_time, PDO::PARAM_STR);
    $stmt->bindValue(':end_time', $end_time, PDO::PARAM_STR);
    $stmt->bindValue(':service_type', $service_type, PDO::PARAM_STR);
    $stmt->bindValue(':owner_city', $owner_city, PDO::PARAM_STR);   
    $stmt->bindValue(':id', $id, PDO::PARAM_INT); 

    //$stmt->bindValue(':pet_name', $pet_name, PDO::PARAM_STR);

    // Tente executar a consulta e verificar se a execução foi bem sucedida
    if ($stmt->execute()) {
        $availableProviders = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
        if (empty($availableProviders)) {
            $_SESSION['msg_no_providers'] = "No available providers that meet your needs. Choose another date or time.";
        }
        $_SESSION['availableProviders'] = $availableProviders; // Store in session
        // Redirect to bookingRequest.php with POST data
        echo '<form id="redirectForm" action="../views/bookingRequest.php" method="post">';
        echo '<input type="hidden" name="pet_name" value="' . htmlspecialchars(implode(", ", $pet_names)) . '">';
        echo '<input type="hidden" name="service_type" value="' . htmlspecialchars($service_type) . '">';
        echo '<input type="hidden" name="date" value="' . htmlspecialchars($service_date) . '">';
        echo '<input type="hidden" name="starttime" value="' . htmlspecialchars($start_time) . '">';
        echo '<input type="hidden" name="endtime" value="' . htmlspecialchars($end_time) . '">';
        echo '<input type="hidden" name="location" value="' . htmlspecialchars($location) . '">';
        echo '<input type="hidden" name="photo_consent" value="' . htmlspecialchars($photo_consent) . '">';
        echo '<input type="hidden" name="review_consent" value="' . htmlspecialchars($review_consent) . '">';
        echo '</form>';
        echo '<script>document.getElementById("redirectForm").submit();</script>';
    } else {
        echo "Erro na execução da consulta.";
        header('Location: ../views/bookingRequest.php');
    }
    

} catch (PDOException $e) {
    // Tratamento de erro
    echo "Erro de conexão: " . $e->getMessage();
    //header('Location: bookingRequest.php');
}
//header('Location: bookingRequest.php');

?>