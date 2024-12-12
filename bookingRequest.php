<?php
session_start();


if (isset($_SESSION['availableProviders'])) {
    $availableProviders = $_SESSION['availableProviders'];
}

// Only unset $_SESSION['availableProviders'] when the form is resubmitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['availableProviders']);
}

$id = $_SESSION['id'];
$address = $_SESSION['address'];

try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para ir buscar os pets do user
    $stmt = $dbh->prepare('SELECT * FROM Pet WHERE owner = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $has_pets = !empty($pets);
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
            <form action="action_findProviders.php" method="post">
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

                    <p>Photo Consent:</p>
                    <label>
                        <input type="radio" name="photo_consent" value="yes" required> Yes
                    </label>
                    <label>
                        <input type="radio" name="photo_consent" value="no" required> No
                    </label>
                    <br>
                    <input type="submit" value="Search for Available Pet Walkers/Pet Sitters">
                </fieldset>
            </form>

            <section id="availableProviders">
                <?php if (isset($_SESSION['msg_no_providers'])) : ?>
                    <p class="msg_error"><?php echo $_SESSION['msg_no_providers']; ?></p>
                    <?php unset($_SESSION['msg_no_providers']); ?>
                <?php endif; ?>

                <?php if(!empty($availableProviders)) : ?>
                    <h2>Available Pet Walkers/Pet Sitters at <?= $availableProviders[0]['day_week'] ?></h2>
                    <?php foreach ($availableProviders as $provider): ?>
                        <article class="eachProvider">
                            <h3><?= $provider['provider_name'] ?></h3>
                            <p><?php echo htmlspecialchars($provider['provider_phone_number']); ?></p>
                            <p><?php echo htmlspecialchars($provider['provider_email']); ?></p>
                            <p>Rating: <?php echo htmlspecialchars($provider['provider_avg_rating']); ?></p>
                            <a href="bookingRequest.php?provider_id=<?php echo $provider['provider_id']; ?>">Book</a>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p> Choose one option to check for available providers. </p>
                <?php endif; ?>
            </section>

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