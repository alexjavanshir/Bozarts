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
            addToCartBtn: document.querySelector('.add-to-cart')
        };

        // Initialiser les écouteurs d'événements
        this.initializeEventListeners();
    }

    // Récupère l'ID du produit depuis l'URL
    getProductIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    // Charge les données du produit
    async loadProduct() {
        if (!this.productId) {
            this.showError('Aucun produit spécifié');
            return;
        }

        try {
            const response = await fetch(`../includes/get_product.php?id=${this.productId}`);
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
        //this.elements.image.src = `../assets/articles/${product.image_url}`;
        this.elements.image.src = `../assets/articles/article${this.productId}.jpg`;
        this.elements.image.alt = product.nom;
        this.elements.title.textContent = product.nom;
        this.elements.reference.textContent = `Référence: ${product.reference}`;
        this.elements.description.textContent = product.description;
        this.elements.price.textContent = `${product.prix}€`;
        
        document.title = `${product.nom} - Bozarts`;
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

    // Ajoute au panier (à implémenter plus tard)
    addToCart() {
        const quantity = parseInt(this.elements.quantityInput.value);
        // TODO: Implémenter l'ajout au panier
        alert(`Produit ajouté au panier (${quantity} unité(s))`);
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