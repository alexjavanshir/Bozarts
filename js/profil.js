class userDisplay {
    constructor() {
        this.userId = this.getuserIdFromUrl();
        this.initializeElements();
        this.loaduser();
    }

    // Récupère les éléments du DOM
    initializeElements() {
        this.elements = {
            nom: document.getElementById('user_nom'),
            prenom: document.getElementById('user_prenom'),
            adresse: document.getElementById('user_adresse'),
            email: document.getElementById('user_email'),
            phone: document.getElementById('user_phone'),
            type: document.getElementById('user_type'),
        };

        // Initialiser les écouteurs d'événements
        this.initializeEventListeners();
    }

    // Récupère l'ID du utilisateur depuis l'URL
    getuserIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    // Charge les données du utilisateur
    async loaduser() {
        if (!this.userId) {
            this.showError('Aucun utilisateur spécifié');
            return;
        }

        try {
            const response = await fetch(`../includes/profil.php?id=${this.userId}`);
            const user = await response.json();

            if (user.error) {
                throw new Error(user.error);
            }

            this.updateuserDisplay(user);
        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Erreur lors du chargement du utilisateur');
        }
    }

    // Met à jour l'affichage du utilisateur
    updateuserDisplay(utilisaters) {
        this.elements.nom.textContent = utilisaters.nom;
        this.elements.prenom.textContent = utilisaters.prenom;
        this.elements.adresse.textContent = utilisaters.adresse;
        this.elements.email.textContent = utilisaters.email;
        this.elements.phone.textContent = utilisaters.telephone;
        this.elements.type.textContent = utilisaters.type;
        
        document.title = `${utilisaters.nom} - Bozarts`;
    }

    // Affiche une erreur
    showError(message) {
        alert(message);
    }

    // Initialise les écouteurs d'événements
    initializeEventListeners() {
        // Vous pouvez ajouter des gestionnaires d'événements ici
    }
}

// Initialiser l'affichage du utilisateur quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new userDisplay();
}); 