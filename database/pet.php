<?php

function fetchMedicalNeeds(array $petOwnerInfo): array {
    global $dbh;
    $medicalNeeds = [];
    foreach ($petOwnerInfo as $pet) {
        $pet_id = $pet['pet_id'];  // Get pet ID
        
        // Fetch medical needs for this pet
        $medicalStmt = $dbh->prepare(
            "SELECT description 
            FROM MedicalNeed 
            JOIN PetMedicalNeed ON PetMedicalNeed.medicalNeed = MedicalNeed.id 
            WHERE PetMedicalNeed.pet = ?"
        );
        $medicalStmt->execute([$pet_id]);
        $medicalNeeds[$pet_id] = $medicalStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $medicalNeeds;
}

function getPets($user_id) {
    global $dbh;
    $stmt = $dbh->prepare('SELECT * FROM Pet WHERE owner = :id');
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getPetByName($user_id, $pet_name) {
    global $dbh;
    $stmt = $dbh->prepare('SELECT id FROM Pet WHERE name = :pet_name AND owner = :owner_id');
    $stmt->bindValue(':pet_name', $pet_name, PDO::PARAM_STR);
    $stmt->bindValue(':owner_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function insertPet($name, $species, $size, $birthdate, $profile_picture, $user_id) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Pet (name, species, size, birthdate, profile_picture, owner) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $species, $size, $birthdate, $profile_picture, $user_id]);
    return $dbh->lastInsertId();  // Retorna o ID do pet recém inserido
}

function insertMedicalNeed($description) {
    global $dbh;
    $stmt = $dbh->prepare('SELECT id FROM MedicalNeed WHERE description = ?');
    $stmt->execute([$description]);
    $existingNeed = $stmt->fetch();

    if (!$existingNeed) {
        $stmt = $dbh->prepare('INSERT INTO MedicalNeed (description) VALUES (?)');
        $stmt->execute([$description]);
        return $dbh->lastInsertId();  // Retorna o ID da nova necessidade médica inserida
    } else {
        return $existingNeed['id'];  // Se já existe, retorna o ID existente
    }
}

function insertPetMedicalNeed($pet_id, $medicalNeed_id) {
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO PetMedicalNeed (pet, medicalNeed) VALUES (?, ?)');
    $stmt->execute([$pet_id, $medicalNeed_id]);
}

function deletePetsFromUser($user_id) {
    global $dbh;
    $stmt = $dbh->prepare('DELETE FROM PetMedicalNeed WHERE pet IN (SELECT id FROM Pet WHERE owner = ?)');
    $stmt->execute([$user_id]);
    $stmt = $dbh->prepare('DELETE FROM Pet WHERE owner = ?');
    $stmt->execute([$user_id]);
}
?>