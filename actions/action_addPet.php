<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Pega os dados do formulário
$name = $_POST['name'];
$species = $_POST['species'];
$size = $_POST['size'];
$birthdate = $_POST['birthdate'];

if (!empty($birthdate) && $birthdate > date('Y-m-d')) {
    $_SESSION['msg_error'] = "Birthdate must be in the past.";
    header('Location: ../account.php#pets');
    exit;
}

// Verifica se o arquivo foi enviado
$profile_picture = null; // Variável que armazenará o nome do arquivo da imagem

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) { 
    $uploadDir = '../images/uploads/'; // Diretório onde as imagens serão armazenadas
    $fileName = basename($_FILES['profile_picture']['name']); //Usa a função basename() para extrair o nome do arquivo enviado, removendo qualquer caminho extra (se houver).
    $uploadFile = $uploadDir . $fileName; //Concatena o diretório de upload com o nome do arquivo para criar o caminho completo onde o arquivo será salvo no servidor.

    // Verifica o tipo de arquivo
    if (in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) { //obtém a extensão do ficheiro e verifica se está dentro das extensões aceites
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profile_picture = $fileName; // Salva o nome do arquivo para inserir no banco de dados
        } else {
            // Se falhar o upload
            echo "Failed to upload file.";
            exit;
        }
    } else {
        echo "Invalid file type. Only jpg, jpeg, png, and gif are allowed.";
        exit;
    }
}





// Função para inserir o pet no banco de dados
function insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Pet (name, species, size, birthdate, profile_picture, owner) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $species, $size, $birthdate, $profile_picture, $user_id]);
}

try {
    $dbh = new PDO('sqlite:../database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inserir o pet
    insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id);

    // Redirecionar para a página de conta após o sucesso
    header('Location: ../account.php');
    exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
