<?php
  session_start();

  // get email and password from HTTP parameters
  $email = $_POST['email'];
  $password = $_POST['password'];

  // check if email and password are correct
  function loginSuccess($email, $password) {
    global $dbh;
    $stmt = $dbh->prepare('SELECT * FROM Person WHERE email = ? AND password = ?');
    $stmt->execute(array($email, hash('sha256', $password)));
    return $stmt->fetch();
  }

  // if login successful:
  // - create a new session attribute for the user
  // - redirect user to main page
  // else:
  // - set error msg "Login failed!"
  // - redirect user to main page

  try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (loginSuccess($email, $password)) {
      $_SESSION['email'] = $email;
      $_SESSION['msg_success'] = 'Welcome back!';
      header('Location: initialPage.php');
    } else {
      $_SESSION['msg_error'] = 'Invalid email or password!';
      header('Location: login.php');
    }

  } catch (PDOException $e) {
    $_SESSION['msg_error'] = 'Error: ' . $e->getMessage();
    header('Location: login.php');
  }


?>