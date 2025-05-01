<?php
require_once '../config/database.php';

// Vérifier si les données nécessaires sont présentes
if (isset($_POST['id']) && isset($_POST['field']) && isset($_POST['value'])) {
    $id = intval($_POST['id']);
    $field = $_POST['field'];
    $value = $_POST['value'];
    
    // Liste des champs autorisés pour éviter les injections SQL
    $allowed_fields = ['nom', 'prenom', 'email', 'phone', 'adresse', 'password', 'type'];
    
    if (!in_array($field, $allowed_fields)) {
        echo json_encode(['error' => 'Champ non autorisé']);
        exit;
    }
    
    // Traitement spécial pour le mot de passe (hachage)
    if ($field === 'password') {
        $value = password_hash($value, PASSWORD_DEFAULT);
    }
    
    // Mise à jour du champ dans la base de données
    // Utiliser un switch pour mapper les noms des champs du formulaire aux noms des colonnes en BDD
    switch($field) {
        case 'phone':
            $field = 'telephone';
            break;
        case 'password':
            $field = 'mot_de_passe';
            break;
    }
    
    $query = "UPDATE utilisateurs SET $field = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $value, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Erreur lors de la mise à jour: ' . mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'Données manquantes']);
}
?> 