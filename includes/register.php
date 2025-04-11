<?php
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $mot_de_passe = trim($_POST["mot_de_passe"]);
    $specialite = trim($_POST["specialite"]);
    $description = trim($_POST["description"]);
    $adresse = trim($_POST["adresse"]);
    $telephone = trim($_POST["telephone"]);

    // Validation des données
    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Vérification si l'email existe déjà
        $sql = "SELECT id FROM artisans WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    // Hash du mot de passe
                    $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

                    // Insertion dans la base de données
                    $sql = "INSERT INTO artisans (nom, prenom, email, mot_de_passe, specialite, description, adresse, telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($stmt = mysqli_prepare($conn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ssssssss", $nom, $prenom, $email, $hashed_password, $specialite, $description, $adresse, $telephone);
                        if (mysqli_stmt_execute($stmt)) {
                            header("location: ../connexion.html");
                            exit();
                        } else {
                            $error = "Une erreur est survenue. Veuillez réessayer.";
                        }
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>