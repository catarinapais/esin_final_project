<?php
session_start(); // Iniciar sessão

// Limpar mensagens de erro e sucesso
unset($_SESSION["msg_error"]);
unset($_SESSION["msg_success"]);

$name = $_POST['name'];
$phone_number = $_POST['phone_number'];
$address = $_POST['address'];
$email = strtolower($_POST['email']);
$city = strtolower($_POST['city']); //saving it in lowercase to avoid case-sensitive issues
$iban = strtoupper($_POST['iban']); //iban em maiusculas (para verificar a sequencia do PT50)
$password = $_POST['password'];
$service_type = $_POST['service_type'];

if (strlen((string)$phone_number) != 9) {
    $_SESSION["msg_error"] = "The phone number must contain 9 digits.";
} elseif (!empty($iban) && empty($service_type)) {
    $_SESSION["msg_error"] = "You must choose a service type if you want to provide a service.";
} elseif (!empty($service_type) && empty($iban)) {
    $_SESSION["msg_error"] = "You must fill the IBAN (account number) if you want to provide a service.";
} elseif (!empty($iban) && (!is_string($iban) || !preg_match('/^PT50\d{21}$/', str_replace(' ', '', $iban)))) {
    $_SESSION["msg_error"] = "Invalid IBAN. It must start with PT50 followed by 21 digits.";
} elseif (strlen(trim($password)) < 8) {
    $_SESSION["msg_error"] = "The password must have at least 8 characters.";
} elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[\W_]/', $password)) {
    $_SESSION["msg_error"] = "The password must contain at least one uppercase letter and one special character.";
}

if (isset($_SESSION["msg_error"])) {
    header('Location: ../register.php');
    exit;
}

function insertPerson($name, $phone_number, $address, $email, $city, $iban, $password, $service_type) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Person (name, phone_number, address, email, city, password) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute(array($name, $phone_number, $address, $email, $city, hash('sha256', $password)));
    $personId = $dbh->lastInsertId();
    insertProvider($personId, $iban, $service_type);
    insertOwner($personId);
    return $personId; // Retorna o ID do novo usuário
}

function insertProvider($person, $iban, $service_type) {
    global $dbh;
    if (!empty($iban) && !empty($service_type)) {
        $stmt = $dbh->prepare('INSERT INTO ServiceProvider (person, iban, service_type) VALUES (?, ?, ?)');
        $stmt->execute(array($person, $iban, $service_type));
    }
}

function insertOwner($person) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO PetOwner (person) VALUES (?)');
    $stmt->execute(array($person));
}

try {
    $dbh = new PDO('sqlite:../database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inserir usuário e obter ID
    $personId = insertPerson($name, $phone_number, $address, $email, $city, $iban, $password, $service_type);

    // Criar sessão para o usuário recém-registrado
    $_SESSION['id'] = $personId;
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $name;
    $_SESSION['phone_number'] = $phone_number;
    $_SESSION['city'] = $city;
    $_SESSION['address'] = $address;
    $_SESSION['iban'] = $iban;
    $_SESSION['service_type'] = $service_type;
    $_SESSION["msg_success"] = "Successful Registration! Welcome, $name.";

    // Redirecionar para a página principal
    header('Location: ../initialPage.php');
    exit();

} catch (PDOException $e) {
    // Em caso de erro, definir mensagem e redirecionar para a página de registro
    $_SESSION["msg_error"] = "Unsuccessful registration. Please try again.";
    header('Location: ../register.php');
    exit();
}
?>
