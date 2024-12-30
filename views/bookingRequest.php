<?php
session_start();

$id = $_SESSION['id'];
$address = $_SESSION['address'];

if (isset($_SESSION['availableProviders'])) {
    $availableProviders = $_SESSION['availableProviders'];
}

if (isset($_SESSION['booking_data'])) {
    $booking_data = $_SESSION['booking_data'];
}

// Only unset $_SESSION['availableProviders'] when the form is resubmitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['availableProviders']);
}

// Verify login in state, if not logged in, redirect to login page.
if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    $_SESSION['msg_error'] = "You must be logged in to access this page!";
    header('Location: login.php');
    exit();
}

require_once('../database/init.php');
require_once('../database/bookings.php');
require_once('../database/pet.php');

try {
    $bookings = getFutureBookings($id);
    $pets = getPets($id);
    $has_pets = !empty($pets);
} catch (PDOException $e) {
    $_SESSION['msg_error'] = "Connection Error.";
    exit();
}

include('../templates/header_tpl.php');
?>
<main id="bookingcontent">
    <?php if ($has_pets): ?>
        <section id="addBooking">
            <section class="error-messages"><?php if (isset($_SESSION['msg_error'])) : ?><p class="msg_error"><?php echo $_SESSION['msg_error']; ?></p><?php unset($_SESSION['msg_error']); ?><?php endif; ?></section>
            <?php $selected_service_type = $_GET['service_type'] ?? ''; // Get the service type from the query parameter, if available
            ?>
            <form action="../actions/action_findProviders.php" method="post">
                <fieldset>
                    <legend>Booking</legend>
                    <div class="form-group">
                        <p><label>Pet's name: <span class="required">*</span> </label></p>
                        <div id="pet-selection"><?php foreach ($pets as $pet): ?><label><input type="checkbox" name="pet_name[]" value="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-checkbox"><?php echo htmlspecialchars($pet['name']); ?></label><?php endforeach; ?></div>
                    </div>
                    <div class="form-group">
                        <p><label>Service Type: <span class="required">*</span> </label></p>
                        <label for="petwalking"><input type="radio" id="petwalking" name="service_type" value="walking" required="">
                            Pet Walking</label> <label for="petsitting"><input type="radio"
                                id="petsitting" name="service_type" value="sitting" required="">
                            Pet Sitting</label>
                        <div class="form-group">
                            <p><label>Pick-Up and Drop-Off Location:<span class="required">*</span></label></p>
                            <label><input type="radio" name="location" id="myPlace" value="myplace" required=""> My Place</label> <label><input type="radio"
                                    name="location" id="providersPlace" value="providersplace"
                                    required=""> Pet Sitter/Walker's Place</label> <label><input type="radio" name="location" id="otherLocation" value="other" required=""> Other Location</label>
                            <div id="otherLocationDiv">
                                <textarea name="other_address" id="other-address" rows="4" cols="10" placeholder="Enter address here... (if 'Other Location' is selected)"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <p><label>Date: <span class="required">*</span></label></p>
                            <input name="date" id="date" type="date" required="">
                        </div>
                        <div class="form-group">
                            <p><label>Start Time: <span class="required">*</span> </label></p>
                            <input name="starttime" id="starttime" type="time" required="">
                        </div>
                        <div class="form-group">
                            <p><label for="endtime">End time: <span class="required">*</span></label></p>
                            <input name="endtime" id="endtime" type="time" required="">
                        </div>
                        <div class="form-group"><label><input type="checkbox" name="photo_consent" value='YES'> I allow PetPatrol to take pictures of
                                my pet during the walks for social media.</label><br>
                            <label><input type="checkbox" name="review_consent" value="YES"> I
                                allow PetPatrol to publish my reviews and display them on the
                                website.</label><br>
                        </div>
                        <input type="submit" value="Search for Available Pet Walkers/Pet Sitters">
                    </div>
                </fieldset>
            </form>
        </section>
        <section id="availableProviders">
            <div id="priceTable">
                <h3>Services Price Table</h3>
                <table class="price-table">
                    <thead>
                        <tr>
                            <th class="tg-0lax">Pet Sitting</th>
                            <th class="tg-0lax">Pet Walking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tg-0lax">15€/hour</td>
                            <td class="tg-0lax"><span style="font-weight:400;font-style:normal">10€/hour</span></td>
                        </tr>
                    </tbody>
                </table>
                <p class="small">Prices are per pet.</p>
            </div>
            <?php if (isset($_SESSION['msg_no_providers'])) : ?>
                <p class="msg_error"><?php echo $_SESSION['msg_no_providers']; ?></p>
                <?php unset($_SESSION['msg_no_providers']); ?>
            <?php endif; ?>
            <?php if (!empty($availableProviders)) : ?>
                <h2>Available Pet Walkers/Pet Sitters at <?= $availableProviders[0]['schedule_day_week'] ?></h2> <!-- Formulário com select para escolher o provider -->
                <form action="../actions/action_addBooking.php" method="post">
                    <div class="form-group">
                        <label>
                            <p>Select a Pet Walker/Pet Sitter:</p><span class="required">*</span>
                        </label>
                        <select name="provider_id" id="provider_selection" required>
                            <option value="">Select a provider</option>
                            <?php foreach ($availableProviders as $provider): ?>
                                <option value="<?= htmlspecialchars($provider['provider_id']) ?>">
                                    <?= htmlspecialchars($provider['provider_name']) ?> -
                                    <?= htmlspecialchars($provider['provider_phone_number']) ?> -
                                    <?= htmlspecialchars($provider['provider_address']) ?> -
                                    Rating: <?= $provider['provider_avg_rating'] !== null ? htmlspecialchars($provider['provider_avg_rating']) : 'No reviews yet.' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div><!-- Campos ocultos com os dados da reserva -->
                    <input type="hidden" name="service_type" value="<?= htmlspecialchars($booking_data['service_type'] ?? '') ?>">
                    <input type="hidden" name="pet_name" value="<?= htmlspecialchars($booking_data['pet_name'] ?? '') ?>">
                    <input type="hidden" name="date" value="<?= htmlspecialchars($booking_data['date'] ?? '') ?>">
                    <input type="hidden" name="location" value="<?= htmlspecialchars($booking_data['location'] ?? '') ?>">
                    <input type="hidden" name="other_address" value="<?= htmlspecialchars($booking_data['other_address'] ?? '') ?>">
                    <input type="hidden" name="starttime" value="<?= htmlspecialchars($booking_data['starttime'] ?? '') ?>">
                    <input type="hidden" name="endtime" value="<?= htmlspecialchars($booking_data['endtime'] ?? '') ?>">
                    <input type="hidden" name="photo_consent" value="<?= htmlspecialchars($booking_data['photo_consent'] ?? 'NO') ?>">
                    <input type="hidden" name="review_consent" value="<?= htmlspecialchars($booking_data['review_consent'] ?? 'NO') ?>">
                    <button type="submit">Book</button>
                </form>
            <?php else: ?>
                <p>Choose one option to check for available providers.</p>
            <?php endif; ?>
        </section>
        <section id="scheduledServices">
            <h2>Scheduled Bookings</h2>
            <?= $error_msg ? '<p class="msg_error">' . $error_msg . '</p>' : '' ?>
            <div class="scrollable-bookings">
                <?php if (!empty($bookings)) : ?>
                    <?php foreach ($bookings as $booking) : ?>
                        <article class="booking-info">
                            <div class="booking-details">
                                <p class="booking-title">Date and Time:</p>
                                <p class="booking-desc"><?php echo htmlspecialchars($booking['date'] . ', ' . $booking['start_time'] . ' - ' . $booking['end_time']); ?></p>
                                <p class="booking-title">Address:</p>
                                <p class="booking-desc"><?php echo htmlspecialchars($booking['address'] . ', ' . ucfirst($booking['owner_city'])); ?></p>
                                <p class="booking-title">Service Type:</p>
                                <p class="booking-desc"><?php echo htmlspecialchars(ucfirst($booking['service_type'])); ?></p>
                                <p class="booking-title">Pet:</p>
                                <p class="booking-desc"><?php echo htmlspecialchars($booking['pet_name']); ?></p>
                                <p class="booking-title">Provider's Name:</p>
                                <p class="booking-desc"><?php echo htmlspecialchars($booking['provider_name']); ?></p>
                                <p class="booking-title">Provider's Rating:</p>
                                <p class="booking-desc"><?php echo $booking['provider_rating'] !== null ? htmlspecialchars(ucfirst($booking['provider_rating'])) : 'No reviews yet.'; ?></p>
                                <a class="message-button" href="../views/messages.php?provider=<?= $booking['provider_id'] ?>&amp;owner=<?= $id ?>">MESSAGE</a>
                            </div>
                            <img src="../images/uploads/<?= htmlspecialchars($booking['pet_picture']) ?>" alt="<?= htmlspecialchars($booking['pet_name']) ?>" class="pet-profile-picture">
                        </article>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No bookings scheduled.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php else: ?>
        <p id="nopets">
            No pets associated with your account.
            Please <a href="account.php#addPetSection"> add a pet</a> to continue.
        </p>
    <?php endif; ?>
</main>
<?php include('../templates/footer_tpl.php'); ?>