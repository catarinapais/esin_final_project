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

// Verify login in state, if not logged in, redirect to login page.
if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    $_SESSION['msg_error'] = "You must be logged in to access this page!";
    header('Location: login.php');
    exit();
}

require_once('../database/init.php');

try {
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


include('../templates/header_tpl.php');
?>
<main id="bookingcontent">
    <section class="error-messages">
        <?php if (isset($_SESSION['msg_error'])) : ?>
            <p class="msg_error"><?php echo $_SESSION['msg_error']; ?></p>
            <?php unset($_SESSION['msg_error']); ?>
        <?php endif; ?>
    </section>
    <?php if ($has_pets): ?>

        <?php
        $selected_service_type = $_GET['service_type'] ?? ''; // Get the service type from the query parameter, if available
        ?>
        <form action="../actions/action_findProviders.php" method="post">
            <fieldset>
                <legend>Booking</legend>

                <div class="form-group">
                    <label for="pet-selection">
                        <p>Pet's name:</p><span class="required">*</span>
                    </label>
                    <div id="pet-selection">
                        <?php foreach ($pets as $pet): ?>
                            <label>
                                <input type="checkbox" name="pet_name[]" value="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-checkbox">
                                <?php echo htmlspecialchars($pet['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="service_type">
                        <p>Service Type:</p><span class="required">*</span>
                    </label>
                    <label for="petwalking">
                        <input type="radio" id="petwalking" name="service_type" value="walking"
                            <?= $selected_service_type === 'petwalking' ? 'checked' : '' ?> required> <!-- espero não ter estragado-->
                        Pet Walking
                    </label>
                    <label for="petsitting">
                        <input type="radio" id="petsitting" name="service_type" value="sitting"
                            <?= $selected_service_type === 'petsitting' ? 'checked' : '' ?> required>
                        Pet Sitting
                    </label>

                    <div class="form-group">
                        <label for="location">
                            <p>Pick-Up and Drop-Off Location:</p><span class="required">*</span>
                        </label>
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
                    </div>

                    <div class="form-group">
                        <label for="date">
                            <p>Date:</p><span class="required">*</span>
                        </label>
                        <input name="date" id="date" type="date" required>
                    </div>

                    <div class="form-group">
                        <label for="starttime">
                            <p>Start Time:</p><span class="required">*</span>
                        </label>
                        <input name="starttime" id="starttime" type="time" required>
                    </div>

                    <div class="form-group">
                        <label for="endtime">
                            <p>End time:</p><span class="required">*</span>
                        </label>
                        <input name="endtime" id="endtime" type="time" required>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="photo_consent" value="YES">
                            I allow PetPatrol to take pictures of my pet during the walks for social media.
                        </label><br>

                        <label>
                            <input type="checkbox" name="review_consent" value="YES">
                            I allow PetPatrol to publish my reviews and display them on the website.
                        </label><br>
                    </div>

                    <input type="submit" value="Search for Available Pet Walkers/Pet Sitters">
            </fieldset>
        </form>

        <section id="availableProviders">
            <?php if (isset($_SESSION['msg_no_providers'])) : ?>
                <p class="msg_error"><?php echo $_SESSION['msg_no_providers']; ?></p>
                <?php unset($_SESSION['msg_no_providers']); ?>
            <?php endif; ?>
            <?php if (!empty($availableProviders)) : ?>
                <h2>Available Pet Walkers/Pet Sitters at <?= $availableProviders[0]['day_week'] ?></h2>
                <?php foreach ($availableProviders as $provider): ?>
                    <article class="eachProvider">
                        <h3><?= $provider['provider_name'] ?></h3>
                        <p><?php echo htmlspecialchars($provider['provider_phone_number']); ?></p>
                        <p><?php echo htmlspecialchars($provider['provider_email']); ?></p>
                        <p>Rating: <?php echo htmlspecialchars($provider['provider_avg_rating']); ?></p>

                        <!-- Formulário oculto para redirecionar para addbooking.php -->
                        <form action="../actions/action_addBooking.php" method="post">
                            <input type="hidden" name="provider_id" value="<?= htmlspecialchars($provider['provider_id']) ?>">
                            <input type="hidden" name="service_type" value="<?= htmlspecialchars($_POST['service_type'] ?? '') ?>">
                            <input type="hidden" name="pet_name" value="<?= htmlspecialchars($_POST['pet_name'] ?? '') ?>">
                            <input type="hidden" name="date" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
                            <input type="hidden" name="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                            <input type="hidden" name="starttime" value="<?= htmlspecialchars($_POST['starttime'] ?? '') ?>">
                            <input type="hidden" name="endtime" value="<?= htmlspecialchars($_POST['endtime'] ?? '') ?>">
                            <input type="hidden" name="photo_consent" value="<?= htmlspecialchars($_POST['photo_consent'] ?? 'NO') ?>">
                            <input type="hidden" name="review_consent" value="<?= htmlspecialchars($_POST['review_consent'] ?? 'NO') ?>">

                            <button type="submit">Book</button>
                        </form>

                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p> Choose one option to check for available providers. </p>
            <?php endif; ?>
        </section>

        <!--restrições a ter em conta ao mostrar os providers disponíveis:
        * verificar service type da reserva e o do provider
        * verificar se o provider tem disponibilidade nesse dia  (day/day_week, ver qual)
        * verificar se o start time e o end time incluem dentro o schedule do provider
        * garantir que o provider não tem mais bookings nesse momento
        * verificar que o owner e o provider são da mesma cidade-->

    <?php else: ?>
        <p id="nopets">
            No pets associated with your account.
            Please <a href="account.php#addPetSection"> add a pet</a> to continue.
        </p>
    <?php endif; ?>


</main>

<?php include('../templates/footer_tpl.php'); ?>