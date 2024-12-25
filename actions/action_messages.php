<?php
session_start();
var_dump($_POST);
$owner = $_POST['owner'];
$provider = $_POST['provider'];
$message_body = $_POST['message_body'];
$send_time = $_POST['send_time'];
$id = $_SESSION['id']; //logged in user

require_once('../database/init.php');

try {
    $stmt = $dbh->prepare('
                    INSERT INTO Message 
                    (sender, message_body, send_time, is_read, owner, provider) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ');
    $stmt->execute([
        $id,
        $message_body,
        $send_time,
        0,
        $owner,
        $provider
    ]);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

header('Location: ../views/messages.php?owner=' . $owner . '&provider=' . $provider);
?>