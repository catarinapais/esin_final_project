<?php
session_start();


require_once('../database/init.php');
require_once('../database/bookings.php');

// Verifica se o booking_id está definido na sessão
if (isset($_SESSION['booking_id'])) {
    $booking_id = $_SESSION['booking_id'];

    try {
        $result = getIban($booking_id);
    } catch (PDOException $e) {
        $_SESSION['msg_error'] = 'Error fetching booking data.';
        exit();
    }
    
    // Verifica se a consulta retornou resultados
    if ($result) {
        $payment =  $_SESSION['total_payments'][$booking_id] ?? 'Not defined';
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
    <p><strong>Amount:</strong> <span class="highlight"><?php echo htmlspecialchars($payment); ?> €</span></p>
<p><strong>Provider's IBAN:</strong> <span class="highlight"><?php echo htmlspecialchars($iban); ?></span></p>

    <p>At the moment, payment is only available by bank transfer.</p>

    <h3>Payment Instructions:</h3>
    <ol>
        <li>Transfer the specified amount above to the provided IBAN.</li>
        <li>Include your name and the pet's name in the transfer description.</li>
        <li>Send the payment receipt to the email: <strong>payments@petpatrol.com</strong>.</li>
    </ol>

    <p>Once we confirm the payment, we will contact you to finalize the process.</p>
</main>

<?php include('../templates/footer_tpl.php'); ?>