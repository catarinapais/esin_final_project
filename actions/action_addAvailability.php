<?php
session_start();

$user_id = $_SESSION['id'];
$provider_service_type = $_SESSION['service_type'];
//$provider_service_type = 'both'; //hard-coded mas era só para testar
// TODO: tirar esta linha

$service_type = $_POST['service_type'];
$date = $_POST['serviceDate'];
$start_time = $_POST['startTime'];
$end_time = $_POST['endTime'];

if (empty($service_type)) {
    $_SESSION['msg_error'] = "Select the available service types.";
    header('Location: ../serviceProvider.php');
    exit;
} else if ($provider_service_type != 'both' && $service_type != $provider_service_type) {
    $_SESSION['msg_error'] = "Service type must match your service type.";
    header('Location: ../serviceProvider.php');
    exit;
} else if ($date < date('Y-m-d')) {
    $_SESSION['msg_error'] = "Service date must be in the future.";
    header('Location: ../serviceProvider.php');
    exit;
} else if ($start_time >= $end_time) {
    $_SESSION['msg_error'] = "Start time must be before end time.";
    header('Location: ../serviceProvider.php');
    exit;
} else if ($start_time < '06:00' || $end_time > '22:00') {
    $_SESSION['msg_error'] = "Service time must be between 06:00 and 22:00.";
    header('Location: ../serviceProvider.php');
    exit;
} 

try {
    $dbh = new PDO('sqlite:../database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare('
            INSERT INTO Schedule 
            (day_week, start_time, end_time, service_provider) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $date,
            $start_time,
            $end_time,
            $user_id
        ]);
        $_SESSION['msg_success'] = "Availability added successfully.";
} catch (PDOException $e) {
    $_SESSION['msg_error'] = "Error adding availability. Please try again.";
    exit();
}

header('Location: ../serviceProvider.php');
?>