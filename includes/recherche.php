<?php
// Définir le header content-type pour JSON
header('Content-Type: application/json');

// Vérifier si le fichier de configuration existe
if (!file_exists('../config/database.php')) {
    echo json_encode(array("error" => "Fichier de configuration introuvable"));
    exit;
}

// Essayer de se connecter à la base de données
try {
    require_once '../config/database.php';
    
    // Vérifier si la connexion a été établie
    if (!isset($conn) || !$conn) {
        echo json_encode(array("error" => "Connexion à la base de données échouée"));
        exit;
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
        $min_price = isset($_GET["min_price"]) ? floatval($_GET["min_price"]) : 0;
        $max_price = isset($_GET["max_price"]) ? floatval($_GET["max_price"]) : PHP_FLOAT_MAX;

        // Pour déboguer
        error_log("Recherche: " . $search);
        
        // Requête simplifiée sans la table artisans
        $sql = "SELECT * FROM produits WHERE (nom LIKE ? OR description LIKE ? OR categorie LIKE ?)";
        
        if ($min_price > 0 || $max_price < PHP_FLOAT_MAX) {
            $sql .= " AND prix BETWEEN ? AND ?";
        }
        
        $search_param = "%" . $search . "%";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            if ($min_price > 0 || $max_price < PHP_FLOAT_MAX) {
                mysqli_stmt_bind_param($stmt, "sssdd", $search_param, $search_param, $search_param, $min_price, $max_price);
            } else {
                mysqli_stmt_bind_param($stmt, "sss", $search_param, $search_param, $search_param);
            }
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $products = array();
                
                while ($row = mysqli_fetch_assoc($result)) {
                    // Formater le prix pour l'affichage
                    $row['formatted_price'] = number_format($row['prix'], 2, ',', ' ') . ' €';
                    // Log l'image_url pour débogage
                    error_log("Recherche - image_url pour produit ID " . $row['id'] . ": " . $row['image_url']);
                    $products[] = $row;
                }
                
                echo json_encode($products);
            } else {
                echo json_encode(array("error" => "Erreur d'exécution: " . mysqli_error($conn)));
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(array("error" => "Erreur de préparation: " . mysqli_error($conn)));
        }
        
        mysqli_close($conn);
    } else {
        echo json_encode(array("error" => "Méthode non autorisée"));
    }
} catch (Exception $e) {
    echo json_encode(array("error" => "Exception: " . $e->getMessage()));
}
?>