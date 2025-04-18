<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialisation de la session
session_start();

require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : null;
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : null;

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Requête préparée correcte
        $sql = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête : " . $conn->error);
        }

        // Bind du paramètre email
        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur d'exécution de la requête : " . $stmt->error);
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
                
                // Redirection selon le type d'utilisateur
                header("Location: ../profil.html");
                exit();
            } else {
                $error = "Email ou mot de passe incorrect.";
                echo "<h1>$error<h1>";
            }
        } else {
            $error = "Email ou mot de passe incorrect.";
            echo "<h1>$error<h1>";
        }
    }
}
?>
