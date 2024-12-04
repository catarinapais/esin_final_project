<!DOCTYPE html>
<html lang="en">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Care Booking</title>
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

    <main id="bookingcontent">
    <form action="save.php" method="post">
        <fieldset>
            <legend>Booking</legend>
    
            <p>Pet's name:</p>
            <select name="pet_name" id="pet-name">
                <option value="selecionar">Select</option>
                <option value="dosmeus">Dos Meus</option>
                <option value="pets">Pets</option>
            </select>
            <br>
    
            <p>Service Type:</p>
<label>
    <!-- Verifica se o parâmetro 'service' na URL é 'petwalking' -->
    <input type="radio" name="service_type" value="petwalking" 
        <?php echo (isset($_GET['service']) && $_GET['service'] === 'petwalking') ? 'checked' : ''; ?> required> Pet Walking
</label>
<label>
    <!-- Verifica se o parâmetro 'service' na URL é 'petsitting' -->
    <input type="radio" name="service_type" value="petsitting" 
        <?php echo (isset($_GET['service']) && $_GET['service'] === 'petsitting') ? 'checked' : ''; ?> required> Pet Sitting
</label>

    
            <p>Pick-Up and Drop-Off Location:</p>
            <label>
                <input type="radio" name="location" id="myPlace" value="myplace" required> My Place
            </label>
            <label>
                <input type="radio" name="location" id="providersPlace" value="providersplace" required> Pet Sitter/Walker's Place
            </label>
            <label>
                <input type="radio" name="location" id="otherLocation" value="other" required> Other Location
            </label>
            
            <!-- Move this div directly after the #otherLocation radio button -->
            <!--TODO: nao deixar aumentar o tamanho do textarea para os lados-->
            <!--na review nao permite aumentar mais para os aldos, acredito que seja pelo fieldset-->
            <div id="otherLocationDiv">
                <textarea name="other_address" id="other-address" rows="3" cols="30" placeholder="Enter address here..."></textarea>
            </div>
            
        <br>

      <p>Date:</p>
        <input name="date" id="date" type="date" required>
        <p>Start Time:</p>
        <input name="starttime" id="starttime" type="time" required>
        <!--TODO: por condiçao para o endtime > starttime-->
        <!--TODO: condição para os serviços sejam apenas a horas decentes ???? tipo day time-->
        <p>End time:</p>
        <input name="endtime" id="endtime" type="time" required>
        <br>
        <input type="submit" value="Search for Available Pet Walkers/Pet Sitters">

        <br>
 <!-- fazer dropdown de lista com os providers disponíveis--> 
        <p>Photo Consent:</p>
        <label>
            <input type="radio" name="photo_consent" value="yes" required>   Yes
        </label>
        <label>
            <input type="radio" name="photo_consent" value="no" required> No
        </label>
        <br>
        <input type="submit" value="Calculate">
        <br>
    </fieldset>
        <a id="authenticationLink" href="account.html">See past bookings.</a>
    </form>
    </main>

<footer>
    <div class="footer-container">

        <!-- Quick Links Section -->
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="bookingRequest.html">Book a service</a></li>
                <li><a href="serviceProvider.html">Become a PetPatroller</a></li>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="FAQs.html">FAQs</a></li>
            </ul>
        </div>

        <!-- Contact Section -->
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p>Email: contact@petpatrol.com</p>
            <p>Phone: +351 225362821 </p>
        </div>

        <!-- Address Section -->
        <div class="footer-section">
            <h3>Our Office</h3>
            <p>PetPatrol HQ</p>
            <p>Rua Dr. Roberto Frias, 4200-465 Porto</p>
            <p>Open Hours: Mon-Fri, 9am - 6pm</p>
        </div>

        <!-- Newsletter Section -->
        <div class="footer-section">
            <h3>Subscribe to our Newsletter</h3>
            <form action="/subscribe" method="post">
                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>

    </div>

    <!-- Legal Section -->
    <div class="footer-legal">
        <p>&copy; 2024 PetPatrol. All rights reserved.</p>
       
    </div>
</footer>

</body>

</html>