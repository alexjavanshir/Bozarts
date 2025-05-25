<?php

error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactiver l'affichage des erreurs pour éviter qu'elles contaminent le JSON

// Initialisation de la session
session_start();

require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : null;
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : null;

    if (empty($email) || empty($password)) {
        // Réponse JSON pour l'erreur
        echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs.']);
        exit();
    } else {
        // Requête préparée correcte
        $sql = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête.']);
            exit();
        }

        // Bind du paramètre email
        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Erreur d\'exécution de la requête.']);
            exit();
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Vérification du mot de passe avec password_verify
            if (password_verify($password, $user['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['type'];
                $_SESSION['user_email'] = $user['email'];
                
                // Réponse JSON de succès
                echo json_encode([
                    'success' => true, 
                    'redirect' => '../pages/index.html?id=' . $_SESSION['user_id']
                ]);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
            exit();
        }
    }
}
