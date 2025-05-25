document.addEventListener('DOMContentLoaded', function() {
    const eventForm = document.getElementById('event-form');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    // Définir la date minimale comme maintenant
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    
    // Définir les attributs min pour les champs de date
    dateDebut.min = minDateTime;
    dateFin.min = minDateTime;

    // Validation des dates
    function validateDates() {
        const debut = new Date(dateDebut.value);
        const fin = new Date(dateFin.value);
        
        if (debut && fin) {
            if (fin <= debut) {
                alert('La date de fin doit être postérieure à la date de début');
                dateFin.value = '';
                return false;
            }
        }
        return true;
    }

    dateFin.addEventListener('change', validateDates);
    dateDebut.addEventListener('change', function() {
        if (dateFin.value) {
            validateDates();
        }
    });

    eventForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Vérifier les dates avant l'envoi
        if (!validateDates()) {
            return;
        }

        // Vérifier que l'utilisateur est connecté
        try {
            const sessionResponse = await fetch('../includes/check_session.php');
            const sessionData = await sessionResponse.json();
            
            if (!sessionData.type) {
                alert('Vous devez être connecté pour créer un événement');
                window.location.href = 'connexion.html';
                return;
            }

            // Créer un objet FormData pour envoyer les données du formulaire
            const formData = new FormData(eventForm);

            // Ajouter l'ID de l'utilisateur
            formData.append('createur_id', sessionData.id);

            // Envoyer les données au serveur
            const response = await fetch('../includes/ajouter-evenement.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert('Événement créé avec succès !');
                window.location.href = 'index.html';
            } else {
                alert('Erreur lors de la création de l\'événement : ' + data.error);
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la création de l\'événement');
        }
    });
}); 