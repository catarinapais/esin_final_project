<?php
session_start();
?>

<!-- Login page -->
 <!-- Nome ou emmail (ou username ou email) --> 
  <!-- password -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--define this for responsive design-->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/layout.css" rel="stylesheet">
        <link href="css/responsive.css" rel="stylesheet">
    </head>
    <body>
        <header id="navigationBar">
            <a href="initialPage.php">
                <div id="logo">
                    <h1>Pet Patrol</h1>
                    <h2>Sit and Walk</h2>
                    <img src="images/logo1.png" alt="Logo of Pet Patrol">
                </div>
            </a>
        </header>
        <?php
        if (isset($_SESSION["msg_error"])) {
            echo "<p class='msg_error'>{$_SESSION["msg_error"]}</p>";
            unset($_SESSION["msg_error"]);
        }
        ?>
        <section id="authentication">
            <form class="login-form" action="action_login.php" method="POST">
                <h2>Login</h2>
                <div class="form-group"> <!-- faria mais sentido se calhar ser o mail ou o username, se for o username metemos no register para meter-->
                    <p>Email:</p>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <p>Password:</p>
                    <input type="password" id="password" name="password" required>
                </div>
                <br>
                <div class="form-group">
                    <input type="submit" value="Login">
                </div>
            </form> 
            <p>Don't have an account? 
                <a id="authenticationLink" href="register.php">Register Here</a>
            </p>
        </section>
        <?php include('footer.php'); ?>
    </body>
</html>