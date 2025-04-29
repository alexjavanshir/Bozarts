<?php
define('DB_SERVER', 'herogu.garageisep.com');
define('DB_USERNAME', 'ryDpk02xS4_bozarts');
define('DB_PASSWORD', 'S5G9rjvfzQYDGk0d');
define('DB_NAME', 'khL3GZYk4J_bozarts');

// Connexion à la base de données
try {  
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if (!$conn) {
        throw new Exception("ERREUR : Impossible de se connecter. " . mysqli_connect_error());
    }
} catch (Exception $e) {
    die($e->getMessage());
}
?>