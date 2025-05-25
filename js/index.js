// Gestion du bandeau Join Us ou Ajouter Article selon le statut de connexion
document.addEventListener('DOMContentLoaded', async function() {
    const joinUsSection = document.querySelector('.join-us');
    
    if (joinUsSection) {
        try {
            const response = await fetch('../includes/check_session.php');
            const data = await response.json();
            
            if (data.droit === 'admin') {
                // L'utilisateur est connecté en tant qu'admin, modifier le bandeau
                joinUsSection.innerHTML = `
                    <h2 class="section-title">Compte Adminisatrateur</h2>
                    <div class="rejoindre-text">
                        <p>Bienvenue dans l'interface d'administration.<br> Gérez les utilisateurs et les contenus de la plateforme.</p>
                    </div>
                    <div class="admin-buttons">
                        <a href="admin.html" class="join-button admin-button">Administration</a>
                    </div>
                `;
            }
            else if (data.type === 'artisan') {
                // L'utilisateur est connecté en tant qu'artisan, modifier le bandeau
                joinUsSection.innerHTML = `
                    <h2 class="section-title">Gérez votre compte artisan</h2>
                    <div class="rejoindre-text">
                        <p>En tant qu'artisan, vous pouvez ajouter vos créations à la plateforme et les proposer à la vente.</p>
                    </div>
                    <div class="artisan-buttons">
                        <a href="ajouter-produit.html" class="join-button">Ajouter un article</a>
                        <a href="mes-annonces.html" class="join-button">Mes annonces</a>
                        <a href="ajouter-evenement.html" class="join-button">Ajouter un événement</a>
                    </div>
                `;
            }
            else if (data.type === 'client') {
                // L'utilisateur est connecté en tant que client, modifier le bandeau
                joinUsSection.innerHTML = `
                    <h2 class="section-title">Content de vous voir ${data.nom} ${data.prenom} !</h2>
                    <div class="rejoindre-text">
                        <p>Bozarts; Des créations qui racontent une histoire.<br> Explore les œuvres de talents émergents, partage ta vision du monde et rejoins une communauté passionnée par l'art sous toutes ses formes.</p>
                    </div>
                    <a href="recherche.html?search=+" class="join-button">Découvrir les œuvres</a>
                `;
            }
            // Si c'est un client non connecté, on garde le bandeau "Join Us" par défaut
            else {
                joinUsSection.innerHTML = ` 
                <h2 class="section-title">Bienvenue sur Bozarts !</h2>
                <div class="rejoindre-text">
                    <p>Des créations qui racontent une histoire.<br> Explore les œuvres de talents émergents, partage ta vision du monde et rejoins une communauté passionnée par l'art sous toutes ses formes.</p>
                </div>
                <a href="../pages/recherche.html?search=+" class="join-button">Découvrir les œuvres</a>
            `;
            }
        } catch (error) {
            console.error('Erreur lors de la vérification de la session:', error);
        } finally {
             // Charger les événements indépendamment du statut de connexion initial
            loadEvenements();
        }
    }
});

// Fonction pour charger les événements
async function loadEvenements() {
    try {
        const sessionResponse = await fetch('../includes/check_session.php');
        const sessionData = await sessionResponse.json();
        const currentUserId = sessionData.id || null; // Get the current user's ID

        const response = await fetch('../includes/get-evenements.php');
        const evenements = await response.json();
        
        const container = document.getElementById('evenements-container');
        
        if (evenements.error) {
            container.innerHTML = `<p class="error-message">${evenements.error}</p>`;
            return;
        }
        
        if (evenements.length === 0) {
            container.innerHTML = '<p>Aucun événement à venir pour le moment.</p>';
            return;
        }
        
        container.innerHTML = evenements.map(evenement => `
            <div class="event-card">
                ${evenement.image_url ? 
                    `<img src="../${evenement.image_url}" alt="${evenement.titre}" class="event-image">` :
                    `<div class="event-image-placeholder"></div>`
                }
                <div class="event-content">
                    <h3>${evenement.titre}</h3>
                    <p class="event-date">Du ${evenement.date_debut_formatted} au ${evenement.date_fin_formatted}</p>
                    <p class="event-location">📍 ${evenement.lieu}</p>
                    <p class="event-description">${evenement.description}</p>
                    <p class="event-creator">Organisé par ${evenement.createur_prenom} ${evenement.createur_nom}</p>
                    <p class="event-participants">${evenement.nombre_participants} participant(s)</p>
                    <div class="event-actions">
                        <button onclick="participerEvenement(${evenement.id})" class="join-button">Participer</button>
                        ${currentUserId && currentUserId == evenement.createur_id ? 
                            `<button onclick="supprimerEvenement(${evenement.id})" class="delete-button">Supprimer</button>` : 
                            ''}
                    </div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Erreur lors du chargement des événements:', error);
        document.getElementById('evenements-container').innerHTML = 
            '<p class="error-message">Une erreur est survenue lors du chargement des événements.</p>';
    }
}

// Fonction pour participer à un événement
async function participerEvenement(eventId) {
    try {
        const response = await fetch('../includes/participer-evenement.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ event_id: eventId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Vous participez maintenant à cet événement !');
            loadEvenements(); // Recharger les événements pour mettre à jour le nombre de participants
        } else {
            alert(data.error || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de l\'inscription à l\'événement');
    }
}

// Nouvelle fonction pour supprimer un événement
async function supprimerEvenement(eventId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
        try {
            const response = await fetch('../includes/supprimer-evenement.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ event_id: eventId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Événement supprimé avec succès !');
                loadEvenements(); // Recharger les événements après suppression
            } else {
                alert(data.error || 'Une erreur est survenue lors de la suppression');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la suppression de l\'événement');
        }
    }
}

