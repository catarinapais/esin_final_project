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


if (!empty($iban) && empty($service_type)) {
    $_SESSION["msg_error"] = "You must choose a service type if you want to provide a service.";
} elseif (!empty($service_type) && empty($iban)) {
    $_SESSION["msg_error"] = "You must fill the IBAN (account number) if you want to provide a service.";
}

if (isset($_SESSION["msg_error"])) {
    header('Location: ../views/register.php');
    exit;
}

require_once('../database/init.php');
require_once('../database/person.php');

try {

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
    header('Location: ../views/initialPage.php');
    exit();

} catch (PDOException $e) {
    // Em caso de erro, definir mensagem e redirecionar para a página de registro
    $_SESSION["msg_error"] = "Unsuccessful registration. Please try again.";
    header('Location: ../views/register.php');
    exit();
}
?>
