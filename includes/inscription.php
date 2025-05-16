<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactiver l'affichage des erreurs pour éviter qu'elles contaminent le JSON

require_once "../config/database.php";

// Fonction pour valider le mot de passe
function validatePassword($password) {
    // Vérifier la longueur minimale
    if (strlen($password) < 8) {
        return false;
    }
    
    // Vérifier la présence d'au moins un caractère spécial
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+/', $password)) {
        return false;
    }
    
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];

        // Validation de la correspondance des mots de passe
        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas!']);
            exit();
        }
        
        // Validation de la complexité du mot de passe
        if (!validatePassword($password)) {
            echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères et un caractère spécial.']);
            exit();
        }

        // Vérifier si l'email existe déjà
        $check_email = "SELECT id FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête.']);
            exit();
        }
        
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Erreur d\'exécution de la requête.']);
            exit();
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé.']);
            exit();
        }

        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur avec les informations minimales et des valeurs par défaut
        $sql = "INSERT INTO utilisateurs (email, mot_de_passe, type, nom, prenom) VALUES (?, ?, 'client', 'À compléter', 'À compléter')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erreur de préparation de la requête d\'insertion.']);
            exit();
        }
        
        $stmt->bind_param("ss", $email, $hashed_password);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du compte.']);
            exit();
        }

        $user_id = $stmt->insert_id;
        
        // Démarrer la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_type'] = 'client';
        $_SESSION['user_email'] = $email;
        
        // Réponse JSON de succès
        echo json_encode([
            'success' => true, 
            'redirect' => '../pages/completer-profil.html'
        ]);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        exit();
    }
} else {
    // Si ce n'est pas une requête POST, envoyer une erreur JSON
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}