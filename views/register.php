<?php
session_start();
?>


<?php
    include('../templates/header_tpl.php');
    ?>
    <?php
    if (isset($_SESSION["msg_error"])) {
        echo "<p class='msg_error'>{$_SESSION["msg_error"]}</p>";
        unset($_SESSION["msg_error"]);
    }
    ?>
    <section id="authentication">
        <!--TODO: nos campos obrigatórios, pôr um asterisco a vermelho-->
        <form class="registration-form" action="../actions/action_register.php" method="POST">
            <h2>Register</h2>
            <div class="form-group">
                <label for="name">Name:<span class="required">*</span></label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:<span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:<span class="required">*</span></label>
                <input type="tel" pattern="\d{9}" title="9-digit phone number" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="address">Address:<span class="required">*</span></label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="city">City:<span class="required">*</span></label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="iban">IBAN:<span class="registerProvider">(In case you want to provide a service)</span></label>
                <input type="text" pattern="PT50(\s*\d\s*){21}" title="Valid portuguese IBAN" id="iban" name="iban">
            </div>
            <div class="form-group">
                <label for="service_type">Service Type:<span class="registerProvider">(In case you want to provide a service)</span></label>
                <select id="service_type" name="service_type">
                    <option value="" disabled selected>Choose service type</option>
                    <option value="sitting">Sitting</option>
                    <option value="walking">Walking</option>
                    <option value="both">Both</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password:<span class="required">*</span></label>
                <input type="password" pattern="(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}" title="Password must be at least 8 characters long, include one uppercase letter, one number, and one special character" id="password" name="password" required>
            </div>
            <br>
            <input type="submit" value="Register">
        </form>
        <p>Already have an account?
            <a id="authenticationLink" href="login.php">Login Here</a>
        </p>
    </section>
    <?php include('../templates/footer_tpl.php'); ?>
