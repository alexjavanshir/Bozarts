class userDisplay {
    constructor() {
        this.initializeElements();
        // On initialise d'abord les éléments avant de charger l'utilisateur
        this.initUser();
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

    // Initialise l'utilisateur
    async initUser() {
        this.userId = await this.getuserIdFromUrl();
        if (this.userId) {
            this.loaduser();
        }
    }

    // Récupère l'ID du utilisateur depuis l'URL
    async getuserIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const idFromUrl = urlParams.get('id');
        
        if (idFromUrl) {
            return idFromUrl;
        } else {
            // Si aucun ID dans l'URL, essayer de charger l'utilisateur connecté
            // Vous devez avoir une API qui renvoie l'ID de l'utilisateur connecté
            return await this.getCurrentUserId();
        }
    }
    
    // Récupère l'ID de l'utilisateur connecté
    async getCurrentUserId() {
        try {
            const response = await fetch('../includes/check_session.php');
            const data = await response.json();
            
            if (data.error) {
                // Rediriger vers la page de connexion si aucun utilisateur n'est connecté
                window.location.href = 'connexion.html';
                return null;
            }
            
            return data.id;
        } catch (error) {
            console.error('Erreur:', error);
            window.location.href = 'connexion.html';
            return null;
        }
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
        // Gestion des modals pour la modification des informations
        const modifyButtons = document.querySelectorAll('.modify-button');
        const closeButtons = document.querySelectorAll('.close-button');
        const saveButtons = document.querySelectorAll('.save-button');
        
        modifyButtons.forEach((button, index) => {
            button.addEventListener('click', (e) => {
                // Trouver le modal container associé au bouton
                const parentInfoGroup = button.closest('.info-group');
                const modalContainer = parentInfoGroup.querySelector('.modal-container');
                
                if (modalContainer) {
                    modalContainer.classList.add('active');
                }
            });
        });
        
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Fermer le modal parent
                const modalContainer = button.closest('.modal-container');
                if (modalContainer) {
                    modalContainer.classList.remove('active');
                }
            });
        });
        
        saveButtons.forEach(button => {
            button.addEventListener('click', () => {
                const field = button.getAttribute('data-field');
                const modalContainer = button.closest('.modal-container');
                const modal = button.closest('.modal');
                
                // Trouver l'input ou select dans le modal
                let input;
                if (field === 'type') {
                    input = modal.querySelector('select');
                } else {
                    input = modal.querySelector('input');
                }
                
                if (input && input.value) {
                    this.saveUserData(field, input.value);
                    
                    // Mettre à jour l'affichage
                    const parentInfoGroup = modalContainer.closest('.info-group');
                    const spanElement = parentInfoGroup.querySelector('span');
                    
                    if (spanElement) {
                        if (field === 'password') {
                            spanElement.textContent = '******'; // Ne pas afficher le mot de passe
                        } else {
                            spanElement.textContent = input.value;
                        }
                    }
                    
                    // Fermer le modal
                    modalContainer.classList.remove('active');
                } else {
                    alert('Veuillez remplir le champ');
                }
            });
        });
        
        // Fermer le modal si on clique en dehors
        document.addEventListener('click', (e) => {
            const modalContainers = document.querySelectorAll('.modal-container.active');
            modalContainers.forEach(container => {
                if (e.target === container) {
                    container.classList.remove('active');
                }
            });
        });
    }
    
    // Sauvegarde les données de l'utilisateur
    async saveUserData(field, value) {
        if (!this.userId) {
            this.showError('Aucun utilisateur spécifié');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('id', this.userId);
            formData.append('field', field);
            formData.append('value', value);
            
            const response = await fetch('../includes/changer_profil.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.error) {
                throw new Error(result.error);
            }
            
            // Afficher un message de succès
            alert('Mise à jour réussie !');
            
        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Erreur lors de la mise à jour des informations');
        }
    }
}

// Initialiser l'affichage du utilisateur quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser l'affichage de l'utilisateur
    new userDisplay();
    
    // Gestionnaire pour le bouton de déconnexion
    const logoutButton = document.querySelector('.logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', () => {
            // Confirmer la déconnexion
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                window.location.href = '../includes/logout.php';
            }
        });
    }
}); 