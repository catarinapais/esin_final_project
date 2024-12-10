<?php
session_start();

$availableProviders = isset($_SESSION['availableProviders']) ? $_SESSION['availableProviders'] : [];
unset($_SESSION['availableProviders']); // Clear the session data after retrieving it

// Conectar ao banco de dados
try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter o ID da URL
    $person_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;  // Garantir que o ID seja um número inteiro

    // Consulta para ir buscar os pets do user
    $stmt = $dbh->prepare('SELECT * FROM Pet WHERE owner = :owner_id');
    $stmt->bindValue(':owner_id', $person_id, PDO::PARAM_INT);
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
            $stmt = $dbh->prepare('SELECT address FROM Person WHERE id = :person_id');
            $stmt->bindValue(':person_id', $person_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $location = $result['address'] ?? null;
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Care Booking</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
</head>

<body>
<?php
    include('header.php');
    ?>
    <main id="bookingcontent">
        <?php if ($has_pets): ?>
            <form action="" method="post">
                <fieldset>
                    <legend>Booking</legend>
                    <p>Pet's name:</p>
                    <!--Pus o select a mostrar os nomes dos pets do user -->
                    <select name="pet_name" id="pet-name">
                        <?php foreach ($pets as $pet): ?>
                            <option value="<?php echo htmlspecialchars($pet['name']); ?>">
                                <?php echo htmlspecialchars($pet['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <br>

                    <p>Service Type:</p>
                    <label for="petwalking">
                        <input type="radio" id="petwalking" name="service_type" value="walking" required>
                        Pet Walking
                    </label>
                    <br>
                    <label for="petsitting">
                        <input type="radio" id="petsitting" name="service_type" value="sitting" required>
                        Pet Sitting
                    </label>

                    <p>Pick-Up and Drop-Off Location:</p>
                    <label>
                        <input type="radio" name="location" id="myPlace" value="myplace" required> My Place
                    </label>
                    <label>
                        <input type="radio" name="location" id="providersPlace" value="providersplace" required> Pet Sitter/Walker's Place
                    </label>
                    <label>
                        <input type="radio" name="location" id="otherLocation" value="other" required> Other Location
                    </label>

                    <div id="otherLocationDiv">
                        <textarea name="other_address" id="other-address" rows="3" cols="30" placeholder="Enter address here... (only if 'Other Location' is selected)"></textarea>
                    </div>

                    <p>Date:</p>
                    <input name="date" id="date" type="date" required>
                    <p>Start Time:</p>
                    <input name="starttime" id="starttime" type="time" required>

                    <p>End time:</p>
                    <input name="endtime" id="endtime" type="time" required>
                    <br>
                    <input type="submit" value="Search for Available Pet Walkers/Pet Sitters">

                    <p>Photo Consent:</p>
                    <label>
                        <input type="radio" name="photo_consent" value="yes" required> Yes
                    </label>
                    <label>
                        <input type="radio" name="photo_consent" value="no" required> No
                    </label>
                    <br>
                    <input type="submit" value="Calculate">
                </fieldset>
            </form>
        <?php else: ?>
            <p id="nopets">No pets associated with your account. Please add pets to continue.</p>
        <?php endif; ?>
        <!--restrições a ter em conta ao mostrar os providers disponíveis:
        * verificar service type da reserva e o do provider
        * verificar se o provider tem disponibilidade nesse dia  (day/day_week, ver qual)
        * verificar se o start time e o end time incluem dentro o schedule do provider
        * garantir que o provider não tem mais bookings nesse momento
        * verificar que o owner e o provider são da mesma cidade-->
        

    </main>

    <?php include('footer.php'); ?>
</body>

</html>