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
    
    formulaire.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Vérification du captcha
        const captchaResponse = grecaptcha.getResponse();
        if (!captchaResponse) {
            alert('Veuillez compléter le captcha');
            return;
        }

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        const conditions = document.getElementById('conditions').checked;

        // Vérification des mots de passe
        if (password !== confirmPassword) {
            alert('Les mots de passe ne correspondent pas');
            return;
        }

        // Vérification des conditions
        if (!conditions) {
            alert('Vous devez accepter les conditions d\'utilisation');
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
                window.location.href = 'completer-profil.html';
            } else {
                alert(data.message || 'Une erreur est survenue lors de l\'inscription');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'inscription');
        }
    });
});