<?php
session_start();
require_once "../config/database.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../inscription.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
    $prenom = filter_var($_POST['prenom'], FILTER_SANITIZE_STRING);
    $type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
    $adresse = filter_var($_POST['adresse'], FILTER_SANITIZE_STRING);
    $telephone = filter_var($_POST['telephone'], FILTER_SANITIZE_STRING);

    // Mettre à jour le profil utilisateur
    $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, type = ?, adresse = ?, telephone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nom, $prenom, $type, $adresse, $telephone, $user_id);

    if ($stmt->execute()) {
        // Rediriger vers la page de profil
        header("Location: ../profil.html");
        exit();
    } else {
        die("Erreur lors de la mise à jour du profil : " . $stmt->error);
    }
}

// Si ce n'est pas une requête POST, rediriger vers le formulaire HTML
header("Location: ../completer-profil.html");
exit();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compléter votre profil - Bozarts</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main>
        <div class="form-container">
            <h1>COMPLÉTEZ VOTRE PROFIL</h1>
            <form action="completer-profil.php" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>

                <div class="form-group">
                    <label for="type">Type de compte</label>
                    <select id="type" name="type" required>
                        <option value="client">Client</option>
                        <option value="artisan">Artisan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea id="adresse" name="adresse" required></textarea>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" required>
                </div>

                <button type="submit" class="form-submit">Finaliser l'inscription</button>
            </form>
        </div>
    </main>
</body>
</html> 