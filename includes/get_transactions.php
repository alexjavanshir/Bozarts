<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

try {
    // Vérifier si l'utilisateur est un artisan
    $query = "SELECT type FROM utilisateurs WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $isArtisan = ($user['type'] === 'artisan');

    // Récupérer les commandes envoyées (pour tous les utilisateurs)
    $query_commandes_envoyees = "
        SELECT 
            c.id as commande_id,
            c.date_commande,
            c.statut,
            c.montant_total,
            c.adresse_livraison,
            GROUP_CONCAT(
                CONCAT(
                    p.nom, '|',
                    cp.quantite, '|',
                    cp.prix_unitaire
                ) SEPARATOR '||'
            ) as produits
        FROM commandes c
        LEFT JOIN commande_produits cp ON c.id = cp.commande_id
        LEFT JOIN produits p ON cp.produit_id = p.id
        WHERE c.client_id = ?
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ";

    // Récupérer les commandes reçues (pour les artisans)
    $query_commandes_recues = "
        SELECT 
            c.id as commande_id,
            c.date_commande,
            c.statut,
            c.montant_total,
            c.adresse_livraison,
            u.nom as client_nom,
            u.prenom as client_prenom,
            GROUP_CONCAT(
                CONCAT(
                    p.nom, '|',
                    cp.quantite, '|',
                    cp.prix_unitaire
                ) SEPARATOR '||'
            ) as produits
        FROM commandes c
        JOIN commande_produits cp ON c.id = cp.commande_id
        JOIN produits p ON cp.produit_id = p.id
        JOIN utilisateurs u ON c.client_id = u.id
        WHERE p.artisan_id = ?
        GROUP BY c.id
        ORDER BY c.date_commande DESC
    ";

    // Exécuter les requêtes
    $commandes_envoyees = [];
    $commandes_recues = [];

    // Récupérer les commandes envoyées
    $stmt = mysqli_prepare($conn, $query_commandes_envoyees);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $commandes_envoyees = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Récupérer les commandes reçues si c'est un artisan
    if ($isArtisan) {
        $stmt = mysqli_prepare($conn, $query_commandes_recues);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $commandes_recues = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Formater les données pour l'affichage
    function formaterCommandes($commandes, $isArtisan = false) {
        return array_map(function($commande) use ($isArtisan) {
            $produits = [];
            if ($commande['produits']) {
                foreach (explode('||', $commande['produits']) as $produit) {
                    list($nom, $quantite, $prix) = explode('|', $produit);
                    $produits[] = [
                        'nom' => $nom,
                        'quantite' => $quantite,
                        'prix' => $prix
                    ];
                }
            }
            
            $formatted = [
                'id' => $commande['commande_id'],
                'date' => date('d/m/Y H:i', strtotime($commande['date_commande'])),
                'statut' => $commande['statut'],
                'montant_total' => number_format($commande['montant_total'], 2, ',', ' '),
                'adresse_livraison' => $commande['adresse_livraison'],
                'produits' => $produits
            ];

            if ($isArtisan && isset($commande['client_nom'])) {
                $formatted['client'] = [
                    'nom' => $commande['client_nom'],
                    'prenom' => $commande['client_prenom']
                ];
            }

            return $formatted;
        }, $commandes);
    }

    echo json_encode([
        'success' => true,
        'commandesEnvoyees' => formaterCommandes($commandes_envoyees),
        'commandesRecues' => $isArtisan ? formaterCommandes($commandes_recues, true) : [],
        'isArtisan' => $isArtisan
    ]);

} catch (Exception $e) {
    error_log("Erreur lors de la récupération des commandes : " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des commandes'
    ]);
}
?> 