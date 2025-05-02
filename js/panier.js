class Panier {
    constructor() {
        this.elements = {
            cartItems: document.querySelector('.cart-items'),
            cartSummary: document.querySelector('.cart-summary'),
            checkoutButton: document.querySelector('.checkout-button')
        };

        this.init();
    }

    init() {
        this.loadPanier();
        this.setupEventListeners();
    }

    loadPanier() {
        console.log('Chargement du panier...');
        // Récupérer directement les données du panier sans vérification supplémentaire
        fetch('../includes/afficher_panier.php')
            .then(response => {
                console.log('Réponse status:', response.status);
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.text().then(text => {
                    console.log('Réponse brute:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Erreur de parsing JSON:', e);
                        throw new Error('La réponse n\'est pas un JSON valide');
                    }
                });
            })
            .then(data => {
                console.log('Données reçues:', data);
                if (data.error === 'not_logged_in') {
                    this.elements.cartItems.innerHTML = '<p>Veuillez vous connecter pour voir votre panier.</p>';
                    return;
                }
                
                this.displayPanier(data.panier);
                this.updateSummary(data.total);
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.elements.cartItems.innerHTML = `<p>Erreur lors du chargement du panier: ${error.message}</p>`;
            });
    }

    displayPanier(panier) {
        this.elements.cartItems.innerHTML = '';
        
        if (!panier || panier.length === 0) {
            this.elements.cartItems.innerHTML = '<p>Votre panier est vide</p>';
            return;
        }

        panier.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'cart-item';
            
            // Utiliser un chemin d'image de secours si image_url est vide
            const imagePath = item.image_url ? 
                `../assets/articles/${item.image_url}` : 
                `../assets/articles/article${item.id}.jpg`;
                
            itemElement.innerHTML = `
                <div class="item-image">
                    <img src="${imagePath}" alt="${item.nom}" onerror="this.src='../assets/articles/article_sans_image.jpg'"/>
                </div>
                <div class="item-details">
                    <h3>${item.nom}</h3>
                    <p>Quantité : ${item.quantite}</p>
                    <p>Prix : ${item.prix}€</p>
                    <button class="remove-item" data-id="${item.id}">Supprimer</button>
                </div>
            `;
            this.elements.cartItems.appendChild(itemElement);
        });
    }

    updateSummary(total) {
        const fraisLivraison = total > 0 ? 10 : 0;
        const totalAvecLivraison = total + fraisLivraison;

        this.elements.cartSummary.innerHTML = `
            <h2>Total</h2>
            <div class="summary-details">
                <div class="summary-line">
                    <span>Sous-Total :</span>
                    <span>${total}€</span>
                </div>
                <div class="summary-line">
                    <span>Livraison :</span>
                    <span>${fraisLivraison}€</span>
                </div>
                <div class="summary-line total">
                    <span>Total :</span>
                    <span>${totalAvecLivraison}€</span>
                </div>
                ${total > 0 ? '<button class="checkout-button">Paiement</button>' : ''}
            </div>
        `;
        
        // Mettre à jour le bouton de paiement après avoir recréé le HTML
        if (total > 0) {
            this.elements.checkoutButton = document.querySelector('.checkout-button');
            this.elements.checkoutButton.addEventListener('click', () => {
                alert('Fonctionnalité de paiement à venir');
            });
        }
    }

    setupEventListeners() {
        this.elements.cartItems.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                const productId = e.target.dataset.id;
                this.removeItem(productId);
            }
        });
    }

    removeItem(productId) {
        console.log("Suppression du produit ID:", productId);
        const formData = new FormData();
        formData.append('produit_id', productId);

        fetch('../includes/supprimer_panier.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log("Réponse:", response);
            if (response.ok) {
                this.loadPanier();
            } else {
                throw new Error('Erreur lors de la suppression du produit');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la suppression du produit');
        });
    }
}

// Initialiser le panier quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new Panier();
}); 