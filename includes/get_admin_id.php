<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../config/database.php';

header('Content-Type: application/json');

// Récupérer l'ID de l'administrateur
$query = "SELECT id, nom, prenom, email, droit FROM utilisateurs WHERE droit = 'admin' ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $admins = [];
    while ($admin = mysqli_fetch_assoc($result)) {
        $admins[] = $admin;
    }
    
    // Prendre le premier admin trouvé
    $admin = $admins[0];
    
    // Log pour le débogage
    error_log("Admin trouvé - ID: " . $admin['id'] . ", Nom: " . $admin['nom'] . ", Droit: " . $admin['droit']);
    
    echo json_encode([
        'admin_id' => $admin['id'],
        'nom' => $admin['nom'],
        'prenom' => $admin['prenom'],
        'email' => $admin['email'],
        'droit' => $admin['droit']
    ]);
} else {
    error_log("Aucun administrateur trouvé dans la base de données");
    echo json_encode(['error' => 'Aucun administrateur trouvé']);
}

mysqli_close($conn); 