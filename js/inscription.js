document.addEventListener('DOMContentLoaded', function() {
    const formulaire = document.querySelector('.inscription-form');
    const messageErreur = document.getElementById('message-erreur');
    const fermer = document.getElementById('fermer');
    
    // S'assurer que le message est caché initialement
    if (messageErreur) {
        messageErreur.style.display = 'none';
        
        // Gestionnaire pour fermer la pop-up
        if (fermer) {
            fermer.addEventListener('click', function() {
                messageErreur.classList.remove("open");
                messageErreur.classList.add("hidden");
                messageErreur.style.display = 'none';
            });
        }
    }
    
    // Créer un élément pour afficher les messages d'erreur (comme dans connexion.js)
    const errorContainer = document.createElement('div');
    errorContainer.className = 'error-message';
    errorContainer.style.display = 'none';
    
    // Insérer le conteneur d'erreur avant le bouton de soumission
    const submitButton = formulaire.querySelector('.form-submit');
    formulaire.insertBefore(errorContainer, submitButton);
    
    // Fonction pour valider le mot de passe
    function validatePassword(password) {
        // Vérifie la longueur minimale de 8 caractères
        if (password.length < 8) {
            return false;
        }
        
        // Vérifie la présence d'au moins un caractère spécial
        const specialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
        if (!specialChars.test(password)) {
            return false;
        }
        
        return true;
    }
    
    formulaire.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Masquer le message d'erreur précédent s'il existe
        errorContainer.style.display = 'none';
        
        // Récupération des valeurs du formulaire
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        // Vérification de la complexité du mot de passe
        if (!validatePassword(password)) {
            errorContainer.textContent = 'Le mot de passe doit contenir au moins 8 caractères et un caractère spécial.';
            errorContainer.style.display = 'block';
            return false;
        }
        
        // Vérification des mots de passe
        if (password !== confirmPassword) {
            errorContainer.textContent = 'Les mots de passe ne correspondent pas.';
            errorContainer.style.display = 'block';
            return false;
        }
        
        // Récupérer les données du formulaire
        const formData = new FormData(formulaire);
        
        // Désactiver le bouton pendant la requête
        submitButton.disabled = true;
        submitButton.textContent = 'Inscription en cours...';
        
        // Envoi des données via Fetch
        fetch('../includes/inscription.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirection en cas de succès
                window.location.href = data.redirect;
            } else {
                // Afficher le message d'erreur
                errorContainer.textContent = data.message;
                errorContainer.style.display = 'block';
                
                // Réactiver le bouton
                submitButton.disabled = false;
                submitButton.textContent = 'S\'inscrire';
            }
        })
        .catch(error => {
            errorContainer.textContent = 'Une erreur est survenue lors de l\'inscription.';
            errorContainer.style.display = 'block';
            console.error('Erreur d\'inscription:', error);
            
            // Réactiver le bouton
            submitButton.disabled = false;
            submitButton.textContent = 'S\'inscrire';
        });
    });
});