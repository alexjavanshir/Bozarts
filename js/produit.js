class ProductDisplay {
    constructor() {
        this.productId = this.getProductIdFromUrl();
        this.initializeElements();
        this.loadProduct();
    }

    // Récupère les éléments du DOM
    initializeElements() {
        this.elements = {
            image: document.getElementById('product-img'),
            title: document.getElementById('product-title'),
            reference: document.getElementById('product-reference'),
            description: document.getElementById('product-description'),
            price: document.getElementById('product-price'),
            quantityInput: document.querySelector('.quantity-input'),
            minusBtn: document.querySelector('.quantity-btn.minus'),
            plusBtn: document.querySelector('.quantity-btn.plus'),
            addToCartBtn: document.querySelector('.add-to-cart'),
            contactSellerBtn: document.querySelector('.contact-seller')
        };

        // Initialiser les écouteurs d'événements
        this.initializeEventListeners();
    }

    // Récupère l'ID du produit depuis l'URL
    getProductIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        console.log("ID du produit récupéré de l'URL:", id);
        return id;
    }

    // Charge les données du produit
    async loadProduct() {
        if (!this.productId) {
            this.showError('Aucun produit spécifié');
            return;
        }

        try {
            const response = await fetch(`../includes/produit.php?id=${this.productId}`);
            const product = await response.json();

            if (product.error) {
                throw new Error(product.error);
            }

            this.updateProductDisplay(product);
        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Erreur lors du chargement du produit');
        }
    }

    // Met à jour l'affichage du produit
    updateProductDisplay(product) {
        this.elements.image.src = product.image_url;
        this.elements.image.alt = product.nom;
        this.elements.title.textContent = product.nom;
        this.elements.reference.textContent = `Catégorie: ${product.categorie}`;
        this.elements.description.textContent = product.description;
        this.elements.price.textContent = `${product.prix}€`;
        
        document.title = `${product.nom} - Bozarts`;

        // Ajouter l'ID du vendeur au bouton de contact
        this.elements.contactSellerBtn.onclick = () => {
            window.location.href = `messagerie.html?vendeur_id=${product.artisan_id}`;
        };
    }

    // Initialise les écouteurs d'événements
    initializeEventListeners() {
        // Gestion de la quantité
        this.elements.minusBtn?.addEventListener('click', () => this.updateQuantity(-1));
        this.elements.plusBtn?.addEventListener('click', () => this.updateQuantity(1));
        
        // Gestion du panier
        this.elements.addToCartBtn?.addEventListener('click', () => this.addToCart());
        
        // Gestion de l'erreur de chargement d'image
        this.elements.image.addEventListener('error', () => {
            this.elements.image.src = '../assets/articles/article_sans_image.jpg';
        });
    }

    // Met à jour la quantité
    updateQuantity(change) {
        const currentValue = parseInt(this.elements.quantityInput.value);
        const newValue = currentValue + change;
        
        if (newValue >= 1) {
            this.elements.quantityInput.value = newValue;
        }
    }

    // Ajoute au panier
    addToCart() {
        console.log("Méthode addToCart appelée");
        const quantity = parseInt(this.elements.quantityInput.value);
        const productId = this.productId;
        
        console.log("Données à envoyer:", {
            produit_id: productId,
            quantite: quantity
        });

        // Vérifier que les données sont valides
        if (!productId || isNaN(productId) || productId <= 0) {
            console.error("ID de produit invalide:", productId);
            alert("ID de produit invalide. Veuillez réessayer.");
            return;
        }

        if (!quantity || isNaN(quantity) || quantity <= 0) {
            console.error("Quantité invalide:", quantity);
            alert("Veuillez spécifier une quantité valide.");
            return;
        }

        // Créer un formulaire pour envoyer les données
        const formData = new FormData();
        formData.append('produit_id', productId);
        formData.append('quantite', quantity);

        // Envoyer les données au serveur
        fetch('../includes/ajouter_panier.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log("Réponse reçue:", response);
            if (response.ok) {
                window.location.href = '../pages/panier.html';
            } else {
                return response.text().then(text => {
                    console.error("Erreur réponse:", text);
                    throw new Error('Erreur lors de l\'ajout au panier: ' + text);
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'ajout au panier: ' + error.message);
        });
    }

    // Affiche une erreur
    showError(message) {
        alert(message);
    }
}

// Initialiser l'affichage du produit quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    new ProductDisplay();
}); 