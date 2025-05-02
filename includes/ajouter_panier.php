<?php
session_start();
require_once '../config/database.php';

// Création du répertoire logs s'il n'existe pas
if (!file_exists("../logs")) {
    mkdir("../logs", 0755, true);
}

// Pour le débogage
$log_file = fopen("../logs/panier_debug.log", "a");
fwrite($log_file, "--------- " . date('Y-m-d H:i:s') . " ---------\n");
fwrite($log_file, "Session: " . print_r($_SESSION, true) . "\n");
fwrite($log_file, "POST: " . print_r($_POST, true) . "\n");

// Vérifier si l'utilisateur est connecté (utiliser la variable de session user_id)
if (!isset($_SESSION["user_id"])) {
    fwrite($log_file, "Non connecté, redirection vers connexion.html\n");
    fclose($log_file);
    header("location: ../pages/connexion.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION["user_id"];
    // S'assurer que produit_id est un entier
    $produit_id = intval(trim($_POST["produit_id"]));
    $quantite = intval(trim($_POST["quantite"]));

    fwrite($log_file, "Client ID: $client_id, Produit ID: $produit_id, Quantité: $quantite\n");

    // Vérification que le produit existe dans la base de données
    $sql_check = "SELECT id FROM produits WHERE id = ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "i", $produit_id);
        if (mysqli_stmt_execute($stmt_check)) {
            mysqli_stmt_store_result($stmt_check);
            if (mysqli_stmt_num_rows($stmt_check) == 0) {
                fwrite($log_file, "Produit ID $produit_id n'existe pas dans la base de données\n");
                fclose($log_file);
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
        fwrite($log_file, "SQL préparé: $sql avec client_id=$client_id, produit_id=$produit_id\n");
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            $num_rows = mysqli_stmt_num_rows($stmt);
            fwrite($log_file, "Nombre de lignes trouvées: $num_rows\n");
            
            if ($num_rows > 0) {
                // Mettre à jour la quantité si le produit existe déjà
                mysqli_stmt_bind_result($stmt, $quantite_existante);
                mysqli_stmt_fetch($stmt);
                $nouvelle_quantite = $quantite_existante + $quantite;
                
                $sql = "UPDATE paniers SET quantite = ? WHERE client_id = ? AND produit_id = ?";
                fwrite($log_file, "Mise à jour: $sql avec quantite=$nouvelle_quantite\n");
                
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iii", $nouvelle_quantite, $client_id, $produit_id);
                    $result = mysqli_stmt_execute($stmt);
                    fwrite($log_file, "Résultat de la mise à jour: " . ($result ? "Succès" : "Échec") . "\n");
                } else {
                    fwrite($log_file, "Erreur préparation UPDATE: " . mysqli_error($conn) . "\n");
                }
            } else {
                // Insérer un nouveau produit dans le panier
                $sql = "INSERT INTO paniers (client_id, produit_id, quantite) VALUES (?, ?, ?)";
                fwrite($log_file, "Insertion: $sql\n");
                
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iii", $client_id, $produit_id, $quantite);
                    $result = mysqli_stmt_execute($stmt);
                    fwrite($log_file, "Résultat de l'insertion: " . ($result ? "Succès" : "Échec") . "\n");
                    if (!$result) {
                        fwrite($log_file, "Erreur lors de l'insertion: " . mysqli_error($conn) . "\n");
                    }
                } else {
                    fwrite($log_file, "Erreur préparation INSERT: " . mysqli_error($conn) . "\n");
                }
            }
            
            fwrite($log_file, "Redirection vers panier.html\n");
            fclose($log_file);
            header("location: ../pages/panier.html");
            exit();
        } else {
            fwrite($log_file, "Erreur exécution: " . mysqli_error($conn) . "\n");
        }
        mysqli_stmt_close($stmt);
    } else {
        fwrite($log_file, "Erreur préparation SELECT: " . mysqli_error($conn) . "\n");
    }
    mysqli_close($conn);
}

fwrite($log_file, "Fin du script sans redirection\n");
fclose($log_file);
?>