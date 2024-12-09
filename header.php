<?php
session_start();

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $dbh = new PDO('sqlite:database.db');
    $stmt = $dbh->prepare('SELECT * FROM Person WHERE email = ?;');
    $stmt->execute(array($email));
    $person = $stmt->fetch();
    $name = $person['name'];
    $stmt->closeCursor();
}
?>

<header id="navigationBar">
        <a href="initialPage.php">
            <div id="logo">
                <h1>Pet Patrol</h1>
                <h2>Sit and Walk</h2>
                <img src="images/logo1.png" alt="Logo of Pet Patrol">
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
                    <li><a href="account.php"><?php echo $name; ?></a></li>
                    
                <?php else: ?>
                    <li><a href="aboutus.php">ABOUT US</a></li>
                    <li class="signup"><a href="register.php">REGISTER</a></li>
                    <li class="signup"><a href="login.php">LOGIN</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>