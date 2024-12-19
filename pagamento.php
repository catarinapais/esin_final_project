<?php
session_start();
$payment = $_SESSION['payment'] ?? 'Not defined';
$iban = $_SESSION['iban'] ?? 'Not defined';




include('templates/header_tpl.php'); ?>

 <main id="payment-content">
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

<?php include('templates/footer_tpl.php'); ?>
