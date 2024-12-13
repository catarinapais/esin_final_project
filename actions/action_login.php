<?php
session_start();

// Get email and password from HTTP parameters
$email = $_POST['email'];
$password = $_POST['password'];

// Check if email and password are correct
function loginSuccess($email, $password) {
    global $dbh;
    $stmt = $dbh->prepare(
      'SELECT id, name, email, phone_number, city, address
      FROM Person 
      WHERE email = ? AND password = ?');
    $stmt->execute(array($email, hash('sha256', $password)));
    return $stmt->fetch(); // Fetch will return the row if credentials are valid
    if ($user) {
      var_dump($user); // Mostra os dados retornados
  }
}

try {
    // Connect to SQLite database
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Attempt login
    $user = loginSuccess($email, $password);

    if ($user = loginSuccess($email, $password)) {
      $_SESSION['id'] = $user['id']; // 'id' deve ser a coluna correspondente no banco
      $_SESSION['email'] = $user['email'];
      $_SESSION['name'] = $user['name'];
      $_SESSION['phone_number'] = $user['phone_number'];
      $_SESSION['city'] = $user['city'];
      $_SESSION['address'] = $user['address'];
      $_SESSION['msg_success'] = 'Welcome back!';
      header('Location: initialPage.php');
      exit();
  } else {
      $_SESSION['msg_error'] = 'Invalid email or password!';
      header('Location: login.php');
      exit();
  }
  
} catch (PDOException $e) {
    // Handle connection errors
    $_SESSION['msg_error'] = 'Error: ' . $e->getMessage();
    header('Location: login.php'); // Redirect to login page
}
?>
