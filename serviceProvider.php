<?php

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
  $email = 'johndoe@gmail.com';
  $stmt = $dbh->prepare('SELECT * FROM Schedule JOIN ServiceProvider ON Schedule.service_provider = ServiceProvider.person JOIN Person ON ServiceProvider.person = Person.id WHERE Person.email = :email'); // prepared statement
  $stmt->execute([':email' => $email]); // sem valores porque nao temos placeholders no prepared statement
  $schedule = $stmt->fetchAll(); //fetching all schedules by the user (array of arrays)
} catch (Exception $e) {
  $error_msg = $e->getMessage(); // ir buscar a mensagem de erro e guardar em $error_msg
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Service Provider</title>
  <!--<link rel="stylesheet" type="text/css" href="css/serviceProvider.css">-->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/layout.css" rel="stylesheet">
  <link href="css/responsive.css" rel="stylesheet">
</head>

<body>
  <!--TODO: só mostrar esta página se a pessoa tiver posto um IBAN no site-->
  <!--ou dizer que, se quiser fazer serviços, tem de introduzir os campos iban e service type-->
  <header id="navigationBar">
    <a href="initialPage.html">
      <div id="logo">
        <h1>Pet Patrol</h1>
        <h2>Sit and Walk</h2>
        <img src="images/logo1.png" alt="Logo of Pet Patrol">
      </div>
    </a>
    <nav id="menu">
      <input type="checkbox" id="hamburger">
      <label class="hamburger" for="hamburger"></label>
      <ul id="menuItems">
        <li><a href="bookingRequest.html">BOOK A SERVICE</a></li>
        <li><a href="serviceProvider.php">DO A SERVICE</a></li>
        <li><a href="account.html">ACCOUNT</a></li>
        <li><a href="aboutus.html">ABOUT US</a></li>
        <li class="signup"><a href="register.html">REGISTER</a></li>
        <li class="signup"><a href="login.html">LOGIN</a></li>
      </ul>
    </nav>
  </header>
  <main class="mainContent">
    <section id="scheculeForm">
      <form action="serviceProvider.php" method="post">
        <!--post: enviar informação (encriptada)-->
        <!--get: receber informação (envia pelo url)-->
        <fieldset>
          <legend>Schedule</legend>
          <p>Service Type: </p>
          <label>
            <input type="checkbox" name="serviceType" value="petSitting">Pet Sitting
            <input type="checkbox" name="serviceType" value="petWalking">Pet Walking
          </label>
          <p>Date: <input type="date" name="serviceDate" required="required"></p>
          <!--TODO: endtime > starttime e tem de estar entre 06:00 e 22:00-->
          <p>Start Time: <input type="time" name="startTime" required="required"></p>
          <p>End Time: <input type="time" name="endTime" required="required"></p>
          <input type="submit" value="Add Availability">
          <!--podemos usar submit em vez de button porque já dissemos o method no form-->
        </fieldset>
      </form>
    </section>
    <section id="scheduledBookings">
      <h2>Scheduled Bookings</h2>
      <article>
        <p>Service Type</p>
        <a href="">Name Person</a>
        <p>Name Animal</p>
        <p>Name Species</p>
        <p>Medical Needs</p>
        <p>Date and Time</p>
      </article>
    </section>
    <section id="availability">
      <h2>Scheduled Availability</h2>
      <table class="availabilityTimetable">
        <?php makeAvailabilityTable($schedule); ?>
      </table>
    </section>
  </main>
</body>

</html>