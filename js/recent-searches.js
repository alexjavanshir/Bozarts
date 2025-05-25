// Gestion des recherches récentes
document.addEventListener('DOMContentLoaded', function() {
    const recentSearchesContainer = document.querySelector('.recent-searches');
    const MAX_RECENT_SEARCHES = 5;

    // Fonction pour sauvegarder une recherche
    function saveSearch(searchTerm) {
        if (!searchTerm) return;
        
        let recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        
        // Supprimer la recherche si elle existe déjà
        recentSearches = recentSearches.filter(search => search !== searchTerm);
        
        // Ajouter la nouvelle recherche au début
        recentSearches.unshift(searchTerm);
        
        // Limiter le nombre de recherches récentes
        if (recentSearches.length > MAX_RECENT_SEARCHES) {
            recentSearches = recentSearches.slice(0, MAX_RECENT_SEARCHES);
        }
        
        localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
        displayRecentSearches();
    }

    // Fonction pour afficher les recherches récentes
    function displayRecentSearches() {
        const recentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
        
        if (recentSearches.length === 0) {
            recentSearchesContainer.innerHTML = '<p>Pas encore de recherche récente</p>';
            return;
        }

        const searchesHTML = recentSearches.map(search => `
            <a href="recherche.html?search=${encodeURIComponent(search)}" class="recent-search-item">
                ${search}
            </a>
        `).join('');

        recentSearchesContainer.innerHTML = searchesHTML;
    }

    // Écouter les soumissions du formulaire de recherche
    const searchForm = document.querySelector('.search-container form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput.value.trim()) {
                saveSearch(searchInput.value.trim());
            }
        });
    }

    // Afficher les recherches récentes au chargement de la page
    displayRecentSearches();
}); 