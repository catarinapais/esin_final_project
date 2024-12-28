<?php
session_start();

// Get email and password from HTTP parameters
$email = $_POST['email'];
$password = $_POST['password'];

require_once('../database/init.php');
require_once('../database/person.php');

try {

  // Attempt login
  $user = loginSuccess($email, $password);
  if ($user = loginSuccess($email, $password)) {
    $_SESSION['id'] = $user['id']; // 'id' deve ser a coluna correspondente no banco
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['phone_number'] = $user['phone_number'];
    $_SESSION['city'] = $user['city'];
    $_SESSION['address'] = $user['address'];
    if (!empty($user['iban'])) {
      $_SESSION['iban'] = $user['iban'];
      $_SESSION['service_type'] = $user['service_type'];
    }
    $_SESSION['msg_success'] = 'Welcome back!';

    // Reset failed attempts for this email
    unset($_SESSION['failed_attempts'][$email]);

    header('Location: ../views/initialPage.php');
    exit();
  } else {
    // Failed login
    if (!isset($_SESSION['failed_attempts'][$email])) {
      $_SESSION['failed_attempts'][$email] = 0;
    }
    $_SESSION['failed_attempts'][$email]++;

    if ($_SESSION['failed_attempts'][$email] >= 3) {
      $_SESSION['msg_error'] = 'Too many failed login attempts. Please try again later.';
      header('Location: ../views/initialPage.php');
      exit();
    }
    $_SESSION['msg_error'] = 'Invalid email or password!';
    header('Location: ../views/login.php');
    exit();
  }
} catch (PDOException $e) {
  // Handle connection errors
  $_SESSION['msg_error'] = 'Error: ' . $e->getMessage();
  header('Location: ../views/login.php'); // Redirect to login page
}
