<?php
session_start();
$user_id = $_SESSION['id'];

require_once('../database/init.php');
require_once('../database/pet.php');
require_once('../database/bookings.php');

try {
    
    $dbh->beginTransaction(); 
    // Apagar informações relacionadas ao usuário
    deletePetsFromUser($user_id); // Excluir os registos relacionados a animais de estimação
    deleteBookingsFromUser($user_id); // Excluir os registos de reservas e pagamentos
    deleteUser($user_id); // Excluir informações do utilizador

    $dbh->commit();
    session_destroy(); // Encerrar a sessão

    header('Location: ../views/initialPage.php');
    exit;
} catch (PDOException $e) {
    $dbh->rollBack();
    $_SESSION['msg_error'] = "Error deleting account. Please try again.";
    exit;
}
?>
