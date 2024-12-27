<?php
function getSchedule($user_id, $week_start, $week_end) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT 
            day_week, 
            start_time, 
            end_time 
        FROM Schedule 
        WHERE service_provider = :service_provider 
            AND day_week BETWEEN :week_start AND :week_end'
    );
    $stmt->execute([
        ':service_provider' => $user_id,
        ':week_start' => $week_start,
        ':week_end' => $week_end,
      ]);
    return $stmt->fetchAll();
}

function insertSchedule($user_id, $date, $start_time, $end_time) {
    global $dbh;
    $stmt = $dbh->prepare(
        'INSERT INTO 
            Schedule (day_week, start_time, end_time, service_provider) 
            VALUES (?, ?, ?, ?)'
        );
    $stmt->execute([
        $date,
        $start_time,
        $end_time,
        $user_id
    ]);
    $_SESSION['msg_success'] = "Availability added successfully.";
}
?>