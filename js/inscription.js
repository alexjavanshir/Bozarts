document.addEventListener('DOMContentLoaded', function() {
    const formulaire = document.querySelector('.inscription-form');
    const messageErreur = document.getElementById('message-erreur');
    const fermer = document.getElementById('fermer');
    const messageArea = document.getElementById('message-area');
    
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
    
    // Function to display messages dynamically
    function displayMessage(message, type) {
        messageArea.innerHTML = ''; // Clear previous messages
        const messageElement = document.createElement('div');
        messageElement.textContent = message;
        // Use existing error-message class for errors, and a new success class for success
        if (type === 'error') {
            messageElement.classList.add('error-message');
        } else {
            messageElement.classList.add('message', type);
        }
        messageArea.appendChild(messageElement);
    }
    
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
    
    formulaire.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous messages on new submission
        messageArea.innerHTML = '';

        // Vérification du captcha
        const captchaResponse = grecaptcha.getResponse();
        if (!captchaResponse) {
            displayMessage('Veuillez compléter le captcha', 'error');
            return;
        }

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const conditions = document.getElementById('conditions').checked;

        // Vérification des mots de passe
        if (password !== confirmPassword) {
            displayMessage('Les mots de passe ne correspondent pas', 'error');
            return;
        }

        // Vérification des conditions
        if (!conditions) {
            displayMessage('Vous devez accepter les conditions d\'utilisation', 'error');
            return;
        }

        try {
            const response = await fetch('../includes/inscription.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email,
                    password,
                    captchaResponse
                })
            });

            const data = await response.json();

            if (data.success) {
                displayMessage('Inscription réussie ! Vous allez être redirigé.', 'success');
                // Rediriger l'utilisateur après un court délai
                setTimeout(function() {
                    window.location.href = 'completer-profil.html'; // Ou la page de connexion
                }, 2000);
            } else {
                displayMessage(data.message || 'Une erreur est survenue lors de l\'inscription.', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            displayMessage('Une erreur est survenue lors de l\'envoi du formulaire.', 'error');
        }
    });
});