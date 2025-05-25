<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Vous devez être connecté pour créer un événement']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $lieu = trim($_POST['lieu']);
    $createur_id = $_SESSION['user_id'];

    // Validation des données
    if (empty($titre) || empty($description) || empty($date_debut) || empty($date_fin) || empty($lieu)) {
        echo json_encode(['success' => false, 'error' => 'Tous les champs obligatoires doivent être remplis']);
        exit;
    }

    // Gestion de l'image
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/evenements/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'error' => 'Format d\'image non autorisé']);
            exit;
        }

        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image_url = 'assets/evenements/' . $newFileName;
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors du téléchargement de l\'image']);
            exit;
        }
    }

    try {
        // Préparer et exécuter la requête
        $query = "INSERT INTO evenements (createur_id, titre, description, date_debut, date_fin, lieu, image_url) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issssss", $createur_id, $titre, $description, $date_debut, $date_fin, $lieu, $image_url);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Événement créé avec succès']);
        } else {
            // Si l'insertion échoue et qu'une image a été uploadée, la supprimer
            if ($image_url && file_exists('../' . $image_url)) {
                unlink('../' . $image_url);
            }
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la création de l\'événement']);
        }
        
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        // Si une erreur survient et qu'une image a été uploadée, la supprimer
        if ($image_url && file_exists('../' . $image_url)) {
            unlink('../' . $image_url);
        }
        echo json_encode(['success' => false, 'error' => 'Une erreur est survenue : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
}
?> 