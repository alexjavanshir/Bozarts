<?php
// Désactiver l'affichage des erreurs pour éviter qu'elles ne corrompent la sortie JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Démarrer la session et inclure la configuration
session_start();
require_once '../config/database.php';

// Définir le type de contenu avant toute sortie
header('Content-Type: application/json');

// Fonction pour envoyer une réponse JSON et terminer le script
function sendJsonResponse($success, $message, $data = null) {
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit;
}

// Fonction pour logger les erreurs
function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $logMessage .= " - Context: " . json_encode($context);
    }
    error_log($logMessage);
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    logError("Tentative de paiement sans utilisateur connecté");
    sendJsonResponse(false, 'Utilisateur non connecté');
}

// Vérifier les données POST requises
if (!isset($_POST['address']) || !isset($_POST['postalCode']) || !isset($_POST['city'])) {
    logError("Données de livraison manquantes", $_POST);
    sendJsonResponse(false, 'Données de livraison manquantes');
}

try {
    // Récupérer les informations du panier
    $query = "
        SELECT p.*, pr.prix, pr.nom 
        FROM paniers p 
        JOIN produits pr ON p.produit_id = pr.id 
        WHERE p.client_id = ?
    ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $panier = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (empty($panier)) {
        logError("Tentative de paiement avec panier vide", ['user_id' => $_SESSION['user_id']]);
        sendJsonResponse(false, 'Le panier est vide');
    }

    // Calculer le montant total
    $montant_total = 0;
    foreach ($panier as $item) {
        $montant_total += $item['prix'] * $item['quantite'];
    }

    // Ajouter les frais de livraison
    $frais_livraison = 10;
    $montant_total += $frais_livraison;

    // Démarrer la transaction
    mysqli_begin_transaction($conn);
    logError("Transaction démarrée");

    try {
        // Préparer l'adresse complète
        $adresse_complete = trim($_POST['address'] . ', ' . $_POST['postalCode'] . ' ' . $_POST['city']);

        // Insérer la commande
        $query = "
            INSERT INTO commandes (client_id, statut, montant_total, adresse_livraison) 
            VALUES (?, 'en attente', ?, ?)
        ";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ids", $_SESSION['user_id'], $montant_total, $adresse_complete);
        mysqli_stmt_execute($stmt);
        
        $commande_id = mysqli_insert_id($conn);
        logError("Commande créée", ['commande_id' => $commande_id]);

        // Insérer les produits de la commande
        $query = "
            INSERT INTO commande_produits (commande_id, produit_id, quantite, prix_unitaire) 
            VALUES (?, ?, ?, ?)
        ";
        $stmt = mysqli_prepare($conn, $query);
        
        foreach ($panier as $item) {
            mysqli_stmt_bind_param($stmt, "iiid", 
                $commande_id,
                $item['produit_id'],
                $item['quantite'],
                $item['prix']
            );
            mysqli_stmt_execute($stmt);
        }
        logError("Produits de la commande insérés", ['commande_id' => $commande_id]);

        // Vider le panier
        $query = "DELETE FROM paniers WHERE client_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        logError("Panier vidé", ['user_id' => $_SESSION['user_id']]);

        // Valider la transaction
        mysqli_commit($conn);
        logError("Transaction validée", ['commande_id' => $commande_id]);

        // Envoyer la réponse de succès
        sendJsonResponse(true, 'Transaction effectuée avec succès !', ['commande_id' => $commande_id]);

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        mysqli_rollback($conn);
        logError("Erreur lors de la création de la commande", [
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
        sendJsonResponse(false, 'Erreur lors de la création de la commande');
    }

} catch (Exception $e) {
    logError("Erreur inattendue", [
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    sendJsonResponse(false, 'Une erreur inattendue est survenue');
}
?> 