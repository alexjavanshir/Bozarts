<?php
session_start();
require_once '../config/database.php';

// Pour le débogage
if (!file_exists("../logs")) {
    mkdir("../logs", 0755, true);
}
$log_file = fopen("../logs/panier_affichage.log", "a");
fwrite($log_file, "--------- " . date('Y-m-d H:i:s') . " ---------\n");
fwrite($log_file, "Session: " . print_r($_SESSION, true) . "\n");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    fwrite($log_file, "Non connecté, envoi réponse JSON avec erreur\n");
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'not_logged_in',
        'panier' => [],
        'total' => 0
    ]);
    fclose($log_file);
    exit;
}

$client_id = $_SESSION["user_id"];
$panier = array();
$total = 0;

fwrite($log_file, "Client ID: $client_id\n");

try {
    // Récupérer les produits du panier
    $sql = "SELECT p.id, p.nom, p.prix, p.image_url, pan.quantite, pan.produit_id 
            FROM paniers pan 
            JOIN produits p ON pan.produit_id = p.id 
            WHERE pan.client_id = ?";

    fwrite($log_file, "SQL préparé: $sql avec client_id=$client_id\n");

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $client_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            fwrite($log_file, "Requête exécutée avec succès\n");
            
            while ($row = mysqli_fetch_assoc($result)) {
                fwrite($log_file, "Produit trouvé: ID=" . $row['id'] . ", Nom=" . $row['nom'] . "\n");
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
            fwrite($log_file, "Nombre d'articles dans le panier: " . count($panier) . "\n");
            fwrite($log_file, "Total: $total\n");
        } else {
            fwrite($log_file, "Erreur exécution: " . mysqli_error($conn) . "\n");
            throw new Exception("Erreur lors de l'exécution de la requête");
        }
        mysqli_stmt_close($stmt);
    } else {
        fwrite($log_file, "Erreur préparation: " . mysqli_error($conn) . "\n");
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
        fwrite($log_file, "Erreur JSON: " . json_last_error_msg() . "\n");
        throw new Exception("Erreur lors de l'encodage JSON: " . json_last_error_msg());
    }
    
    fwrite($log_file, "Réponse JSON générée avec succès\n");
    echo $json_response;
    
} catch (Exception $e) {
    fwrite($log_file, "Exception: " . $e->getMessage() . "\n");
    
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
        'panier' => [],
        'total' => 0
    ]);
}

fwrite($log_file, "Fin du script\n");
fclose($log_file);
?> 