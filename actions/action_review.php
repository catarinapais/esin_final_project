<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: ../views/login.php');
    exit;
}

$service_id = $_POST['service_id'];
$role = $_POST['role'];
$rating = $_POST['review'];
$description = $_POST['reviewDescription'];
$makePublic = isset($_POST['makePublic']) ? 1 : 0; // if its set -> 1, otherwise -> 0

require_once('../database/init.php');
require_once('../database/review.php');

try {
    insertReview($rating, $description, $service_id, $role);
    header('Location: ../views/account.php');
    exit;

} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}
?>