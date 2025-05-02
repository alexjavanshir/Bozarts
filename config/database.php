<?php
define('DB_SERVER', 'localhost');
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
    die($e->getMessage());
}
?>