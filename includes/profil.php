<?php
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $query = "SELECT * FROM utilisateurs WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'Utilisateur non trouvé']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'ID du utilisateur non spécifié']);
}
?> 