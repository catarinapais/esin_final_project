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