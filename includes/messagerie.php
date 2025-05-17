<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les conversations de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT DISTINCT 
            CASE 
                WHEN m.expediteur_id = :user_id THEN m.destinataire_id 
                ELSE m.expediteur_id 
            END as other_user_id,
            u.nom,
            u.prenom,
            (SELECT MAX(date_envoi) FROM messages 
             WHERE (expediteur_id = :user_id AND destinataire_id = other_user_id)
                OR (expediteur_id = other_user_id AND destinataire_id = :user_id)) as last_message_date
        FROM messages m
        JOIN utilisateurs u ON u.id = CASE 
            WHEN m.expediteur_id = :user_id THEN m.destinataire_id 
            ELSE m.expediteur_id 
        END
        WHERE m.expediteur_id = :user_id OR m.destinataire_id = :user_id
        ORDER BY last_message_date DESC
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si une conversation spécifique est demandée
    if (isset($_GET['conversation_id'])) {
        $stmt = $pdo->prepare("
            SELECT m.*, 
                   u_exp.nom as expediteur_nom, 
                   u_exp.prenom as expediteur_prenom,
                   u_dest.nom as destinataire_nom, 
                   u_dest.prenom as destinataire_prenom
            FROM messages m
            JOIN utilisateurs u_exp ON m.expediteur_id = u_exp.id
            JOIN utilisateurs u_dest ON m.destinataire_id = u_dest.id
            WHERE (m.expediteur_id = :user_id AND m.destinataire_id = :conversation_id)
               OR (m.expediteur_id = :conversation_id AND m.destinataire_id = :user_id)
            ORDER BY m.date_envoi ASC
        ");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':conversation_id' => $_GET['conversation_id']
        ]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Marquer les messages comme lus
        $stmt = $pdo->prepare("
            UPDATE messages 
            SET lu = TRUE 
            WHERE destinataire_id = :user_id 
            AND expediteur_id = :conversation_id 
            AND lu = FALSE
        ");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':conversation_id' => $_GET['conversation_id']
        ]);

        echo json_encode(['messages' => $messages]);
        exit;
    }

    echo json_encode(['conversations' => $conversations]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur lors de la récupération des messages']);
}
?> 