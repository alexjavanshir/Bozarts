<?php
// Définir le header content-type
header('Content-Type: text/html; charset=utf-8');

// Afficher les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fonction pour exécuter les requêtes SQL du fichier init.sql
function executeInitSQL() {
    global $conn;
    
    // Récupérer le contenu du fichier init.sql
    $initSQL = file_get_contents('database/init.sql');
    
    if (!$initSQL) {
        return "Erreur: Impossible de lire le fichier init.sql";
    }
    
    // Diviser le fichier en requêtes individuelles
    $queries = explode(';', $initSQL);
    
    // Exécuter chaque requête
    $error = false;
    $results = [];
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        $result = mysqli_query($conn, $query);
        if (!$result) {
            $error = true;
            $results[] = "Erreur dans la requête: " . mysqli_error($conn) . "<br>Requête: " . htmlspecialchars($query);
        } else {
            $results[] = "Succès: " . htmlspecialchars(substr($query, 0, 50)) . "...";
        }
    }
    
    if ($error) {
        return "Certaines requêtes ont échoué:<br>" . implode("<br>", $results);
    } else {
        return "Toutes les requêtes ont été exécutées avec succès!";
    }
}

// Essayer de se connecter à la base de données
try {
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Vérifier la connexion
    if (!$conn) {
        throw new Exception("Erreur de connexion: " . mysqli_connect_error());
    }
    
    echo "<h1>Test de connexion à la base de données</h1>";
    echo "<p>Connexion réussie à la base de données!</p>";
    
    // Exécuter les requêtes d'initialisation
    echo "<h2>Initialisation de la base de données</h2>";
    echo "<p>" . executeInitSQL() . "</p>";
    
    // Fermer la connexion
    mysqli_close($conn);
    
} catch (Exception $e) {
    die("<h1>Erreur</h1><p>" . $e->getMessage() . "</p>");
}
?> 