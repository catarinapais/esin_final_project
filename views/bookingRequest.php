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
        // Carregar o tipo de serviço previamente selecionado (se aplicável)
        $selected_service_type = $_GET['service_type'] ?? '';
        ?>

        <!-- Formulário inicial para selecionar critérios -->
        <form action="" method="post" id="searchForm">
            <fieldset>
                <legend>Booking</legend>
                <div class="form-group">
                    <label for="pet-selection"><p>Pet's name:</p><span class="required">*</span></label>
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
                        <input type="radio" id="petwalking" name="service_type" value="walking" <?= $selected_service_type === 'walking' ? 'checked' : '' ?> required>
                        Pet Walking
                    </label>
                    <label for="petsitting">
                        <input type="radio" id="petsitting" name="service_type" value="sitting" <?= $selected_service_type === 'sitting' ? 'checked' : '' ?> required>
                        Pet Sitting
                    </label>
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
                        <p>End Time:</p><span class="required">*</span>
                    </label>
                    <input name="endtime" id="endtime" type="time" required>
                </div>
                <input type="submit" name="search_providers" value="Search for Available Providers">
            </fieldset>
        </form>

        <?php
        // Processar a pesquisa de provedores disponíveis
        if (isset($_POST['search_providers'])) {
            try {
                $stmt = $dbh->prepare('SELECT * FROM Providers WHERE city = :city AND service_type = :service_type');
                $stmt->bindValue(':city', $_SESSION['city'], PDO::PARAM_STR);
                $stmt->bindValue(':service_type', $_POST['service_type'], PDO::PARAM_STR);
                $stmt->execute();
                $availableProviders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
        }
        ?>

        <?php if (!empty($availableProviders)): ?>
            <!-- Formulário para selecionar um provider e confirmar reserva -->
            <form action="../actions/action_addBooking.php" method="post">
                <fieldset>
                    <legend>Select Provider</legend>
                    <div class="form-group">
                        <label for="provider"><p>Choose a Provider:</p></label>
                        <select name="provider_id" id="provider" required>
                            <?php foreach ($availableProviders as $provider): ?>
                                <option value="<?= htmlspecialchars($provider['provider_id']) ?>">
                                    <?= htmlspecialchars($provider['provider_name']) ?> - Rating: <?= htmlspecialchars($provider['provider_avg_rating']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dados ocultos para enviar detalhes -->
                    <input type="hidden" name="service_type" value="<?= htmlspecialchars($_POST['service_type']) ?>">
                    <input type="hidden" name="pet_name" value="<?= htmlspecialchars(implode(", ", $_POST['pet_name'])) ?>">
                    <input type="hidden" name="date" value="<?= htmlspecialchars($_POST['date']) ?>">
                    <input type="hidden" name="starttime" value="<?= htmlspecialchars($_POST['starttime']) ?>">
                    <input type="hidden" name="endtime" value="<?= htmlspecialchars($_POST['endtime']) ?>">
                    <button type="submit">Confirm Booking</button>
                </fieldset>
            </form>
        <?php else: ?>
            <p>No providers available for the selected criteria.</p>
        <?php endif; ?>

    <?php else: ?>
        <p id="nopets">No pets associated with your account. Please <a href="account.php#addPetSection">add a pet</a> to continue.</p>
    <?php endif; ?>
</main>

<?php include('../templates/footer_tpl.php'); ?>
