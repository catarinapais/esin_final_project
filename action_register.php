<?php
session_start(); // sempre que queremos fazer algo com sessões, no início do ficheiro devemos fazer session_start()

// limpar ambas as mensagens de erro
unset($_SESSION["msg_error"]); // limpar a mensagem de erro
unset($_SESSION["msg_success"]); // limpar a mensagem de sucesso

$name = $_POST['name'];
$phone_number = $_POST['phone_number'];
$address = $_POST['address'];
$email = $_POST['email'];
$city = $_POST['city'];
$iban = $_POST['iban'];
$password = $_POST['password'];
$service_type = $_POST['service_type'];

function insertPerson($name, $phone_number, $address, $email, $city, $iban, $password, $service_type) {
    // temos de definir a variável $dbh como global, para podermos usar dentro da função
    global $dbh;
    //var_dump($name);
    $stmt = $dbh->prepare('INSERT INTO Person (name, phone_number, address, email, city, password) VALUES (?, ?, ?, ?, ?, ?)'); // inserir dentro da base de daods - usamos na mesma prepare
    //var_dump($phone_number);
    $stmt->execute(array($name, $phone_number, $address, $email, $city, hash('sha256',$password))); // executar o statement
    
    $person = $dbh->lastInsertId();
    insertProvider($person, $iban, $service_type);
    insertOwner($person);
}

function insertProvider($person, $iban, $service_type) {
    global $dbh;
    if (strlen($iban) == 0 || strlen($service_type) == 0) {
        // não adiciona provider se os dis campos deste não estiverem preenchidos
        return;
    } else {
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
    insertPerson($name, $phone_number, $address, $email, $city, $iban, $password, $service_type);
    $_SESSION["msg_success"] = "Successful Registration!"; // mensagem de sucesso
    //$_SESSION: variável superglobal - pode ser acessida em qualquer sítio do código (incluindo dentro de funções)
    header('Location: initialPage.php'); // redirecionar para a página principal

} catch (PDOException $e) { // apanhar erro para, caso haja algum problema, o programa continuar a funcionar e só mostrar uma mensagem ao user
    $_SESSION["msg_error"] = "Unsuccessful registration. Please try again."; // mensagem de erro
    // se a register falhou, deixamos estar no ficheiro que está (header)
    header('Location: register.php');
}

// para verificarmos se uma pessoa ao fazer login pôs a password certa, fazemos hash (com o mesmo algoritmo - neste caso 'sha256') e comparamos com o que está na base de dados 

// sempre que queremos fazer algo com sessões, no início do ficheiro devemos fazer session_start()

// verificar se já existe algum user com aquele username
// ver se conseguimos por restrições na passwrod - minimo e carcateres, pelos menos uma maiuscula, ...
?>