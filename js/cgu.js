document.addEventListener('DOMContentLoaded', function() {
    // Charger les CGU au démarrage
    loadCGUs();
});

// Charger les CGU depuis la base de données
async function loadCGUs() {
    try {
        const response = await fetch('../includes/get_cgu.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const cgus = await response.json();
        displayCGUs(cgus);
    } catch (error) {
        console.error('Erreur lors du chargement des CGU:', error);
    }
}

// Afficher les CGU dans la page
function displayCGUs(cgus) {
    const cguContent = document.getElementById('cguContent');
    cguContent.innerHTML = '<h1>Conditions Générales d\'Utilisation</h1>';

    cgus.forEach(cgu => {
        const section = document.createElement('section');
        section.innerHTML = `
            <h2>${cgu.titre}</h2>
            <p>${cgu.contenu}</p>
        `;
        cguContent.appendChild(section);
    });
} 