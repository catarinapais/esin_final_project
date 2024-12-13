<?php
session_start();

// TODO: uncomment quando fizermos o login
/*
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
*/

$service_id = $_POST['service_id'];
$role = $_POST['role'];
$rating = $_POST['review'];
$description = $_POST['reviewDescription'];
$makePublic = isset($_POST['makePublic']) ? 1 : 0; // if its set -> 1, otherwise -> 0

try {
    $dbh = new PDO('sqlite:../database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert the review into the Review table
    $stmt = $dbh->prepare('INSERT INTO Review (rating, description, date_review) VALUES (?, ?, ?)');
    $stmt->execute([$rating, $description, date('Y-m-d')]);
    $review_id = $dbh->lastInsertId();

    // Update the Booking table to link the review
    if ($role == 'owner') {
        $stmt = $dbh->prepare('UPDATE Booking SET ownerReview = ? WHERE id = ?');
    } else {
        $stmt = $dbh->prepare('UPDATE Booking SET providerReview = ? WHERE id = ?');
    }
    $stmt->execute([$review_id, $service_id]);

    header('Location: ../account.php');
    exit;

} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>