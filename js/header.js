document.addEventListener('DOMContentLoaded', async function() {
    // Récupérer le statut de connexion
    try {
        const response = await fetch('../includes/check_session.php');
        const data = await response.json();
        console.log('Données reçues:', data); // Log pour le débogage
        
        // Récupérer le lien du profil dans l'en-tête
        const profileLink = document.querySelector('.header-right a:nth-child(3)');
        const userWelcome = document.getElementById('userWelcome');
        
        if (data.id) {
            // L'utilisateur est connecté, modifier le lien vers la page de profil
            profileLink.href = `profil.html?id=${data.id}`;
            
            // Afficher le nom de l'utilisateur
            if (data.nom && data.prenom) {
                console.log('Affichage du message de bienvenue'); // Log pour le débogage
                userWelcome.innerHTML = `<span class="welcome-text">${data.prenom} ${data.nom}</span>`;
                userWelcome.style.display = 'block';
            } else {
                console.log('Nom ou prénom manquant:', data); // Log pour le débogage
            }
        } else {
            // L'utilisateur n'est pas connecté, garder le lien vers la page de connexion
            profileLink.href = 'connexion.html';
            userWelcome.style.display = 'none';
        }
    } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
    }
});
