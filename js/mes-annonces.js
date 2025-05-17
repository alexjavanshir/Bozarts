// Fonction pour charger les produits
function chargerProduits() {
    fetch('../includes/mes-annonces.php')
        .then(response => {
            console.log('Réponse du serveur:', response);
            return response.text();
        })
        .then(html => {
            console.log('HTML reçu:', html);
            document.getElementById('products-container').innerHTML = html;
            // Ajouter les écouteurs d'événements pour les boutons de suppression
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', handleDelete);
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des produits:', error);
            document.getElementById('products-container').innerHTML = '<p class="error">Erreur lors du chargement des produits</p>';
        });
}

// Fonction pour gérer la suppression d'un produit
function handleDelete(event) {
    event.preventDefault();
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
        return;
    }
    
    const productId = this.getAttribute('data-id');
    const formData = new FormData();
    formData.append('id', productId);
    
    fetch('../includes/supprimer-produit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la liste des produits
            chargerProduits();
        } else {
            alert(data.error || 'Erreur lors de la suppression du produit');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression du produit');
    });
}

// Charger les produits au chargement de la page
document.addEventListener('DOMContentLoaded', chargerProduits); 