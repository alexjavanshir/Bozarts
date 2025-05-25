document.addEventListener('DOMContentLoaded', function() {
    loadTransactions();
});

function loadTransactions() {
    fetch('../includes/get_transactions.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher les commandes envoyées
                displayTransactions(data.commandesEnvoyees, false, 'commandesEnvoyees');
                
                // Gérer l'affichage des commandes reçues
                const sectionCommandesRecues = document.getElementById('sectionCommandesRecues');
                if (data.isArtisan) {
                    sectionCommandesRecues.style.display = 'block';
                    displayTransactions(data.commandesRecues, true, 'commandesRecues');
                } else {
                    sectionCommandesRecues.style.display = 'none';
                }
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Une erreur est survenue lors du chargement des transactions');
        });
}

function displayTransactions(commandes, isArtisan, targetElementId) {
    const transactionsList = document.getElementById(targetElementId);
    
    if (commandes.length === 0) {
        transactionsList.innerHTML = '<p class="no-transactions">Aucune transaction à afficher.</p>';
        return;
    }

    transactionsList.innerHTML = commandes.map(commande => `
        <div class="transaction-card">
            <div class="transaction-header">
                <h3>Commande #${commande.id}</h3>
                <span class="transaction-date">${commande.date}</span>
            </div>
            ${isArtisan ? `
                <div class="client-info">
                    <strong>Client :</strong> ${commande.client.prenom} ${commande.client.nom}
                </div>
            ` : ''}
            <div class="transaction-status ${commande.statut.toLowerCase()}">
                ${commande.statut}
            </div>
            <div class="transaction-details">
                <div class="products-list">
                    ${commande.produits.map(produit => `
                        <div class="product-item">
                            <span class="product-name">${produit.nom}</span>
                            <span class="product-quantity">x${produit.quantite}</span>
                            <span class="product-price">${produit.prix}€</span>
                        </div>
                    `).join('')}
                </div>
                <div class="transaction-summary">
                    <div class="delivery-address">
                        <strong>Adresse de livraison :</strong>
                        <p>${commande.adresse_livraison}</p>
                    </div>
                    <div class="total-amount">
                        <strong>Total :</strong>
                        <span>${commande.montant_total}€</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function showError(message) {
    const errorMessage = `
        <div class="error-message">
            <p>${message}</p>
        </div>
    `;
    document.getElementById('commandesEnvoyees').innerHTML = errorMessage;
    document.getElementById('commandesRecues').innerHTML = errorMessage;
} 