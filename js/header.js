document.addEventListener('DOMContentLoaded', async function() {
    // Récupérer le statut de connexion
    try {
        const response = await fetch('../includes/check_session.php');
        const data = await response.json();
        
        // Récupérer le lien du profil dans l'en-tête
        const profileLink = document.querySelector('.header-right a:nth-child(3)');
        
        if (data.id) {
            // L'utilisateur est connecté, modifier le lien vers la page de profil
            profileLink.href = `profil.html?id=${data.id}`;
        } else {
            // L'utilisateur n'est pas connecté, garder le lien vers la page de connexion
            profileLink.href = 'connexion.html';
        }
    } catch (error) {
        console.error('Erreur lors de la vérification de la session:', error);
    }
});
