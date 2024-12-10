<?php
session_start();

$owner_id = $_SESSION['id'];
$owner_city = $_SESSION['city']; 

$pet_name = $_POST['pet_name'];
$service_type = $_POST['service_type']; // walking ou sitting
$service_date = $_POST['date'];
$start_time = $_POST['starttime'];
$end_time = $_POST['endtime'];

try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // query for getting available providers - AvailableProviders
    // that are not doing other services at the same time - FreeProviders
    $stmt = $dbh->prepare(
        "WITH FreeProviders AS ( 
            SELECT  
                ServiceProvider.person AS provider_id, 
                Person.name AS provider_name, 
                Person.phone_number AS provider_phone_number, 
                ServiceProvider.avg_rating AS provider_avg_rating 
            FROM ServiceProvider 
            JOIN Person ON Person.id = ServiceProvider.person 
            LEFT JOIN Booking 
                ON Booking.provider = ServiceProvider.person  
                AND Booking.date = :date 
                AND (
                    Booking.start_time < :end_time AND Booking.end_time > :start_time
                ) 
            WHERE Booking.id IS NULL 
        ), 
        AvailableProviders AS ( 
            SELECT 
                Schedule.service_provider AS provider_id, 
                Schedule.day_week AS day_week, 
                Schedule.start_time AS schedule_start_time, 
                Schedule.end_time AS schedule_end_time 
            FROM Schedule 
            JOIN ServiceProvider ON ServiceProvider.person = Schedule.service_provider 
            JOIN Person ON Person.id = ServiceProvider.person 
            WHERE 
                Schedule.day_week = strftime('%w', :date) -- Extract day of the week
                AND Schedule.start_time <= :start_time 
                AND Schedule.end_time >= :end_time 
                AND (ServiceProvider.service_type = :service_type OR ServiceProvider.service_type = 'both') 
                AND Person.city = :owner_city
        )
        SELECT 
            FreeProviders.provider_name, 
            FreeProviders.provider_phone_number, 
            FreeProviders.provider_avg_rating, 
            AvailableProviders.day_week, 
            AvailableProviders.schedule_start_time, 
            AvailableProviders.schedule_end_time 
        FROM FreeProviders 
        JOIN AvailableProviders ON FreeProviders.provider_id = AvailableProviders.provider_id;" 
    ); // só falta verificar que o provider não tem nenhum serviço marcado para a mesma hora
    $stmt->bindValue(':date', $service_date, PDO::PARAM_INT);
    $stmt->bindValue(':start_time', $start_time, PDO::PARAM_INT);
    $stmt->bindValue(':end_time', $end_time, PDO::PARAM_INT);
    $stmt->bindValue(':service_type', $service_type, PDO::PARAM_STR);
    $stmt->bindValue(':owner_city', $owner_city, PDO::PARAM_STR);
    //$stmt->bindValue(':pet_name', $pet_name, PDO::PARAM_STR);

    // Tente executar a consulta e verificar se a execução foi bem sucedida
    if ($stmt->execute()) {
        $availableProviders = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
        if (empty($availableProviders)) {
            $_SESSION['msg_no_providers'] = "No available providers that meet your needs. Choose another date or time.";
        }
        $_SESSION['availableProviders'] = $availableProviders; // Store in session
        header('Location: bookingRequest.php');
    } else {
        echo "Erro na execução da consulta.";
        header('Location: bookingRequest.php');
    }

} catch (PDOException $e) {
    // Tratamento de erro
    echo "Erro de conexão: " . $e->getMessage();
    //header('Location: bookingRequest.php');
}
//header('Location: bookingRequest.php');

?>