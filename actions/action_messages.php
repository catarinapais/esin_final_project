<?php
session_start();

$owner = $_POST['owner'];
$provider = $_POST['provider'];
$message_body = $_POST['message_body'];
$send_time = $_POST['send_time'];
$id = $_SESSION['id']; //logged in user

require_once('../database/init.php');
require_once('../database/message.php');

try {
    insertMessage($id, $message_body, $send_time, $owner, $provider);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

header('Location: ../views/messages.php?owner=' . $owner . '&provider=' . $provider);
?>