<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
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
    header('Location: ../account.php#pets');
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

// Função para inserir o pet no banco de dados
function insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Pet (name, species, size, birthdate, profile_picture, owner) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $species, $size, $birthdate, $profile_picture, $user_id]);
    return $dbh->lastInsertId();  // Retorna o ID do pet recém inserido
}

// Função para inserir a necessidade médica
function insertMedicalNeed($description) {
    global $dbh;
    // Verifica se a necessidade médica já existe
    $stmt = $dbh->prepare('SELECT id FROM MedicalNeed WHERE description = ?');
    $stmt->execute([$description]);
    $existingNeed = $stmt->fetch();

    // Se não existir, insere
    if (!$existingNeed) {
        $stmt = $dbh->prepare('INSERT INTO MedicalNeed (description) VALUES (?)');
        $stmt->execute([$description]);
        return $dbh->lastInsertId();  // Retorna o ID da nova necessidade médica inserida
    } else {
        return $existingNeed['id'];  // Se já existe, retorna o ID existente
    }
}

// Função para associar a necessidade médica ao pet
function insertPetMedicalNeed($pet_id, $medicalNeed_id) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO PetMedicalNeed (pet, medicalNeed) VALUES (?, ?)');
    $stmt->execute([$pet_id, $medicalNeed_id]);
}

try {
    $dbh = new PDO('sqlite:../database.db');
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inserir o pet e obter o ID
    $pet_id = insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id); // (A função insertPet retorna o id do último petinserido)

    // Processar as necessidades médicas como texto
    if (!empty($medicalneeds)) {
        // Inserir ou verificar a necessidade médica
        $medicalNeed_id = insertMedicalNeed($medicalneeds);
        // Associar a necessidade médica ao pet
        insertPetMedicalNeed($pet_id, $medicalNeed_id);
    }

    // Redirecionar para a página de conta após o sucesso
    header('Location: ../account.php');
    exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
