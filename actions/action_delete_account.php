<?php
session_start();

require_once('../database/init.php');

try {
    $user_id = $_SESSION['id'];

    // Apagar informações relacionadas ao usuário
    $dbh->beginTransaction();

    // Excluir os registos relacionados a animais de estimação
    $stmt = $dbh->prepare('DELETE FROM PetMedicalNeed WHERE pet IN (SELECT id FROM Pet WHERE owner = ?)');
    $stmt->execute([$user_id]);

    $stmt = $dbh->prepare('DELETE FROM Pet WHERE owner = ?');
    $stmt->execute([$user_id]);

    // Excluir os registos de reservas e pagamentos
    $stmt = $dbh->prepare('DELETE FROM Booking WHERE pet IN (SELECT id FROM Pet WHERE owner = ?)');
    $stmt->execute([$user_id]);

    $stmt = $dbh->prepare('DELETE FROM Payment WHERE id IN (SELECT payment FROM Booking WHERE pet IN (SELECT id FROM Pet WHERE owner = ?))');
    $stmt->execute([$user_id]);

    // Excluir informações do utilizador
    $stmt = $dbh->prepare('DELETE FROM PetOwner WHERE person = ?');
    $stmt->execute([$user_id]);

    $stmt = $dbh->prepare('DELETE FROM ServiceProvider WHERE person = ?');
    $stmt->execute([$user_id]);

    $stmt = $dbh->prepare('DELETE FROM Person WHERE id = ?');
    $stmt->execute([$user_id]);

    $dbh->commit();

    // Encerrar a sessão
    session_destroy();

    // Redirecionar para a página inicial
    header('Location: ../views/initialPage.php');
    exit;
} catch (PDOException $e) {
    $dbh->rollBack();
    echo "Error deleting account: " . $e->getMessage();
    exit;
}
?>
