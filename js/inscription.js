document.addEventListener('DOMContentLoaded', function() {
    const formulaire = document.querySelector('.inscription-form');
    const messageErreur = document.getElementById('message-erreur');
    const fermer = document.getElementById('fermer');
    
    // Ensure the message is hidden initially
    messageErreur.style.display = 'none';
    
    // Gestionnaire pour fermer la pop-up
    fermer.addEventListener('click', function() {
        messageErreur.classList.remove("open");
        messageErreur.classList.add("hidden");
        messageErreur.style.display = 'none';
    });
    
    formulaire.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Récupération des valeurs du formulaire
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        // Vérification des mots de passe
        if (password !== confirmPassword) {
            messageErreur.classList.remove("hidden");
            messageErreur.classList.add("open");
            messageErreur.style.display = 'block';
            return false;
        }
        
        // Si les mots de passe correspondent, soumettre le formulaire
        formulaire.submit();
    });
});