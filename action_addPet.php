<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; //TODO: ver login

$name = $_POST['name'];
$species = $_POST['species'];
$size = $_POST['size'];
$birthdate = $_POST['birthdate'];
$profile_picture = $_POST['profile_picture'];

// TODO: secalhar pomos isto em todas as páginas
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Pet (name, species, size, birthdate, profile_picture, owner) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute(array($name, $species, $size, $birthdate, $profile_picture, $user_id));
}

$dbh = new PDO('sqlite:database.db');
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id);

header('Location: account.php');
exit;
?>