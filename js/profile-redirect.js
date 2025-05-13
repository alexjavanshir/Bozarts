document.addEventListener('DOMContentLoaded', function() {
    // Récupérer l'élément de profil dans le header
    const profileLink = document.querySelector('a.icon-button img[alt="Profil"]').parentNode;
    
    // Fonction pour vérifier la session et mettre à jour le lien
    function checkSessionAndUpdateLink() {
        fetch('../includes/check_session.php')
            .then(response => response.json())
            .then(data => {
                console.log('Données reçues de check_session.php:', data);
                
                // Vérifier les différentes clés possibles pour le type d'utilisateur
                let userType = data.type || data.user_type || data.TYPE || data['type'];
                console.log('Type d\'utilisateur détecté:', userType);
                
                if (data.error) {
                    // Utilisateur non connecté
                    profileLink.href = 'connexion.html';
                    console.log('Utilisateur non connecté, lien vers connexion.html');
                } else {
                    // Utilisateur connecté, rediriger selon le type
                    if (userType === 'artisan') {
                        profileLink.href = 'mes-annonces.html';
                        console.log('Utilisateur artisan, lien vers mes-annonces.html');
                    } else {
                        profileLink.href = 'mes-transactions.html';
                        console.log('Utilisateur client, lien vers mes-transactions.html');
                    }
                }
            })
            .catch(error => {
                // En cas d'erreur, rediriger vers la connexion
                profileLink.href = 'connexion.html';
                console.error('Erreur:', error);
            });
    }
    
    // Vérifier la session au chargement de la page
    checkSessionAndUpdateLink();
    
    // Ajouter un événement click sur le lien de profil
    profileLink.addEventListener('click', function(event) {
        event.preventDefault();
        console.log('Clic sur le lien profil');
        
        fetch('../includes/check_session.php')
            .then(response => response.json())
            .then(data => {
                console.log('Données reçues après clic:', data);
                
                // Vérifier les différentes clés possibles pour le type d'utilisateur
                let userType = data.type || data.user_type || data.TYPE || data['type'];
                console.log('Type d\'utilisateur détecté après clic:', userType);
                
                if (data.error) {
                    // Utilisateur non connecté
                    console.log('Redirection vers connexion.html');
                    window.location.href = 'connexion.html';
                } else {
                    // Utilisateur connecté, rediriger selon le type
                    if (userType === 'artisan') {
                        console.log('Redirection vers mes-annonces.html');
                        window.location.href = 'mes-annonces.html';
                    } else {
                        console.log('Redirection vers mes-transactions.html');
                        window.location.href = 'mes-transactions.html';
                    }
                }
            })
            .catch(error => {
                // En cas d'erreur, rediriger vers la connexion
                window.location.href = 'connexion.html';
                console.error('Erreur:', error);
            });
    });
}); 