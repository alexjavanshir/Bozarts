<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("location: ../pages/connexion.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION["user_id"];
    // S'assurer que produit_id est un entier
    $produit_id = intval(trim($_POST["produit_id"]));
    $quantite = intval(trim($_POST["quantite"]));

    // Vérification que le produit existe dans la base de données
    $sql_check = "SELECT id FROM produits WHERE id = ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "i", $produit_id);
        if (mysqli_stmt_execute($stmt_check)) {
            mysqli_stmt_store_result($stmt_check);
            if (mysqli_stmt_num_rows($stmt_check) == 0) {
                echo json_encode(['error' => 'Produit non trouvé']);
                exit;
            }
        }
        mysqli_stmt_close($stmt_check);
    }

    // Vérifier si le produit existe déjà dans le panier
    $sql = "SELECT quantite FROM paniers WHERE client_id = ? AND produit_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $client_id, $produit_id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            $num_rows = mysqli_stmt_num_rows($stmt);
            
            if ($num_rows > 0) {
                // Mettre à jour la quantité si le produit existe déjà
                mysqli_stmt_bind_result($stmt, $quantite_existante);
                mysqli_stmt_fetch($stmt);
                $nouvelle_quantite = $quantite_existante + $quantite;
                
                $sql = "UPDATE paniers SET quantite = ? WHERE client_id = ? AND produit_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iii", $nouvelle_quantite, $client_id, $produit_id);
                    mysqli_stmt_execute($stmt);
                }
            } else {
                // Insérer un nouveau produit dans le panier
                $sql = "INSERT INTO paniers (client_id, produit_id, quantite) VALUES (?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iii", $client_id, $produit_id, $quantite);
                    mysqli_stmt_execute($stmt);
                }
            }
            
            header("location: ../pages/panier.html");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}
?>