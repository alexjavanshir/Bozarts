document.addEventListener('DOMContentLoaded', function() {
    const produitId = new URLSearchParams(window.location.search).get('id');
    if (!produitId) return;

    loadAvis();
    setupAvisForm();
    setupStarRating();
});

function setupStarRating() {
    const ratingInputs = document.querySelectorAll('.rating input');
    const ratingLabels = document.querySelectorAll('.rating label');

    ratingInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            // Réinitialiser toutes les étoiles
            ratingLabels.forEach(label => {
                label.querySelector('i').className = 'far fa-star';
            });

            // Activer les étoiles jusqu'à la note sélectionnée
            for (let i = 0; i <= index; i++) {
                ratingLabels[i].querySelector('i').className = 'fas fa-star';
            }
        });

        // Effet hover
        input.addEventListener('mouseenter', function() {
            ratingLabels.forEach((label, i) => {
                if (i <= index) {
                    label.querySelector('i').className = 'fas fa-star';
                } else {
                    label.querySelector('i').className = 'far fa-star';
                }
            });
        });
    });

    // Réinitialiser au survol de la div rating
    const ratingDiv = document.querySelector('.rating');
    ratingDiv.addEventListener('mouseleave', function() {
        const checkedInput = document.querySelector('.rating input:checked');
        if (checkedInput) {
            const index = Array.from(ratingInputs).indexOf(checkedInput);
            ratingLabels.forEach((label, i) => {
                label.querySelector('i').className = i <= index ? 'fas fa-star' : 'far fa-star';
            });
        } else {
            ratingLabels.forEach(label => {
                label.querySelector('i').className = 'far fa-star';
            });
        }
    });
}

function loadAvis() {
    const produitId = new URLSearchParams(window.location.search).get('id');
    fetch(`../includes/get_avis.php?produitId=${produitId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAvis(data.avis);
            } else {
                showError(data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur lors du chargement des avis');
        });
}

function displayAvis(avis) {
    const avisContainer = document.getElementById('avis-container');
    if (!avisContainer) return;

    if (avis.length === 0) {
        avisContainer.innerHTML = '<p class="no-avis">Aucun avis pour ce produit</p>';
        return;
    }

    const avisHTML = avis.map(avis => `
        <div class="avis-card">
            <div class="avis-header">
                <div class="avis-user">
                    <i class="fas fa-user-circle"></i>
                    <span>${avis.prenom} ${avis.nom}</span>
                </div>
                <div class="avis-note">
                    ${generateStars(avis.note)}
                </div>
            </div>
            <div class="avis-content">
                <p>${avis.commentaire}</p>
            </div>
            <div class="avis-date">
                ${new Date(avis.date_creation).toLocaleDateString()}
            </div>
        </div>
    `).join('');

    avisContainer.innerHTML = avisHTML;
}

function generateStars(note) {
    const fullStars = Math.floor(note);
    const halfStar = note % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);

    return `
        ${'<i class="fas fa-star"></i>'.repeat(fullStars)}
        ${halfStar ? '<i class="fas fa-star-half-alt"></i>' : ''}
        ${'<i class="far fa-star"></i>'.repeat(emptyStars)}
    `;
}

function setupAvisForm() {
    const form = document.getElementById('avis-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const noteInput = document.querySelector('input[name="note"]:checked');
        if (!noteInput) {
            showError('Veuillez sélectionner une note');
            return;
        }

        const note = noteInput.value;
        const commentaire = document.getElementById('commentaire').value;
        const produitId = new URLSearchParams(window.location.search).get('id');

        if (!commentaire.trim()) {
            showError('Veuillez écrire un commentaire');
            return;
        }

        fetch('../includes/ajouter_avis.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                produitId,
                note,
                commentaire
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Avis ajouté avec succès');
                form.reset();
                // Réinitialiser les étoiles
                document.querySelectorAll('.rating label i').forEach(star => {
                    star.className = 'far fa-star';
                });
                loadAvis();
            } else {
                showError(data.error);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur lors de l\'ajout de l\'avis');
        });
    });
}

function showError(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger';
    alertDiv.textContent = message;
    document.body.insertBefore(alertDiv, document.body.firstChild);
    setTimeout(() => alertDiv.remove(), 5000);
}

function showSuccess(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.textContent = message;
    document.body.insertBefore(alertDiv, document.body.firstChild);
    setTimeout(() => alertDiv.remove(), 5000);
} 