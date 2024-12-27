<?php
session_start();
?>

<?php
  include('../templates/header_auth_tpl.php');
  
        if (isset($_SESSION["msg_error"])) {
            echo "<p class='msg_error'>{$_SESSION["msg_error"]}</p>";
            unset($_SESSION["msg_error"]);
        }
        ?>
        <section id="authentication">
            <form class="login-form" action="../actions/action_login.php" method="POST">
                <h2>Login</h2>
                <div class="form-group"> <!-- faria mais sentido se calhar ser o mail ou o username, se for o username metemos no register para meter-->
                    <p>Email:</p>
                    <input type="email" id="id" name="email" required>
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
        <?php include('../templates/footer_tpl.php'); ?>
