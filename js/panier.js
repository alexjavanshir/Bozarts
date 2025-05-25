class Panier {
    constructor() {
        this.elements = {
            cartItems: document.querySelector('.cart-items'),
            cartSummary: document.querySelector('.cart-summary'),
            checkoutButton: document.querySelector('.checkout-button'),
            paymentModal: document.getElementById('paymentModal'),
            closeModal: document.querySelector('.close-modal'),
            paymentForm: document.getElementById('paymentForm')
        };

        this.init();
    }

    init() {
        this.loadPanier();
        this.setupEventListeners();
        this.setupPaymentForm();
    }

    loadPanier() {
        console.log('Chargement du panier...');
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
            
            const imagePath = item.image_url ? 
                item.image_url : 
                '../assets/articles/article_sans_image.jpg';
                
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
        const totalAvecLivraison = Math.round((total + fraisLivraison) * 100) / 100;

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
            this.setupEventListeners(); // Réinitialiser les événements
        }
    }

    setupEventListeners() {
        // Gestion des éléments du panier
        this.elements.cartItems.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item')) {
                const productId = e.target.dataset.id;
                this.removeItem(productId);
            }
        });

        // Gestion du bouton de paiement
        if (this.elements.checkoutButton) {
            this.elements.checkoutButton.addEventListener('click', () => {
                this.openPaymentModal();
            });
        }

        // Fermeture de la modal
        if (this.elements.closeModal) {
            this.elements.closeModal.addEventListener('click', () => {
                this.closePaymentModal();
            });
        }

        // Fermeture de la modal en cliquant en dehors
        window.addEventListener('click', (e) => {
            if (e.target === this.elements.paymentModal) {
                this.closePaymentModal();
            }
        });
    }

    setupPaymentForm() {
        // Formatage automatique du numéro de carte
        const cardNumber = document.getElementById('cardNumber');
        if (cardNumber) {
            cardNumber.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{4})/g, '$1 ').trim();
                e.target.value = value;
            });
        }

        // Formatage de la date d'expiration
        const expiryDate = document.getElementById('expiryDate');
        if (expiryDate) {
            expiryDate.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2);
                }
                e.target.value = value;
            });
        }

        // Validation du formulaire
        if (this.elements.paymentForm) {
            this.elements.paymentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (this.validatePaymentForm()) {
                    this.processPayment();
                }
            });
        }
    }

    validatePaymentForm() {
        const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const expiryDate = document.getElementById('expiryDate').value;
        const cvv = document.getElementById('cvv').value;
        const cardName = document.getElementById('cardName').value;
        const address = document.getElementById('address').value;
        const postalCode = document.getElementById('postalCode').value;
        const city = document.getElementById('city').value;

        // Validation basique
        if (cardNumber.length !== 16) {
            alert('Le numéro de carte doit contenir 16 chiffres');
            return false;
        }

        if (!expiryDate.match(/^\d{2}\/\d{2}$/)) {
            alert('Format de date d\'expiration invalide (MM/AA)');
            return false;
        }

        if (cvv.length !== 3) {
            alert('Le code CVV doit contenir 3 chiffres');
            return false;
        }

        if (!cardName || !address || !postalCode || !city) {
            alert('Veuillez remplir tous les champs');
            return false;
        }

        return true;
    }

    processPayment() {
        const formData = new FormData();
        formData.append('address', document.getElementById('address').value);
        formData.append('postalCode', document.getElementById('postalCode').value);
        formData.append('city', document.getElementById('city').value);

        console.log('Envoi des données de paiement...');
        
        fetch('../includes/creer_commande.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Statut de la réponse:', response.status);
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
            if (data.success) {
                alert(data.message);
                this.closePaymentModal();
                window.location.href = 'mes-transactions.html';
            } else {
                alert(data.message || 'Une erreur est survenue lors du paiement');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du traitement du paiement: ' + error.message);
        });
    }

    openPaymentModal() {
        this.elements.paymentModal.classList.add('show');
    }

    closePaymentModal() {
        this.elements.paymentModal.classList.remove('show');
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