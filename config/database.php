<?php
error_reporting(E_ALL);

define('DB_SERVER', 'localhost:8889');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'bozarts');

// Connexion à la base de données
try {  
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if (!$conn) {
        throw new Exception("ERREUR : Impossible de se connecter. " . mysqli_connect_error());
    }
    
    // Ne pas fermer automatiquement la connexion - laissez les scripts le faire eux-mêmes
    // Les scripts PHP ferment automatiquement les connexions à la fin de leur exécution
    
} catch (Exception $e) {
    // Au lieu de tuer le script, retournons une erreur JSON si c'est une requête AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
        exit();
    } else {
        die($e->getMessage());
    }
}
?>