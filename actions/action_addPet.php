<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../views/login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Ir buscar os dados do formulário
$name = $_POST['name'];
$species = $_POST['species'];
$size = $_POST['size'];
$birthdate = $_POST['birthdate'];
$medicalneeds = $_POST['medicalneeds'];  // Considera que medicalneeds é um texto com todas as necessidades

if (!empty($birthdate) && $birthdate > date('Y-m-d')) {
    $_SESSION['msg_error'] = "Birthdate must be in the past.";
    header('Location: ../views/account.php#pets');
    exit;
}

// Verifica se o arquivo foi enviado
$profile_picture = null; // Variável que armazenará o nome do arquivo da imagem

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) { 
    $uploadDir = '../images/uploads/'; // Diretório onde as imagens serão armazenadas
    $fileName = basename($_FILES['profile_picture']['name']); // Usando basename() para obter o nome do arquivo
    $uploadFile = $uploadDir . $fileName; // Caminho completo para onde o arquivo será armazenado no servidor

    // Verifica o tipo de arquivo
    if (in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) { 
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profile_picture = $fileName; // Salva o nome do arquivo para inserir no banco de dados
        } else {
            echo "Failed to upload file.";
            exit;
        }
    } else {
        echo "Invalid file type. Only jpg, jpeg, png, and gif are allowed.";
        exit;
    }
}
else{ 
    $profile_picture = 'imagemdefault.jpg';
}


require_once('../database/init.php');
require_once('../database/pet.php');

try {
    // Inserir o pet e obter o ID
    $pet_id = insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id); 
    // (A função insertPet retorna o id do último petinserido)

    // Processar as necessidades médicas como texto
    if (!empty($medicalneeds)) {
        // Inserir ou verificar a necessidade médica
        $medicalNeed_id = insertMedicalNeed($medicalneeds);
        // Associar a necessidade médica ao pet
        insertPetMedicalNeed($pet_id, $medicalNeed_id);
    }

    // Redirecionar para a página de conta após o sucesso
    header('Location: ../views/account.php');
    exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
