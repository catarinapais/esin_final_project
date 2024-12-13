<?php
session_start();

$availableProviders = isset($_SESSION['availableProviders']) ? $_SESSION['availableProviders'] : [];
unset($_SESSION['availableProviders']); // Clear the session data after retrieving it

$id = $_SESSION['id'];
$address = $_SESSION['address'];

// Conectar ao banco de dados
try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para ir buscar os pets do user
    $stmt = $dbh->prepare('SELECT * FROM Pet WHERE owner = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $has_pets = !empty($pets);

    // Verificar se o form foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ir buscar dados do form
        $pet_name = $_POST['pet_name'] ?? null;
        $service_type = $_POST['service_type'] ?? null;
        $location = $_POST['location'] ?? null;
        $date = $_POST['date'] ?? null;
        $start_time = $_POST['starttime'] ?? null;
        $end_time = $_POST['endtime'] ?? null;
        $photo_consent = $_POST['photo_consent'] ?? null;
        $duration = $end_time - $start_time;
        $service_provider_id = 1; //TODO: alterar isto quando a catarina puser a dar a cena dos providers no form, tou a assumir que vai retornar o id, se for o nome devemosmudar!!

        $start = new DateTime($start_time);
        $end = new DateTime($end_time);

        // Calcular a diferença
        $interval = $start->diff($end);

        // Obter a duração em minutos
        $duration = ($interval->h * 60) + $interval->i; // Total de minutos

        // Definir as taxas por minuto
        $rate_per_minute_walking = 0.50; // Taxa para Pet Walking
        $rate_per_minute_sitting = 0.75;  // Taxa para Pet Sitting

        // Determinar a taxa com base no tipo de serviço
        if ($service_type === 'walking') {
            $payment = $duration * $rate_per_minute_walking; // Cálculo para Walking
        } elseif ($service_type === 'sitting') {
            $payment = $duration * $rate_per_minute_sitting; // Cálculo para Sitting
        }

        // Se a localização for "myplace", buscar a morada do user  a partir do id e passa-a para a variável location
        if ($location === 'myplace') {
            $location = $address; // vai buscar morada do user
        } elseif ($location === 'providersplace') {
            // Se a localização for "Pet Sitter/Walker's Place", buscar a morada do provider
            if ($service_provider_id) {
                $stmt = $dbh->prepare('
                SELECT address FROM Person 
                WHERE id = (SELECT person FROM ServiceProvider WHERE person = :provider_id)
            ');
                $stmt->bindValue(':provider_id', $service_provider_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $location = $result['address'] ?? null; // vai buscar morada do provider
            }
            // Se a localização for "other", vai buscar a morada que o user inseriu no text area
        } elseif ($location === 'other') {
            $location = $_POST['other_address'] ?? null; // vai buscar morada que está no textarea
        }


        // Inserir dados na tabela Booking
        $stmt = $dbh->prepare('
            INSERT INTO Booking 
            (date, start_time, end_time, duration, address_collect, photo_consent, provider, type, pet, payment) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $date,
            $start_time,
            $end_time,
            $duration,
            $location,
            $photo_consent,
            $service_provider_id,
            $service_type,
            $pet_name,
            $payment
        ]);
        echo "<p>Booking successfully submitted!</p>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>