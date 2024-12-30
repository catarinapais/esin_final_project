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
require_once('../database/person.php');

try {
    // query for getting available providers - AvailableProviders
    $availableProviders = searchAvailableProviders($service_date, $start_time, $end_time, $service_type, $owner_city, $id);
    
    if (empty($availableProviders)) {
        $_SESSION['msg_no_providers'] = "No available providers that meet your needs. Choose another date or time.";
        $_SESSION['availableProviders'] = $availableProviders;
        header('Location: ../views/bookingRequest.php');
    } else {
        $_SESSION['availableProviders'] = $availableProviders; // Store in session
        $_SESSION['booking_data'] = [
            'pet_name' => implode(", ", $pet_names),
            'service_type' => $service_type,
            'date' => $service_date,
            'starttime' => $start_time,
            'endtime' => $end_time,
            'location' => $location,
            'photo_consent' => $photo_consent,
            'review_consent' => $review_consent
        ];
        header('Location: ../views/bookingRequest.php');
        exit;
    }
} catch (PDOException $e) {
    // Tratamento de erro
    $_SESSION['msg_error'] = "Error finding providers. Please try again.";
    header('Location: ../views/bookingRequest.php');
    exit;
}

?>