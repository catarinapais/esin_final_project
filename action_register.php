<?php
session_start(); // Iniciar sessão

// Limpar mensagens de erro e sucesso
unset($_SESSION["msg_error"]);
unset($_SESSION["msg_success"]);

$name = $_POST['name'];
$phone_number = $_POST['phone_number'];
$address = $_POST['address'];
$email = $_POST['email'];
$city = $_POST['city'];
$iban = $_POST['iban'];
$password = $_POST['password'];
$service_type = $_POST['service_type'];

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
    $dbh = new PDO('sqlite:database.db');
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
    $_SESSION["msg_success"] = "Successful Registration! Welcome, $name.";

    // Redirecionar para a página principal
    header('Location: initialPage.php');
    exit();

} catch (PDOException $e) {
    // Em caso de erro, definir mensagem e redirecionar para a página de registro
    $_SESSION["msg_error"] = "Unsuccessful registration. Please try again.";
    header('Location: register.php');
    exit();
}
?>
