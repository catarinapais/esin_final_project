<?php
session_start();
$name = $_SESSION['name'];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Patrol</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <link href="css/messages.css" rel="stylesheet">
</head>
<body>
<header id="navigationBar">
    <a href="initialPage.php">
        <div id="logo">
            <h1>Pet Patrol</h1>
            <h2>Sit and Walk</h2>
            <img src="images/assets/logo1.png" alt="Logo of Pet Patrol">
        </div>
    </a>
    <nav id="menu">
        <input type="checkbox" id="hamburger">
        <label class="hamburger" for="hamburger"></label>
        <ul id="menuItems">
            <?php if (isset($_SESSION['email'])): ?>
                <li><a href="bookingRequest.php">BOOK A SERVICE</a></li>
                <li><a href="serviceProvider.php">DO A SERVICE</a></li>
                <li><a href="aboutus.php">ABOUT US</a></li>
                <li>
                    <a href="account.php" class="nomedapessoa">
                    <span class="emoji" role="img" aria-label="apple">🙎🏼</span> 
                        <?php echo ($name); ?>
                    </a>
                </li>
            <?php else: ?>
                <li><a href="aboutus.php">ABOUT US</a></li>
                <li class="signup"><a href="register.php" class="button-74">REGISTER</a></li>
                <li class="signup">
                    <a href="login.php" class="button-56" role="button">LOGIN</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
