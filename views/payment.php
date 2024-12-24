<?php
session_start();


require_once('../database/init.php');


// Verifica se o booking_id está definido na sessão
if (isset($_SESSION['booking_id'])) {
    $booking_id = $_SESSION['booking_id'];

    // Consulta para obter o pagamento e o IBAN do prestador de serviços
    $stmt = $dbh->prepare('
        SELECT 
    Payment.price,
    ServiceProvider.iban
FROM 
    Booking
INNER JOIN ServiceProvider ON Booking.provider = ServiceProvider.person
LEFT JOIN Payment ON Booking.payment = Payment.id
WHERE 
    Booking.id = :booking_id;
    ');
    $stmt->bindValue(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    var_dump($result);

    // Verifica se a consulta retornou resultados
    if ($result) {
        $payment = $result['payment'] ?? 'Not defined';
        $iban = $result['iban'] ?? 'Not defined';
    } else {
        $payment = 'Not defined';
        $iban = 'Not defined';
    }
} else {
    // Se o booking_id não estiver definido na sessão, define valores padrão
    $payment = 'Not defined';
    $iban = 'Not defined';
}

include('../templates/header_tpl.php');
?>

<main id="payment-content">
    <?php if (isset($_SESSION["msg_success"])) {
        echo "<p class='msg_success'>{$_SESSION["msg_success"]}</p>";
        unset($_SESSION["msg_success"]);
    } ?>
    <h1>Payment</h1>
    <p><strong>Amount:</strong> <?php echo htmlspecialchars($payment); ?></p>
    <p><strong>Provider's IBAN:</strong> <?php echo htmlspecialchars($iban); ?></p>
    <p>At the moment, payment is only available by bank transfer.</p>

    <h3>Payment Instructions:</h3>
    <ol>
        <li>Transfer the specified amount above to the provided IBAN.</li>
        <li>Include your name and the pet's name in the transfer description.</li>
        <li>Send the payment receipt to the email: <strong>payments@example.com</strong>.</li>
    </ol>

    <p>Once we confirm the payment, we will contact you to finalize the process.</p>
</main>

<?php include('../templates/footer_tpl.php'); ?>
