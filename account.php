<?php
try {
    // Conectar ao banco de dados SQLite
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter o ID da URL
    $user_id = (int) $_GET['id'];  // Garantir que o ID seja um número inteiro

    // Preparar a consulta para pegar as informações do PetOwner com o ID específico e os pets
    $stmt = $dbh->prepare(
        'SELECT 
            Person.name, 
            Person.email, 
            Person.phone_number, 
            Person.address, 
            Person.city, 
            PetOwner.avg_rating,
            Pet.id AS pet_id, 
            Pet.name AS pet_name, 
            Pet.species AS pet_species, 
            Pet.size AS pet_size, 
            Pet.birthdate AS pet_age,
            Pet.profile_picture AS pet_profile_picture
        FROM 
            PetOwner 
        JOIN 
            Person ON PetOwner.person = Person.id 
        LEFT JOIN 
            Pet ON Pet.owner = Person.id 
        WHERE 
            PetOwner.person = :id' 
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    // Tente executar a consulta e verificar se a execução foi bem-sucedida
    if ($stmt->execute()) {
        $petOwnerInfo = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
    } else {
        echo "Erro na execução da consulta.";
    }

    $stmt->closeCursor();
    $stmt = $dbh->prepare(
        'SELECT 
            Booking.id AS service_id,
            Booking.type AS type,
            Booking.date AS date, 
            Booking.start_time AS start_time, 
            Booking.duration AS duration,
            Booking.ownerReview AS ownerReview,
            Booking.providerReview AS providerReview, 
            Pet.name AS pet_name, 
            Pet.id AS pet_id, 
            Pet.species AS species,
            Payment.price AS service_price,
            OwnerReview.rating AS owner_review,
            ProviderReview.rating AS provider_review
        FROM Booking 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN PetOwner ON Pet.owner = PetOwner.person 
        JOIN Payment ON Booking.payment = Payment.id 
        JOIN Review AS OwnerReview ON Booking.ownerReview = OwnerReview.id 
        JOIN Review AS ProviderReview ON Booking.providerReview = ProviderReview.id 
        WHERE PetOwner.person = :id' 
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $pastBookingsInfo = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
    } else {
        echo "Erro na execução da consulta.";
    }

    $stmt->closeCursor();
    $stmt = $dbh->prepare(
        'SELECT 
            Booking.id AS service_id,
            Booking.type AS type,
            Booking.date AS service_date, 
            Booking.start_time AS service_time, 
            Booking.duration AS service_duration,
            OwnerReview.rating AS owner_review,
            ProviderReview.rating AS provider_review,
            Pet.name AS pet_name, 
            Payment.price AS service_price
        FROM Booking 
        JOIN Pet ON Booking.pet = Pet.id 
        JOIN ServiceProvider ON Booking.provider = ServiceProvider.person 
        JOIN Payment ON Booking.payment = Payment.id 
        JOIN Review AS OwnerReview ON Booking.ownerReview = OwnerReview.id 
        JOIN Review AS ProviderReview ON Booking.providerReview = ProviderReview.id 
        WHERE Booking.provider = :id;' 
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $pastServicesInfo = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
    } else {
        echo "Erro na execução da consulta.";
    }

    $stmt->closeCursor();
    $stmt = $dbh->prepare(
        'SELECT *  
        FROM ServiceProvider 
        WHERE ServiceProvider.person = :id' 
    );
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $providerInfo = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
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
    <title>Account</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <li><a href="bookingRequest.php">BOOK A SERVICE</a></li>
                <li><a href="serviceProvider.php">DO A SERVICE</a></li>
                <li><a href="account.php">ACCOUNT</a></li>
                <li><a href="aboutus.html">ABOUT US</a></li>
                <li class="signup"><a href="register.php">REGISTER</a></li>
                <li class="signup"><a href="login.html">LOGIN</a></li>
            </ul>
        </nav>
    </header>

    <main id="accountcontent">

        <?php if (isset($petOwnerInfo) && $petOwnerInfo): ?>
            <!-- If $petOwnerInfo is set and contains data -->
            <section id="staticTags">
                <h2>Personal Information</h2>
                <p><strong>Name:</strong> <?= htmlspecialchars($petOwnerInfo[0]['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($petOwnerInfo[0]['email']) ?></p>
                <p><strong>Phone number:</strong> <?= htmlspecialchars($petOwnerInfo[0]['phone_number']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($petOwnerInfo[0]['address']) ?></p>
                <p><strong>City:</strong> <?= htmlspecialchars($petOwnerInfo[0]['city']) ?></p>
                <!--TODO: calcular o review e dar update a base de dados!-->
                <p><strong>Rating as Owner:</strong>
                    <?php if(!empty($petOwnerInfo[0]['avg_rating'])): ?>
                        <?= htmlspecialchars($petOwnerInfo[0]['avg_rating']) ?>
                    <?php else: ?>
                        No ratings yet.
                    <?php endif; ?>
                </p>
                <?php if (!empty($providerInfo[0]['iban'])): ?>
                    <p><strong>Rating as Provider:</strong>
                        <?php if(!empty($providerInfo[0]['avg_rating'])): ?>
                            <?= htmlspecialchars($providerInfo[0]['avg_rating']) ?>
                        <?php else: ?>
                            No ratings yet.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </section>

            <!--TODO: apenas mostrar os services e os bookings antigos!!-->
            <!--TODO: verificar qual rating é que é para mostrar no "past services" e no "past bookings"-->
            <section id="pastServices">
                <h2>Past Services</h2>
                <?php if (!empty($providerInfo[0]['iban'])): ?>
                    <?php if (!empty($pastServicesInfo)): ?>
                        <?php foreach ($pastServicesInfo as $service): ?>
                            <legend> <?= htmlspecialchars($service['type']) ?></legend>
                            <p><strong>Pet:</strong> <?= htmlspecialchars($booking['pet_name']) ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($service['service_date']) ?></p>
                            <p><strong>Time:</strong> <?= htmlspecialchars($service['service_time']) ?></p>
                            <p><strong>Duration:</strong> <?= htmlspecialchars($service['service_duration']) ?> hours</p>
                            <p><strong>Price:</strong> <?= htmlspecialchars($service['service_price']) ?>€</p>

                            <p><strong>Your Rating:</strong>
                            <?php if (empty($service['provider_review'])): ?>
                                No review yet
                            <?php else: ?>
                                <?= htmlspecialchars($service['provider_review']) ?>
                            <?php endif; ?></p>

                            <!--review of each service (button if theres none)-->
                            <p><strong>Owner's Review:</strong>
                            <?php if (empty($service['owner_review'])): ?>
                                No review yet
                                <form action="review.php" method="post">
                                    <input type="hidden" name="service_id" value="<?= htmlspecialchars($service['service_id']) ?>">
                                    <input type="hidden" name="role" value="owner">
                                    <input type="submit" value="Add Review">
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($service['owner_review']) ?>
                            <?php endif; ?></p>
    
                        <?php endforeach; ?>
                        
                    <?php else: ?>
                        <p>You have no past services.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>You are not eligible to provide a service.</p>
                </section>
            <?php endif; ?>

            <?php if (!empty($petOwnerInfo[0]['pet_id'])): ?>
                <section id="pastBookings">
                    <h2>Past Bookings</h2>
                    <?php if (!empty($pastBookingsInfo)): ?>
                        <?php foreach ($pastBookingsInfo as $booking): ?>
                            <legend> <?= htmlspecialchars($booking['type']) ?></legend>
                            <p><strong>Pet:</strong> <?= htmlspecialchars($booking['pet_name']) ?></p>
                            <p><strong>Date:</strong> <?= htmlspecialchars($booking['date']) ?></p>
                            <p><strong>Time:</strong> <?= htmlspecialchars($booking['start_time']) ?></p>
                            <p><strong>Duration:</strong> <?= htmlspecialchars($booking['duration']) ?> minutes</p>
                            <p><strong>Price:</strong> <?= htmlspecialchars($booking['service_price']) ?>€</p>

                            <p><strong>Your Rating:</strong>
                            <?php if (empty($booking['owner_review'])): ?>
                                No review yet
                            <?php else: ?>
                                <?= htmlspecialchars($booking['owner_review']) ?>
                            <?php endif; ?></p>

                            <p><strong>Provider's Rating:</strong>
                            <?php if (empty($booking['provider_review'])): ?>
                                No review yet</p>
                                <form action="review.php" method="post">
                                    <input type="hidden" name="service_id" value="<?= htmlspecialchars($booking['service_id']) ?>">
                                    <input type="hidden" name="role" value="provider">
                                    <input type="submit" value="Add Review">
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($booking['provider_review']) ?></p>
                            <?php endif; ?>



                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>You have no past bookings.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section id="pets">
                <legend>
                    <h2>Pets</h2>
                    <img src="images/pata.png" alt="Pet" class="pet-icon">
                </legend>

                <!-- Add a new pet -->
                <input type="checkbox" id="toggleForm" style="display: none;">
                <label for="toggleForm" id="addPetButton" class="button"></label>
                <form id="newPetForm" action="action_addPet.php" method="post">
                    <h3>Add a New Pet</h3>
                    <div class="form-group">
                        <label for="name">Pet Name:<span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="species">Species:<span class="required">*</span></label>
                        <select id="species" name="species" required>
                            <option value="" disabled selected>Choose pet species</option>
                            <option value="dog">Dog</option>
                            <option value="cat">Cat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="size">Size:<span class="required">*</span></label>
                        <select id="size" name="size" required>
                            <option value="" disabled selected>Choose pet size</option>
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="birthdate">Birth Date:</label>
                        <input type="date" id="birthdate" name="birthdate">
                    </div>

                    <div class="form-group">
                        <label for="profile_picture">Profile Picture:</label>
                        <input type="file" accept='image/*' id="profile_picture" name="profile_picture">
                    </div>

                    <input type="submit" value="Add Pet">
                </form>

                <!-- If there are pets -->
                <?php if (!empty($petOwnerInfo[0]['pet_id'])): ?>
                    <?php foreach ($petOwnerInfo as $pet): ?>
                        <legend> <?= htmlspecialchars($pet['pet_name']) ?></legend>
                        <p><strong>Species:</strong> <?= htmlspecialchars($pet['pet_species']) ?></p>
                        <p><strong>Size:</strong> <?= htmlspecialchars($pet['pet_size']) ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($pet['pet_age']) ?> years</p>
                        <!-- Exibir a imagem do pet -->
                        <?php if (!empty($pet['pet_profile_picture'])): ?>
                            <img src="images/uploads/<?= htmlspecialchars($pet['pet_profile_picture']) ?>"
                                alt="<?= htmlspecialchars($pet['pet_name']) ?>"
                                class="pet-profile-pic">
                        <?php else: ?>
                            <img src="images/imagemdefault.jpg"
                                alt="Default picture for pet"
                                class="pet-profile-pic">
                        <?php endif; ?>
                    <?php endforeach; ?>
            </section>

        <?php else: ?>
            <p>You have no pets added.</p>
        <?php endif; ?>

    <?php else: ?>
        <p>No account information found.</p>
    <?php endif; ?>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="bookingRequest.html">Book a service</a></li>
                    <li><a href="serviceProvider.html">Become a PetPatroller</a></li>
                    <li><a href="aboutus.html">About Us</a></li>
                    <li><a href="FAQs.html">FAQs</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: contact@petpatrol.com</p>
                <p>Phone: +351 225362821</p>
            </div>

            <div class="footer-section">
                <h3>Our Office</h3>
                <p>PetPatrol HQ</p>
                <p>Rua Dr. Roberto Frias, 4200-465 Porto</p>
                <p>Open Hours: Mon-Fri, 9am - 6pm</p>
            </div>

            <div class="footer-section">
                <h3>Subscribe to our Newsletter</h3>
                <form action="/subscribe" method="post">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-legal">
            <p>&copy; 2024 PetPatrol. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>