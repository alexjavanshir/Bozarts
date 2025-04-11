// Gestion de la recherche
function performSearch() {
    const searchInput = document.getElementById('search-input');
    const specialiteSelect = document.getElementById('specialite-select');
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');

    const params = new URLSearchParams({
        search: searchInput.value,
        specialite: specialiteSelect.value,
        min_price: minPriceInput.value,
        max_price: maxPriceInput.value
    });

    fetch(`includes/search.php?${params}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => console.error('Error:', error));
}

// Affichage des résultats de recherche
function displaySearchResults(products) {
    const resultsContainer = document.getElementById('search-results');
    resultsContainer.innerHTML = '';

    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <img src="${product.image_url}" alt="${product.nom}">
            <h3>${product.nom}</h3>
            <p>${product.description}</p>
            <p class="price">${product.formatted_price}</p>
            <p class="artisan">Artisan: ${product.artisan_name}</p>
            <button onclick="viewProduct(${product.id})">Voir détails</button>
        `;
        resultsContainer.appendChild(productCard);
    });
}

// Gestion de la galerie
function loadGallery() {
    fetch('includes/gallery.php')
        .then(response => response.json())
        .then(data => {
            displayGallery(data);
        })
        .catch(error => console.error('Error:', error));
}

// Affichage de la galerie
function displayGallery(products) {
    const galleryContainer = document.getElementById('gallery');
    galleryContainer.innerHTML = '';

    products.forEach(product => {
        const galleryItem = document.createElement('div');
        galleryItem.className = 'gallery-item';
        galleryItem.innerHTML = `
            <img src="${product.image_url}" alt="${product.nom}">
            <div class="gallery-item-info">
                <h3>${product.nom}</h3>
                <p>${product.artisan_name}</p>
                <p>${product.formatted_price}</p>
            </div>
        `;
        galleryItem.addEventListener('click', () => viewProduct(product.id));
        galleryContainer.appendChild(galleryItem);
    });
}

// Gestion du panier
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function addToCart(productId, quantity = 1) {
    const existingItem = cart.find(item => item.productId === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({ productId, quantity });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
}

// Gestion des événements
function loadEvents() {
    fetch('includes/events.php')
        .then(response => response.json())
        .then(data => {
            displayEvents(data);
        })
        .catch(error => console.error('Error:', error));
}

function displayEvents(events) {
    const eventsContainer = document.getElementById('events');
    eventsContainer.innerHTML = '';

    events.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'event-card';
        eventCard.innerHTML = `
            <h3>${event.titre}</h3>
            <p>${event.description}</p>
            <p>Date: ${formatDate(event.date_debut)} - ${formatDate(event.date_fin)}</p>
            <p>Lieu: ${event.lieu}</p>
            <p>Type: ${event.type}</p>
        `;
        eventsContainer.appendChild(eventCard);
    });
}

// Fonctions utilitaires
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    loadGallery();
    loadEvents();
});