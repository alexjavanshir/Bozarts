<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];

        // Validation
        if ($password !== $confirm_password) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        // Vérifier si l'email existe déjà
        $check_email = "SELECT id FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête : " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Erreur d'exécution de la requête : " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            throw new Exception("Cet email est déjà utilisé.");
        }

        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur avec les informations minimales et des valeurs par défaut
        $sql = "INSERT INTO utilisateurs (email, mot_de_passe, type, nom, prenom) VALUES (?, ?, 'client', 'À compléter', 'À compléter')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête : " . $conn->error);
        }
        
        $stmt->bind_param("ss", $email, $hashed_password);
        if (!$stmt->execute()) {
            throw new Exception("Erreur d'exécution de la requête : " . $stmt->error);
        }

        $user_id = $stmt->insert_id;
        
        // Démarrer la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user_id;
        
        // Redirection vers completer-profil.html
        header("Location: http://localhost/Bozarts/completer-profil.html");
        exit();
    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    // Si ce n'est pas une requête POST, rediriger vers la page d'inscription
    header("Location: ../inscription.html");
    exit();
}
?>