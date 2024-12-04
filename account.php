<?php
try {
    // Conectar ao banco de dados SQLite
    $dbh = new PDO('sqlite:database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter o ID da URL
    $petOwnerId = (int) $_GET['id'];  // Garantir que o ID seja um número inteiro

    // Preparar a consulta para pegar as informações do PetOwner com o ID específico e os pets
    $stmt = $dbh->prepare('
        SELECT 
            Person.name, 
            Person.email, 
            Person.phone_number, 
            Person.adress, 
            Person.city, 
            PetOwner.avg_rating,
            Pet.id AS pet_id, 
            Pet.name AS pet_name, 
            Pet.species AS pet_species, 
            Pet.size AS pet_size, 
            Pet.age AS pet_age,
            Pet.profile_picture AS pet_profile_picture
        FROM 
            PetOwner
        JOIN 
            Person ON PetOwner.person = Person.id
        LEFT JOIN 
            Pet ON Pet.owner = Person.id
        WHERE 
            PetOwner.person = :id
    ');
    $stmt->bindValue(':id', $petOwnerId, PDO::PARAM_INT);

    // Tente executar a consulta e verificar se a execução foi bem-sucedida
    if ($stmt->execute()) {
        $petOwnerInfo = $stmt->fetchAll(); // todas as linhas da tabela todos os resultados (queremos todos os pets da pessoa)
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
            <a href="initialPage.html">
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

        <main id="accountcontent">
       

            <?php if (isset($petOwnerInfo) && $petOwnerInfo): ?>
                <!-- If $petOwnerInfo is set and contains data -->
                <section id="staticTags">
                    <h2>Personal Information</h2>
                    <p><strong>Name:</strong> <?= htmlspecialchars($petOwnerInfo[0]['name']) ?></p>
                    <p><strong>Email:</strong>  <?= htmlspecialchars($petOwnerInfo[0]['email']) ?></p>
                    <p><strong>Phone number:</strong>  <?= htmlspecialchars($petOwnerInfo[0]['phone_number']) ?></p>
                    <p> <strong>Adress:</strong> <?= htmlspecialchars($petOwnerInfo[0]['adress']) ?></p>
                    <p><strong>City:</strong>  <?= htmlspecialchars($petOwnerInfo[0]['city']) ?></p>
                    <p><strong>Rating:</strong>  <?= htmlspecialchars($petOwnerInfo[0]['avg_rating']) ?></p>
                </section>

                <!-- If there are pets -->
                <?php if (!empty($petOwnerInfo[0]['pet_id'])): ?>
                    <section id="pets">
                        <legend>
                        <h2>Pets</h2>
                        <img src="images/pata.png" alt="Pet" class="pet-icon">
                </legend>
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
                    <p>No pets found for this owner.</p>
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
