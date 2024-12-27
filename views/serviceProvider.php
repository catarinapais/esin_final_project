<?php
session_start();
$id = $_SESSION['id'];
$email = $_SESSION['email'];

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
  $_SESSION['msg_error'] = "You must be logged in to access this page!";
  header('Location: login.php');
  exit();
}

function calculateWeekRange($week_offset = 0)
{
  $current_date = new DateTime();

  $current_date->modify('Monday this week'); // Move to the start of the current week (Monday)
  $current_date->modify("+$week_offset week"); // week offset (get)
  $week_start = $current_date->format('Y-m-d'); // start date - monday
  $current_date->modify('+6 days'); // end date - sunday
  $week_end = $current_date->format('Y-m-d');
  return [
    'week_start' => $week_start,
    'week_end' => $week_end,
  ];
}


//função para construir a tabela com a query
function makeAvailabilityTable($schedule) {
  // Map days of the week to columns
  $dayToColumn = [
    'Monday' => 1,
    'Tuesday' => 2,
    'Wednesday' => 3,
    'Thursday' => 4,
    'Friday' => 5,
    'Saturday' => 6,
    'Sunday' => 7,
  ];

  echo '<thead>
      <tr>
          <th class="tg-header">Time</th>
          <th class="tg-header">Monday</th>
          <th class="tg-header">Tuesday</th>
          <th class="tg-header">Wednesday</th>
          <th class="tg-header">Thursday</th>
          <th class="tg-header">Friday</th>
          <th class="tg-header">Saturday</th>
          <th class="tg-header">Sunday</th>
      </tr>
  </thead>
  <tbody>';

  $rows = 16; // from 6am to 10pm
  $columns = 8; // time + all weekdays
  $time = 6; // start time

  for ($i = 0; $i < $rows; $i++) {
    echo '<tr>';
    for ($j = 0; $j < $columns; $j++) {
      if ($j == 0) {
        echo '<td class="tg-top">' . $time . ':00 - ' . ++$time . ':00</td>';
      } else {
        $class = 'tg-all';

        foreach ($schedule as $entry) {
          $date = $entry['day_week']; // Full date (YYYY-MM-DD)
          $startTime = (int)$entry['start_time'];
          $endTime = (int)$entry['end_time'];

          // Determine the day of the week from the full date
          $dateObject = new DateTime($date);
          $dayOfWeek = $dateObject->format('l'); // Converts to "Monday", "Tuesday", etc.

          // Map the day to the correct column
          $columnIndex = $dayToColumn[$dayOfWeek] ?? -1;

          // Calculate the row indices for the time
          $firstRow = $startTime - 6;
          $lastRow = $endTime - 6 - 1;

          // Check if the current cell matches the schedule entry
          if ($j == $columnIndex && $i >= $firstRow && $i <= $lastRow) {
            $class = 'tg-available';
            break;
          }
        }

        echo '<td class="' . $class . '"></td>';
      }
    }
    echo '</tr>';
  }
  echo '</tbody>';
}


// get week offset from the url
$week_offset = isset($_GET['week_offset']) ? (int)$_GET['week_offset'] : 0;
// get week range (using the week offset)
$week_range = calculateWeekRange($week_offset);
// start and end dates
$week_start = $week_range['week_start'];
$week_end = $week_range['week_end'];

require_once('../database/init.php');
require_once('../database/bookings.php');
require_once('../database/schedule.php');

try { // try catch for error handling
  $bookings = getFutureServices($id); // get future services
  $schedule = getSchedule($id, $week_start, $week_end); // get schedule for the week
} catch (Exception $e) {
  $error_msg = $e->getMessage(); // ir buscar a mensagem de erro e guardar em $error_msg
}

include('../templates/header_tpl.php');
?>
<main class="mainContent">
  <section id="scheculeForm">
    <section class="error-messages">
      <?php if (isset($_SESSION['msg_error'])) : ?>
        <p class="msg_error"><?php echo $_SESSION['msg_error']; ?></p>
        <?php unset($_SESSION['msg_error']); ?>
      <?php endif; ?>
    </section>
    <section class="success-messages">
      <?php if (isset($_SESSION['msg_success'])) : ?>
        <p class="msg_success"><?php echo $_SESSION['msg_success']; ?></p>
        <?php unset($_SESSION['msg_success']); ?>
      <?php endif; ?>
    </section>
    <?php if (isset($_SESSION['iban'])) : // if the person is a service provider
    ?>
      <form action="../actions/action_addAvailability.php" method="POST">
        <!--post: enviar informação (encriptada)-->
        <!--get: receber informação (envia pelo url)-->
        <fieldset>
          <legend>Schedule</legend>
          <div class="form-group">
            <p>Service Type: </p>

            <?php if ($_SESSION['service_type'] === 'sitting' || $_SESSION['service_type'] === 'both') : ?>
              <label><input type="checkbox" name="service_type[]" value="sitting">Pet Sitting</label>
            <?php endif; ?>

            <?php if ($_SESSION['service_type'] === 'walking' || $_SESSION['service_type'] === 'both') : ?>
              <label><input type="checkbox" name="service_type[]" value="walking">Pet Walking</label>
            <?php endif; ?>

          </div>

          <div class="form-group">
            <p>Date: <input type="date" name="serviceDate" required="required"></p>
          </div>

          <div class="form-group">
            <p>Start Time: <input type="time" name="startTime" required="required"></p>
          </div>

          <div class="form-group">
            <p>End Time: <input type="time" name="endTime" required="required"></p>
          </div>

          <input type="submit" value="Add Availability">
          <p class="note">Please note: If you input non-rounded hours, they will be rounded down to the previous hour.</p>
        </fieldset>
      </form>

  </section>

  <section id="availability">
    <h2>Scheduled Availability</h2>
    <section id="week-navigation">
      <a href="?week_offset=<?php echo $week_offset - 1; ?>">&lt;</a>
      <p><?php echo $week_start; ?> to <?php echo $week_end; ?></p>
      <a href="?week_offset=<?php echo $week_offset + 1; ?>">&gt;</a>
    </section>
    <table class="availabilityTimetable">
      <?php makeAvailabilityTable($schedule); ?>
    </table>
  </section>
  <section id="scheduledServices">
    <h2>Scheduled Services</h2>
    <?= $error_msg ? '<p class="msg_error">' . $error_msg . '</p>' : '' ?>
    <div class="scrollable-bookings">
      <?php if (!empty($bookings)) : ?>
        <?php foreach ($bookings as $booking) : ?>
          <article class="booking-info">
            <div class="booking-details">
              <p class="booking-title">Date and Time: </p>
              <p class="booking-desc"><?php echo htmlspecialchars($booking['date'] . ', ' . $booking['start_time'] . ' - ' . $booking['end_time']); ?></p>
              <p class="booking-title">Address: </p>
              <p class="booking-desc"><?php echo htmlspecialchars($booking['address'] . ', ' . ucfirst($booking['owner_city'])); ?></p>
              <p class="booking-title">Service Type: </p>
              <p class="booking-desc"><?php echo htmlspecialchars(ucfirst($booking['service_type'])); ?></p>
              <p class="booking-title">Animal's Name: </p>
              <p class="booking-desc"><?php echo htmlspecialchars($booking['pet_name']); ?></p>
              <p class="booking-title">Owner's Name: </p>
              <p class="booking-desc"><?php echo htmlspecialchars($booking['owner_name']); ?></p>
              <p class="booking-title">Medical Needs: </p>
              <p class="booking-desc"><?php echo htmlspecialchars(ucfirst($booking['medical_needs'])); ?></p>
              <a class="message-button" href="../views/messages.php?provider=<?= $id ?>&owner=<?= $booking['owner_id'] ?>">MESSAGE</a>
            </div>
            <img src="../images/uploads/<?= htmlspecialchars($booking['pet_picture']) ?>"
              alt="<?= htmlspecialchars($booking['pet_name']) ?>"
              class="pet-profile-picture">
          </article>
        <?php endforeach; ?>
      <?php else : ?>
        <p>No services scheduled.</p>
      <?php endif; ?>
    </div>
  </section>
<?php else: ?>
  <p id="notAProvider">You are not entitled to provide services.</p>
<?php endif; ?>
</main>

<?php include('../templates/footer_tpl.php'); ?>