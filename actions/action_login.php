<?php
session_start();

// Get email and password from HTTP parameters
$email = $_POST['email'];
$password = $_POST['password'];

// Check if email and password are correct
function loginSuccess($email, $password) {
    global $dbh;
    $stmt = $dbh->prepare(
      'SELECT p.id, p.name, p.email, p.phone_number, p.city, p.address, sp.iban, sp.service_type
      FROM Person p
      LEFT JOIN ServiceProvider sp ON p.id = sp.person
      WHERE p.email = ? AND p.password = ?');
    $stmt->execute(array($email, hash('sha256', $password)));
    return $stmt->fetch(); // Fetch will return the row if credentials are valid
    if ($user) {
      var_dump($user); // Mostra os dados retornados
  }
}

require_once('../database/init.php');
try {

    // Attempt login
    $user = loginSuccess($email, $password);

    if ($user =loginSuccess($email, $password)) {
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
      header('Location: ../views/initialPage.php');
      exit();
  } else {
      $_SESSION['msg_error'] = 'Invalid email or password!';
      header('Location: ../views/login.php');
      exit();
  }
  
} catch (PDOException $e) {
    // Handle connection errors
    $_SESSION['msg_error'] = 'Error: ' . $e->getMessage();
    header('Location: ../views/login.php'); // Redirect to login page
}
?>
