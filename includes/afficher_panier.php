<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'not_logged_in',
        'panier' => [],
        'total' => 0
    ]);
    exit;
}

$client_id = $_SESSION["user_id"];
$panier = array();
$total = 0;

try {
    // Récupérer les produits du panier
    $sql = "SELECT p.id, p.nom, p.prix, p.image_url, pan.quantite, pan.produit_id 
            FROM paniers pan 
            JOIN produits p ON pan.produit_id = p.id 
            WHERE pan.client_id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $client_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $panier[] = [
                    'id' => (int)$row['id'],
                    'produit_id' => (int)$row['produit_id'],
                    'nom' => $row['nom'],
                    'prix' => (float)$row['prix'],
                    'image_url' => $row['image_url'],
                    'quantite' => (int)$row['quantite']
                ];
                $total += (float)$row['prix'] * (int)$row['quantite'];
            }
        } else {
            throw new Exception("Erreur lors de l'exécution de la requête");
        }
        mysqli_stmt_close($stmt);
    } else {
        throw new Exception("Erreur lors de la préparation de la requête");
    }

    // Retourner les données en JSON
    header('Content-Type: application/json');
    
    $response = [
        'panier' => $panier,
        'total' => $total
    ];
    
    $json_response = json_encode($response);
    
    if ($json_response === false) {
        throw new Exception("Erreur lors de l'encodage JSON: " . json_last_error_msg());
    }
    
    echo $json_response;
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
        'panier' => [],
        'total' => 0
    ]);
}
?> 