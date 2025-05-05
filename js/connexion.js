document.addEventListener('DOMContentLoaded', function() {
    const connexionForm = document.querySelector('.connexion-form');
    
    // Créer un élément pour afficher les messages d'erreur
    const errorContainer = document.createElement('div');
    errorContainer.className = 'error-message';
    errorContainer.style.display = 'none';
    
    // Insérer le conteneur d'erreur avant le bouton de soumission
    const submitButton = connexionForm.querySelector('.form-submit');
    connexionForm.insertBefore(errorContainer, submitButton);
    
    connexionForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Empêche le formulaire de s'envoyer avant de vérifier les données
        
        // Masquer le message d'erreur précédent s'il existe
        errorContainer.style.display = 'none';
        
        // Récupérer les données du formulaire
        const formData = new FormData(connexionForm);
        
        // Désactiver le bouton pendant la requête
        submitButton.disabled = true;
        submitButton.textContent = 'Connexion en cours...';

        
        fetch('../includes/connexion.php', { // Requête AJAX vers le serveur (sans recharger la page)
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
                submitButton.textContent = 'Connexion';
            }
        })
        .catch(error => {
            errorContainer.textContent = 'Une erreur est survenue lors de la connexion.';
            errorContainer.style.display = 'block';
            console.error('Erreur de connexion:', error);
            
            // Réactiver le bouton
            submitButton.disabled = false;
            submitButton.textContent = 'Connexion';
        });
    });
});
