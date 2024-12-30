<?php
session_start();
$name = $_SESSION['name'];
$user_id = $_SESSION['id'];

require_once('../database/init.php');
require_once('../database/person.php');
require_once('../database/bookings.php');
require_once('../database/pet.php');

function starReview($stars)
{
    $stars = (int)$stars;
    $count = 1;
    $result = "<div class='star-review-small'>";

    for ($i = 1; $i <= 5; $i++) {
        if ($stars >= $count) {
            $result .= "<span>&#x2605;</span>";
        } else {
            $result .= "<span>&#x2606;</span>";
        }
        $count++;
    }
    $result .= "</div>";
    return $result;
}

try {
    $petOwnerInfo = getPersonInfo($user_id); // Get user info
    $pastBookingsInfo = getPastBookings($user_id); // Fetch past bookings
    $pastServicesInfo = getPastServices($user_id); // Fetch past services
    $providerInfo = getProviderInfo($user_id); // Fetch service provider info
    $medicalNeeds = fetchMedicalNeeds($petOwnerInfo); // Fetch the medical needs for each pet
} catch (PDOException $e) { // error handling
    $_SESSION['msg_error'] = "Connection Error.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Patrol</title>
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/layout.css" rel="stylesheet">
    <link href="../css/responsive.css" rel="stylesheet">
</head>

<body>
    <header id="navigationBar">
        <a href="initialPage.php">
            <div id="logo">
                <h1>Pet Patrol</h1>
                <h2>Sit and Walk</h2>
                <img src="../images/assets/logo1.png" alt="Logo of Pet Patrol">
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


    <main id="accountcontent">

        <div class="main-content">
            <?php if (isset($petOwnerInfo) && $petOwnerInfo): ?>
                <!-- If $petOwnerInfo is set and contains data -->
                <section id="staticTags">
                    <h2>Personal Information</h2>
                    <p><strong>Name:</strong> <?= htmlspecialchars($petOwnerInfo[0]['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($petOwnerInfo[0]['email']) ?></p>
                    <p><strong>Phone number:</strong> <?= htmlspecialchars($petOwnerInfo[0]['phone_number']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($petOwnerInfo[0]['address']) ?></p>
                    <p><strong>City:</strong> <?= ucfirst(htmlspecialchars($petOwnerInfo[0]['city'])) ?></p>
                    <!--TODO: calcular o review e dar update a base de dados!-->
                    <p><strong>Rating as Owner:</strong>
                        <?php if (!empty($petOwnerInfo[0]['avg_rating'])): ?>
                            <?= htmlspecialchars($petOwnerInfo[0]['avg_rating']) ?>
                        <?php else: ?>
                            No ratings yet.
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($providerInfo[0]['iban'])): ?>
                        <p><strong>Rating as Provider:</strong>
                            <?php if (!empty($providerInfo[0]['avg_rating'])): ?>
                                <?= htmlspecialchars($providerInfo[0]['avg_rating']) ?>
                            <?php else: ?>
                                No ratings yet.
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <div class="button-container">
                        <form action="../actions/action_logout.php" method="post" id=logout>
                            <input type="submit" id="logoutb" value="Logout">
                        </form>

                        <!-- Botão para apagar conta -->
                        <form action="../actions/action_delete_account.php" method="post" id="deleteAccount">
                            <input type="submit" id="delete" value="Delete Account" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                        </form>
                    </div>


                </section>
                <!--TODO: verificar qual rating é que é para mostrar no "past services" e no "past bookings"-->

                <section id="pastServices">
                    <h2>Past Services</h2>
                    <?php if (!empty($providerInfo[0]['iban'])): ?>
                        <?php if (!empty($pastServicesInfo)): ?>
                            <?php foreach ($pastServicesInfo as $service): ?>
                                <div class="service-box">
                                    <h3><?= htmlspecialchars($service['type']) ?></h3>
                                    <p><strong>Pet:</strong> <?= htmlspecialchars($service['pet_name']) ?></p>
                                    <p><strong>Date:</strong> <?= htmlspecialchars($service['service_date']) ?></p>
                                    <p><strong>Time:</strong> <?= htmlspecialchars($service['service_time']) ?></p>
                                    <?php
                                    $duration = round($service['service_duration']);
                                    if ($duration >= 60) {
                                        $hours = $duration / 60;
                                        $formatted_duration = htmlspecialchars(round($hours, 2)) . ' h';
                                    } else {
                                        $formatted_duration = htmlspecialchars($duration) . ' min';
                                    }
                                    ?>
                                    <p><strong>Duration:</strong> <?= $formatted_duration ?></p>
                                    <p><strong>Price:</strong> <?= htmlspecialchars($service['payment']) ?>€</p>

                                    <div class="review">
                                        <p><strong>Your Rating:</strong></p>
                                        <?php if (empty($service['provider_review'])): ?>
                                            <p>No review yet</p>
                                        <?php else: ?>
                                            <?= starReview($service['provider_review']); ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="review">
                                        <p><strong>Owner's Review:</strong></p>
                                        <?php if (empty($service['owner_review'])): ?>
                                            <p>No review yet</p>
                                        <?php else: ?>
                                            <?= starReview($service['owner_review']); ?>
                                        <?php endif; ?>

                                    </div>

                                    <?php if (empty($service['owner_review'])): ?>
                                        <form action="review.php" method="post">
                                            <input type="hidden" name="service_id" value="<?= htmlspecialchars($service['service_id']) ?>">
                                            <input type="hidden" name="role" value="owner">
                                            <input type="submit" class="review-owner" value="Add Review">
                                        </form>
                                    <?php endif; ?>

                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>You have no past services.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>You are not eligible to provide a service.</p>
                    <?php endif; ?>
                </section>

                <?php if (!empty($petOwnerInfo[0]['pet_id'])): ?>
                    <section id="pastBookings">
                        <h2>Past Bookings</h2>
                        <?php if (!empty($pastBookingsInfo)): ?>
                            <?php foreach ($pastBookingsInfo as $booking): ?>
                                <div class="booking-box">
                                    <h3><?= htmlspecialchars($booking['type']) ?></h3>
                                    <p><strong>Pet:</strong> <?= htmlspecialchars($booking['pet_name']) ?></p>
                                    <p><strong>Date:</strong> <?= htmlspecialchars($booking['date']) ?></p>
                                    <p><strong>Time:</strong> <?= htmlspecialchars($booking['start_time']) ?></p>
                                    <?php
                                    $duration = round($booking['duration']);
                                    if ($duration >= 60) {
                                        $hours = $duration / 60;
                                        $formatted_duration = htmlspecialchars(round($hours, 2)) . ' h';
                                    } else {
                                        $formatted_duration = htmlspecialchars($duration) . ' min';
                                    }
                                    ?>
                                    <p><strong>Duration:</strong> <?= $formatted_duration ?></p>
                                    <p><strong>Price:</strong> <?= htmlspecialchars($booking['payment']) ?>€</p>

                                    <div class="review">
                                        <p><strong>Your Rating:</strong></p>
                                        <?php if (empty($booking['owner_review'])): ?>
                                            <p>No review yet</p>
                                        <?php else: ?>
                                            <?= starReview($booking['owner_review']); ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="review">
                                        <p><strong>Provider's Rating:</strong></p>
                                        <?php if (empty($booking['provider_review'])): ?>
                                            <p>No review yet</p>
                                        <?php else: ?>
                                            <?= starReview($booking['provider_review']); ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (empty($booking['provider_review'])): ?>
                                        <form action="review.php" method="post">
                                            <input type="hidden" name="service_id" value="<?= htmlspecialchars($booking['service_id']) ?>">
                                            <input type="hidden" name="role" value="provider">
                                            <input type="submit" class="review-provider" value="Add Review">
                                        </form>
                                    <?php endif; ?>

                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>You have no past bookings.</p>
                        <?php endif; ?>
                    </section>

                <?php endif; ?>
                <section id="pets">
                    <h2>Pets</h2>
                    <!-- Add a new pet -->
                    <?php //TODO: dar layout a esta msg de erro 
                    ?>
                    <?php if (isset($_SESSION['msg_error'])): ?>
                        <p class="msg_error"><?= $_SESSION['msg_error'] ?></p>
                        <?php unset($_SESSION['msg_error']); ?>
                    <?php endif; ?>
                    <input type="checkbox" id="toggleForm" style="display: none;">
                    <label for="toggleForm" id="addPetButton" class="button">
                        <span class="add-icon">+</span>
                    </label>
                    <form id="newPetForm" action="../actions/action_addPet.php" method="post" enctype="multipart/form-data">
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
                            <label for="medicalneeds">Medical needs we should know about:</label>
                            <textarea name="medicalneeds" id="medicalneeds" rows="3" cols="30"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="profile_picture">Profile Picture:</label>
                            <input type="file" accept="image/*" id="profile_picture" name="profile_picture">
                        </div>

                        <input type="submit" value="Add Pet">
                    </form>
                    <!-- If there are pets -->
                    <?php if (!empty($petOwnerInfo[0]['pet_id'])): ?>
                        <div class="pets-container" id="addPetSection">
                            <?php foreach ($petOwnerInfo as $pet): ?>
                                <div class="pet-card">
                                    <div class="pet-image">
                                        <?php if (!empty($pet['pet_profile_picture'])): ?>
                                            <img src="../images/uploads/<?= htmlspecialchars($pet['pet_profile_picture']) ?>"
                                                alt="<?= htmlspecialchars($pet['pet_name']) ?>"
                                                class="pet-profile-pic">
                                        <?php endif; ?>
                                    </div>
                                    <div class="pet-info">
                                        <legend> <?= htmlspecialchars($pet['pet_name']) ?></legend>
                                        <p><strong>Species:</strong> <?= ucfirst(htmlspecialchars($pet['pet_species'])) ?></p>
                                        <p><strong>Size:</strong> <?= ucfirst(htmlspecialchars($pet['pet_size'])) ?></p>
                                        <?php if (!empty($pet['pet_age'])): ?> <!-- Verifica se o campo pet_age tem algum valor -->
                                            <p><strong>Birth Date:</strong> <?= htmlspecialchars($pet['pet_age']) ?></p>
                                        <?php endif; ?>
                                        <!-- Display medical needs if they exist -->
                                        <?php if (isset($medicalNeeds[$pet['pet_id']]) && !empty($medicalNeeds[$pet['pet_id']])): ?>


                                            <p class="medical-needs">
                                                <strong>Medical Needs:</strong>
                                                <?php foreach ($medicalNeeds[$pet['pet_id']] as $need): ?>
                                                    <?= htmlspecialchars($need['description']) ?>
                                                <?php endforeach; ?>
                                            </p>
                                        <?php else: ?>
                                            <p>No medical needs recorded.</p>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>You have no pets added.</p>
                    <?php endif; ?>
                </section>

            <?php else: ?>
                <p>No account information found.</p>
            <?php endif; ?>


        </div>
    </main>


    <?php include('../templates/footer_tpl.php'); ?>