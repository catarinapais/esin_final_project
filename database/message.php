<?php
function getMessages($owner, $provider) {
    global $dbh;
    $stmt = $dbh->prepare(
        'SELECT * FROM Message WHERE 
        (owner = ? AND provider = ?) OR
        (owner = ? AND provider = ?)
        ORDER BY send_time DESC;'
    ); // querying all messages between these two homies
    $stmt->execute(array($owner,$provider,$provider,$owner));
    return $stmt->fetchAll();
}

function insertMessage($id, $message_body, $send_time, $owner, $provider) {
    global $dbh;
    $stmt = $dbh->prepare(
        'INSERT INTO 
        Message (sender, message_body, send_time, is_read, owner, provider) 
        VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $id,
        $message_body,
        $send_time,
        0,
        $owner,
        $provider
    ]);
}
?>