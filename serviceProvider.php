<?php
session_start();
$id = $_SESSION['id'];
$email = $_SESSION['email'];

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
  $_SESSION['msg_error'] = "You must be logged in to access this page!";
  header('Location: login.php');
  exit();
}


//função para construir a tabela com a query
function makeAvailabilityTable($schedule)
{


  //printing table headers
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

  $rows = 16; //from 6am to 10pm
  $columns = 8; //time + all week days
  $time = 6; //beginning of services
  
  for ($i = 0; $i < $rows; $i++) {
    echo '<tr>';
    for ($j = 0; $j < $columns; $j++) {
      if ($j == 0) {
        echo '<td class="tg-top">' . $time . ':00 - ' . ++$time . ':00</td>'; //time column
      } else {
        // Determine if this cell should be marked as available
        $class = 'tg-all';
        if (!empty($schedule)) {
          foreach ($schedule as $entry) {
            $dayOfWeek = $entry['day_week'];
            $startTime = (int)$entry['start_time'];
            $endTime = (int)$entry['end_time'];

            // Map the day of the week to column index
            switch ($dayOfWeek) {
              case 'Monday':
                $columnIndex = 1;
                break;
              case 'Tuesday':
                $columnIndex = 2;
                break;
              case 'Wednesday':
                $columnIndex = 3;
                break;
              case 'Thursday':
                $columnIndex = 4;
                break;
              case 'Friday':
                $columnIndex = 5;
                break;
              case 'Saturday':
                $columnIndex = 6;
                break;
              case 'Sunday':
                $columnIndex = 7;
                break;
              default:
                $columnIndex = -1; // Invalid day
            }

            // Calculate the row indices
            $firstRow = $startTime - 6;
            $lastRow = $endTime - 6 - 1;

            // Check if the current cell matches the schedule entry
            if ($j == $columnIndex && $i >= $firstRow && $i <= $lastRow) {
              $class = 'tg-available';
              break;
            }
          }
        }
        echo '<td class="' . $class . '"></td>'; //weekday columns
      }
    }
    echo '</tr>';
  }
  echo '</tbody>';
}

$dbh = new PDO('sqlite:database.db');
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //association fetching
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling


try { // try catch for error handling
  $stmt = $dbh->prepare(
    'SELECT * 
    FROM Schedule 
    JOIN ServiceProvider ON Schedule.service_provider = ServiceProvider.person 
    JOIN Person ON ServiceProvider.person = Person.id 
    WHERE Person.email = :email'
  ); // prepared statement
  $stmt->execute([':email' => $email]); // sem valores porque nao temos placeholders no prepared statement
  $schedule = $stmt->fetchAll(); //fetching all schedules by the user (array of arrays)
} catch (Exception $e) {
  $error_msg = $e->getMessage(); // ir buscar a mensagem de erro e guardar em $error_msg
}

include('templates/header_tpl.php');
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
    <?php if (isset($_SESSION['iban'])) : // if the person is a service provider?>
    <form action="actions/action_addAvailability.php" method="post">
      <!--post: enviar informação (encriptada)-->
      <!--get: receber informação (envia pelo url)-->
      <fieldset>
        <legend>Schedule</legend>
        <div class="form-group">
          <p>Service Type: </p>
          <label>
            <input type="checkbox" name="service_type" value="sitting">Pet Sitting
            <input type="checkbox" name="service_type" value="walking">Pet Walking
          </label>
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
      </fieldset>
    </form>
  </section>

  <section id="availability">
    <h2>Scheduled Availability</h2>
    <table class="availabilityTimetable">
      <?php makeAvailabilityTable($schedule); ?>
    </table>
  </section>
  <section id="scheduledBookings">
    <h2>Scheduled Bookings</h2>
    <?php if (!empty($bookings)) : ?>
      <?php foreach ($bookings as $booking) : ?>
        <article>
          <p>Service Type: <?php echo htmlspecialchars($booking['service_type']); ?></p>
          <p>Client Name: <?php echo htmlspecialchars($booking['name_person']); ?></p>
          <p>Animal Name: <?php echo htmlspecialchars($booking['name_animal']); ?></p>
          <p>Species: <?php echo htmlspecialchars($booking['name_species']); ?></p>
          <p>Medical Needs: <?php echo htmlspecialchars($booking['medical_needs']); ?></p>
          <p>Date: <?php echo htmlspecialchars($booking['date']); ?></p>
          <p>Time: <?php echo htmlspecialchars($booking['start_time'] . ' - ' . $booking['end_time']); ?></p>
        </article>
      <?php endforeach; ?>
    <?php else : ?>
      <p>No bookings scheduled.</p>
    <?php endif; ?>
  </section>
  <?php else: ?>
    <p id="notAProvider">You are not entitled to provide services.</p>
  <?php endif; ?>
</main>

<?php include('templates/footer_tpl.php'); ?>