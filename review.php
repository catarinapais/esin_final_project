<?php
session_start();

$service_id = $_POST['service_id']; // id do serviço que se vai fazer review
$role = $_POST['role']; // a quem se vai dar a review

if ($role == 'owner') {
    $service = 'Service';
} else {
    $service = 'Booking';
}
// role=owner - o provider vai dar review ao owner (pelo SERVICE feito)
// role=provider - o owner vai dar review ao provider (pelo BOOKING feito)

try {
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare(
        'SELECT 
            Booking.type AS type,
            Booking.date AS date, 
            Booking.start_time AS start_time, 
            Booking.duration AS duration,
            Booking.ownerReview AS ownerReview,
            Booking.providerReview AS providerReview, 
            Pet.name AS pet_name, 
            Pet.id AS pet_id, 
            Pet.species AS species,
            Owner.name AS owner_name,
            Provider.name AS provider_name
        FROM Booking 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN PetOwner ON Pet.owner = PetOwner.person 
        JOIN Person AS Owner ON PetOwner.person = Owner.id 
        JOIN Person AS Provider ON Booking.provider = Provider.id 
        WHERE Booking.id = :id' 
    );
    $stmt->bindValue(':id', $service_id, PDO::PARAM_INT);
    // Tente executar a consulta e verificar se a execução foi bem-sucedida
    if ($stmt->execute()) {
        $serviceInfo = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
    } else {
        echo "Erro na execução da consulta.";
    }

    $stmt->closeCursor();
    $stmt = $dbh->prepare(
        'SELECT 
            Booking.type AS type,
            Booking.date AS date, 
            Booking.start_time AS start_time, 
            Booking.duration AS duration,
            Booking.ownerReview AS ownerReview,
            Booking.providerReview AS providerReview, 
            Pet.name AS pet_name, 
            Pet.id AS pet_id, 
            Pet.species AS species,
            Owner.name AS owner_name,
            Provider.name AS provider_name
        FROM Booking 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN PetOwner ON Pet.owner = PetOwner.person 
        JOIN Person AS Owner ON PetOwner.person = Owner.id 
        JOIN Person AS Provider ON Booking.provider = Provider.id 
        WHERE Booking.id = :id' 
    );
    $stmt->bindValue(':id', $service_id, PDO::PARAM_INT);
    // Tente executar a consulta e verificar se a execução foi bem-sucedida
    if ($stmt->execute()) {
        $bookingInfo = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
    } else {
        echo "Erro na execução da consulta.";
    }

} catch (PDOException $e) {
    // Tratamento de erro
    echo "Erro de conexão: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Review</title>
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
            <nav id="menu">
                <input type="checkbox" id="hamburger"> 
                <label class="hamburger" for="hamburger"></label>
                <ul id="menuItems">
                    <li><a href="bookingRequest.html">BOOK A SERVICE</a></li>
                    <li><a href="serviceProvider.php">DO A SERVICE</a></li>
                    <li><a href="account.html">ACCOUNT</a></li>
                    <li><a href="aboutus.html">ABOUT US</a></li>
                    <li class="signup"><a href="register.html">REGISTER</a></li>
                    <li class="signup"><a href="login.html">LOGIN</a></li>
                </ul>
            </nav>
        </header>

        <section id="pastService"><!--querying info about this service-->
            <h2>Past <?=$service ?></h2>
            <?php if ($role == "owner") {?>
                <h3>Pet <?= htmlspecialchars($serviceInfo[0]['type']) ?> to <?= htmlspecialchars($serviceInfo[0]['pet_name']) ?></h3>
                <p><?= htmlspecialchars(ucfirst($role)) ?>: <?= htmlspecialchars($serviceInfo[0][$role . '_name']) ?> </p>
                <p><?= htmlspecialchars($serviceInfo[0]['date']) ?>  <?= htmlspecialchars($serviceInfo[0]['start_time']) ?> </p>
            <?php } else if ($role == "provider") { ?>
                <h3>Pet <?= htmlspecialchars($bookingInfo[0]['type']) ?> to <?= htmlspecialchars($bookingInfo[0]['pet_name']) ?></h3>
                <p><?= htmlspecialchars(ucfirst($role)) ?>: <?= htmlspecialchars($bookingInfo[0][$role . '_name']) ?> </p>
                <p><?= htmlspecialchars($bookingInfo[0]['date']) ?>  <?= htmlspecialchars($bookingInfo[0]['start_time']) ?> </p>
            <?php } ?>
        </section>
        <section id="review">
            <form action="action_review.php" method="post">
                <h2>Review this <?= htmlspecialchars(ucfirst($role)) ?></h2>
                <div id="starReview">
                    <input type="radio" id="star5" name="review" value="5" required="required">
                    <label for="star5" title="5 stars">&#9733;</label>
                    <input type="radio" id="star4" name="review" value="4" required="required">
                    <label for="star4" title="4 stars">&#9733;</label>
                    <input type="radio" id="star3" name="review" value="3" required="required">
                    <label for="star3" title="3 stars">&#9733;</label>
                    <input type="radio" id="star2" name="review" value="2" required="required">
                    <label for="star2" title="2 stars">&#9733;</label>
                    <input type="radio" id="star1" name="review" value="1" required="required">
                    <label for="star1" title="1 star">&#9733;</label>
                </div>
                <label>
                    Description: 
                    <textarea id="reviewDescription" name="reviewDescription" rows="3" cols="30" placeholder="Describe your experience!" required="required"></textarea>
                </label>
                <label>
                    <br>
                    <input type="checkbox" name="makePublic" value="1">
                    I allow this review to be made public in the website.
                </label>
                <input type="hidden" name="service_id" value="<?= htmlspecialchars($service_id) ?>">
                <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
                <input type="submit" value="Submit Review">
    </body>
</html>
