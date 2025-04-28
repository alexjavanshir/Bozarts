// Récupérer le terme de recherche depuis l'URL
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const searchTerm = urlParams.get('search');
  
  // Ajouter un message de débogage
  console.log("Terme de recherche:", searchTerm);

  // Si un terme de recherche est présent, effectuer la recherche
  if (searchTerm) {
    // Afficher un message indiquant que la recherche est en cours
    document.getElementById('search-results').innerHTML = '<p>Recherche en cours...</p>';
    performSearch(searchTerm);
  } else {
    // Afficher un message si aucun terme de recherche n'est présent
    document.getElementById('search-results').innerHTML = '<p><div class="error-message">Veuillez saisir un terme de recherche.</div></p>';
  }
});

function performSearch(term) {
  const url = `../includes/recherche.php?search=${encodeURIComponent(term)}`;
  console.log("URL de recherche:", url);
  
  fetch(url)
    .then(response => {
      console.log("Statut de la réponse:", response.status);
      // Afficher le texte brut de la réponse pour le débogage
      response.clone().text().then(text => {
        console.log("Réponse brute:", text);
      });
      return response.json();
    })
    .then(data => {
      console.log("Données reçues:", data);
      // Vérifier si une erreur a été renvoyée
      if (data && data.error) {
        throw new Error(data.error);
      }
      displaySearchResults(data);
    })
    .catch(error => {
      console.error('Erreur:', error);
      document.getElementById('search-results').innerHTML = 
        `<div class="error-message">Une erreur est survenue lors de la recherche: ${error.message}</div>`;
    });
}

function displaySearchResults(products) {
  const resultsContainer = document.getElementById('search-results');
  resultsContainer.innerHTML = '';

  if (!products || products.length === 0) {
    resultsContainer.innerHTML = '<p><div class="error-message">Aucun résultat trouvé pour votre recherche.</div></p>';
    return;
  }

  const searchCount = document.createElement('div');
  searchCount.className = 'search-count';
  searchCount.textContent = `${products.length} article(s) trouvé(s)`;
  resultsContainer.appendChild(searchCount);

  products.forEach(product => {
    const productCard = document.createElement('div');
    productCard.className = 'product-card';
    
    // Vérifier si les propriétés existent et utiliser des valeurs par défaut si nécessaire
    const imageUrl = product.image_url ? `../assets/articles/${product.image_url}` : '../assets/articles/default-product.jpg';
    const nom = product.nom || 'Produit sans nom';
    const description = product.description || 'Aucune description disponible';
    const prix = product.formatted_price || `${product.prix} €`;
    
    productCard.innerHTML = `
      <img src="${imageUrl}" alt="${nom}" onerror="this.src='../assets/articles/default-product.jpg'">
      <h3>${nom}</h3>
      <p>${description}</p>
      <p class="price">${prix}</p>
      <button onclick="viewProduct(${product.id})">Voir détails</button>
    `;
    resultsContainer.appendChild(productCard);
  });
}

function viewProduct(productId) {
  window.location.href = `produit.html?id=${productId}`;
} 