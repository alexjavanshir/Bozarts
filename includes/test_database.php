<?php
// Définir le header content-type
header('Content-Type: text/html; charset=utf-8');

echo "<h1>Test de connexion à la base de données</h1>";

try {
    // Vérifier si le fichier de configuration existe
    if (!file_exists('../config/database.php')) {
        echo "<p style='color:red'>ERREUR: Fichier de configuration introuvable</p>";
        exit;
    }
    
    echo "<p>Fichier de configuration trouvé, tentative de connexion...</p>";
    
    // Inclure le fichier de configuration
    require_once '../config/database.php';
    
    // Vérifier si la connexion a été établie
    if (!isset($conn)) {
        echo "<p style='color:red'>ERREUR: Variable \$conn non définie</p>";
        exit;
    }
    
    if (!$conn) {
        echo "<p style='color:red'>ERREUR: Connexion échouée: " . mysqli_connect_error() . "</p>";
        exit;
    }
    
    echo "<p style='color:green'>Connexion à la base de données réussie!</p>";
    
    // Tester une requête simple
    echo "<h2>Test de la requête sur la table produits</h2>";
    
    $sql = "SHOW TABLES";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo "<p style='color:red'>ERREUR: " . mysqli_error($conn) . "</p>";
    } else {
        echo "<p>Tables disponibles:</p>";
        echo "<ul>";
        while ($row = mysqli_fetch_row($result)) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
    
    // Tester la table produits
    $sql = "DESCRIBE produits";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        echo "<p style='color:red'>ERREUR: La table 'produits' n'existe pas: " . mysqli_error($conn) . "</p>";
    } else {
        echo "<p>Structure de la table produits:</p>";
        echo "<table border='1'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Fermer la connexion
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "<p style='color:red'>ERREUR: Exception: " . $e->getMessage() . "</p>";
}
?> 