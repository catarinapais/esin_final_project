<?php
session_start(); 
?>

<!-- Registration page -->
 <!-- Nome, phone number, adress, email, city, iban, password, 
  service type-->
<!DOCTYPE html>
<html lang="en">
    <head> 
        <title> Registration Page</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--define this for responsive design-->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/layout.css" rel="stylesheet">
        <link href="css/responsive.css" rel="stylesheet">
    </head> 
    <body> 
        <header id="navigationBar">
            <a href="initialPage.html">
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
            <!--TODO: nos campos obrigatórios, pôr um asterisco a vermelho-->
            <form class="registration-form" action="action_register.php" method="POST"> <!-- reference to php file to be created-->
                <h2>Register</h2>
                <p>Name:</p>
                <input type="text" id="name" name="name" required>
            
                <p>Phone Number:</p>
                <input type="tel" id="phone_number" name="phone_number" required>
         
                <p>Address:</p>
                <input type="text" id="address" name="address" required>
         
                <p>Email:</p>
                <input type="email" id="email" name="email" required>
          
                <p>City:</p>
                <input type="text" id="city" name="city" required>

                <!--TODO: eu separava isto aqui, nao sei como-->
                <!--ou explicar que a pessoa preenche estes campos se quiser ser prestadora de serviços-->
                <p>IBAN:</p>
                <input type="text" id="iban" name="iban" required>
                <!--TODO: iban está required!! não pode ser-->
                <!--verificar se o iban cumpre com as restrições (PT50 e 30 digitos)-->

                <p>Password:</p>
                <input type="password" id="password" name="password" required>

                <p>Service Type:</p>
                <select id="service_type" name="service_type" required>
                    <option value="" disabled selected>Choose service type</option>
                    <option value="sitting">Sitting</option>
                    <option value="walking">Walking</option>
                    <option value="both">Both</option>
                </select>
            <br>
        
                <input type="submit" value="Register">
            </form>
            <p>Already have an account? 
                <a id="authenticationLink" href="login.html">Login Here</a>
            </p>
        </section>
    </body>
</html>