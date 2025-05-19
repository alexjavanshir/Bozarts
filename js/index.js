// Gestion du bandeau Join Us ou Ajouter Article selon le statut de connexion
document.addEventListener('DOMContentLoaded', async function() {
    const joinUsSection = document.querySelector('.join-us');
    
    if (joinUsSection) {
        try {
            const response = await fetch('../includes/check_session.php');
            const data = await response.json();
            
            if (data.type === 'artisan') {
                // L'utilisateur est connecté en tant qu'artisan, modifier le bandeau
                joinUsSection.innerHTML = `
                    <h2 class="section-title">Ajoutez votre article</h2>
                    <div class="rejoindre-text">
                        <p>En tant qu'artisan, vous pouvez ajouter vos créations à la plateforme et les proposer à la vente.</p>
                    </div>
                    <div class="artisan-buttons">
                        <a href="ajouter-produit.html" class="join-button">Ajouter un article</a>
                        <a href="mes-annonces.html" class="join-button">Mes annonces</a>
                    </div>
                `;
            }
            else if (data.type === 'client') {
                // L'utilisateur est connecté en tant que client, modifier le bandeau
                joinUsSection.innerHTML = `
                    <h2 class="section-title">Bienvenue sur Bozarts !</h2>
                    <div class="rejoindre-text">
                        <p>Des créations qui racontent une histoire.<br> Explore les œuvres de talents émergents, partage ta vision du monde et rejoins une communauté passionnée par l'art sous toutes ses formes.</p>
                    </div>
                    <a href="../pages/recherche.html?search=+" class="join-button">Découvrir</a>
                `;
            }
            else if (data.droit === 'admin') {
                // L'utilisateur est connecté en tant qu'admin, modifier le bandeau
                joinUsSection.innerHTML = `
                    <h2 class="section-title">ADMIN</h2>
                    <div class="rejoindre-text">
                        <p>Bienvenue dans l'interface d'administration.<br> Gérez les utilisateurs et les contenus de la plateforme.</p>
                    </div>
                    <div class="admin-buttons">
                        <a href="../pages/admin.html" class="join-button admin-button">Administration</a>
                        <a href="../pages/recherche.html?search=+" class="join-button">Découvrir</a>
                    </div>
                `;
            }
            // Si c'est un client non connecté, on garde le bandeau "Join Us" par défaut
            else {
                joinUsSection.innerHTML = ` 
                <h2 class="section-title">Bienvenue sur Bozarts - NON CONNCETE!</h2>
                <div class="rejoindre-text">
                    <p>Des créations qui racontent une histoire.<br> Explore les œuvres de talents émergents, partage ta vision du monde et rejoins une communauté passionnée par l'art sous toutes ses formes.</p>
                </div>
                <a href="../pages/recherche.html?search=+" class="join-button">Découvrir</a>
            `;
            }
        } catch (error) {
            console.error('Erreur lors de la vérification de la session:', error);
        }
    }

});

