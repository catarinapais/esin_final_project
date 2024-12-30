<?php
session_start();


$availableProviders = isset($_SESSION['availableProviders']) ? $_SESSION['availableProviders'] : [];
unset($_SESSION['availableProviders']); // Clear the session data after retrieving it

$id = $_SESSION['id'];
$address = $_SESSION['address'];

// Conectar ao banco de dados
require_once('../database/init.php');
require_once('../database/pet.php');
require_once('../database/bookings.php');
require_once('../database/person.php');

try {

    // Consulta para ir buscar os pets do user
    $stmt = $dbh->prepare('SELECT * FROM Pet WHERE owner = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $has_pets = !empty($pets);

    // Verificar se o form foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ir buscar dados do form
        $provider_id = $_POST['provider_id'] ?? null;
        $pet_name = $_POST['pet_name'] ?? null;
        $pet_names_array = explode(", ", $pet_name);
        $service_type = $_POST['service_type'] ?? null;
        $location = $_POST['location'] ?? null;
        $date = $_POST['date'] ?? null;
        $start_time = $_POST['starttime'] ?? null;
        $end_time = $_POST['endtime'] ?? null;
        $photo_consent = $_POST['photo_consent'];
        $review_consent = $_POST['review_consent'];
        $duration = $end_time - $start_time;

        $start = new DateTime($start_time);
        $end = new DateTime($end_time);

        // Calcular a diferença
        $interval = $start->diff($end);

        // Obter a duração em minutos
        $duration = ($interval->h * 60) + $interval->i; // Total de minutos

        // Definir as taxas por minuto
        $rate_per_minute_walking = 1/6; // Taxa para Pet Walking
        $rate_per_minute_sitting = 15/60;  // Taxa para Pet Sitting

        // Determinar a taxa com base no tipo de serviço
        if ($service_type === 'walking') {
            $payment = $duration * $rate_per_minute_walking; // Cálculo para Walking
        } elseif ($service_type === 'sitting') {
            $payment = $duration * $rate_per_minute_sitting; // Cálculo para Sitting
        }
        
        // Verificar o número total de pets selecionados
        $number_of_pets = count($pet_names_array);

        // Calcular o pagamento total
        $total_payment = $payment * $number_of_pets;


        // Se a localização for "myplace", buscar a morada do user  a partir do id e passa-a para a variável location
        if ($location === 'myplace') {
            $location = $address; // vai buscar morada do user
        } elseif ($location === 'providersplace') {
            // Se a localização for "Pet Sitter/Walker's Place", buscar a morada do provider
            if ($provider_id) {
                $location = getProviderPersonalInfo($provider_id)[0]['provider_address'] ?? null;
            }
            // Se a localização for "other", vai buscar a morada que o user inseriu no text area
        } elseif ($location === 'other') {
            $location = $_POST['other_address'] ?? null; // vai buscar morada que está no textarea
        }

        // Inserir dados na tabela Booking para cada pet
        foreach ($pet_names_array as $pet_name) {
            $pet = getPetByName($id, $pet_name);
            if ($pet) {
                $pet_id = $pet['id'];
                insertBooking($date, $start_time, $end_time, $duration, $location, $photo_consent, $review_consent, $provider_id, $service_type, $pet_id, $payment);
            }
        }
        $booking_id = $dbh->lastInsertId();
        $_SESSION['booking_id'] = $booking_id;
        $_SESSION["msg_success"] = "Booking successfully submitted!";
        header('Location: ../views/payment.php');
    }

// Verificar se já existe um array de pagamentos na sessão
if (!isset($_SESSION['total_payments'])) {
    $_SESSION['total_payments'] = [];
}

// Armazenar o pagamento total  (quando se selecionam varios animais) com o booking_id como chave
$_SESSION['total_payments'][$booking_id] = $total_payment;


} catch (PDOException $e) {
    $_SESSION['msg_error'] = "Error adding booking. Please try again.";
    header('Location: ../views/bookingRequest.php');
    exit();
}
?>