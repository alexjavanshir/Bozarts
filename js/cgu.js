// Vérifier si l'utilisateur est admin
async function checkAdmin() {
  try {
    const response = await fetch('../includes/check_session.php');
    const data = await response.json();
    
    if (data.droit === 'admin') {
      document.getElementById('adminEdit').classList.add('active');
      loadCGUForEdit();
    }
  } catch (error) {
    console.error('Erreur:', error);
  }
}

// Charger le contenu des CGU pour l'édition
async function loadCGUForEdit() {
  try {
    const sections = document.querySelectorAll('#cguContent section');
    const editContainer = document.getElementById('adminEdit');
    const textarea = document.getElementById('cguTextarea');
    textarea.style.display = 'none'; // Cacher le textarea original

    // Nettoyer le conteneur d'édition
    while (editContainer.firstChild) {
      editContainer.removeChild(editContainer.firstChild);
    }

    // Ajouter le titre
    const title = document.createElement('h2');
    title.textContent = 'Édition des CGU';
    editContainer.appendChild(title);

    // Créer l'interface d'édition
    sections.forEach((section, index) => {
      const editSection = document.createElement('div');
      editSection.className = 'edit-section';
      
      const title = section.querySelector('h2').textContent;
      const content = section.innerHTML.replace(/<h2>.*?<\/h2>/, '').trim();
      
      editSection.innerHTML = `
        <h3>${title}</h3>
        <div class="edit-controls">
          <textarea class="section-content" data-section="${index}">${content}</textarea>
        </div>
      `;
      
      editContainer.appendChild(editSection);
    });

    // Ajouter le bouton de sauvegarde
    const saveButton = document.createElement('button');
    saveButton.textContent = 'Enregistrer les modifications';
    saveButton.onclick = saveCGU;
    editContainer.appendChild(saveButton);
  } catch (error) {
    console.error('Erreur:', error);
  }
}

// Sauvegarder les modifications
async function saveCGU() {
  try {
    // Récupérer le template HTML de base
    const template = `<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CGU - Bozarts</title>
    <link rel="stylesheet" href="../assets/css/CGU.css" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <base href="../pages/index.html" />
  </head>
  <body>
    <header>
      <div class="logo">
        <a href="../pages/index.html" class="icon-button">
          <img src="../assets/icons/LOGO.png" alt="BOZARTS" />
        </a>
      </div>
      <div class="search-container">
        <form action="recherche.html" method="get">
          <input type="text" name="search" class="search-box" placeholder="Rechercher" />
          <button type="submit" class="search-button">
            <img src="../assets/icons/search-icon.png" class="search-icon" alt="search icon" />
          </button>
        </form>
      </div>
      <div class="header-right">
        <a href="panier.html" class="icon-button" style="width: 20%">
          <img src="../assets/icons/panier-icon.png" alt="Panier" class="icon-img" />
        </a>
        <a href="messagerie.html" class="icon-button" id="notifications-link">
          <img src="../assets/icons/notifications-icon.png" alt="Notifications" class="icon-img" />
        </a>
        <a href="connexion.html" class="icon-button" id="profile-link">
          <img src="../assets/icons/profil-icon.png" alt="Profil" class="icon-img" />
        </a>
      </div>
    </header>

    <main class="container">
      <div class="admin-edit" id="adminEdit">
        <h2>Édition des CGU</h2>
        <textarea id="cguTextarea"></textarea>
      </div>

      <div class="legal" id="cguContent">
        <h1>Conditions Générales d'Utilisation</h1>
        
        {{CONTENT}}
      </div>
    </main>

    <footer>
      <div class="footer-content">
        <div class="footer-left">
          <p>© 2025 Bozarts. Tous droits réservés.</p>
          <p><a href="../pages/CGU.html" class="footer-link">CGU & Mentions légales</a></p>
          <p>Un problème non résolu par la FAQ ? <a href="../pages/contacter.html" class="footer-link">Contactez-nous</a></p>
        </div>
    
        <div class="footer-center">
          <ul class="footer-links">
            <li><a href="faq.html" class="footer-link">Notre FAQ</a></li>
          </ul>
        </div>
    
        <div class="footer-right">
          <p>Suivez-nous</p>
          <div class="social-icons">
            <a href="#"><img src="../assets/icons/facebook.png" alt="Facebook" /></a>
            <a href="#"><img src="../assets/icons/twitter.png" alt="Twitter" /></a>
            <a href="#"><img src="../assets/icons/instagram.png" alt="Instagram" /></a>
            <a href="#"><img src="../assets/icons/linkedin.png" alt="LinkedIn" /></a>
          </div>
        </div>
      </div>
    </footer>

    <script src="../js/header.js"></script>
    <script src="../js/cgu.js"></script>
  </body>
</html>`;

    // Construire le contenu des sections
    let sectionsContent = '';
    const editedSections = document.querySelectorAll('.edit-section');
    
    editedSections.forEach((editSection) => {
      const sectionTitle = editSection.querySelector('h3').textContent;
      const content = editSection.querySelector('textarea').value;
      sectionsContent += `
        <section>
          <h2>${sectionTitle}</h2>
          ${content}
        </section>
      `;
    });

    // Remplacer le placeholder par le contenu
    const updatedContent = template.replace('{{CONTENT}}', sectionsContent);

    const response = await fetch('../includes/update_cgu.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ content: updatedContent })
    });

    const data = await response.json();
    
    if (data.success) {
      alert('CGU mises à jour avec succès !');
      location.reload();
    } else {
      alert('Erreur lors de la mise à jour des CGU');
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Erreur lors de la mise à jour des CGU');
  }
}

// Vérifier le statut admin au chargement de la page
document.addEventListener('DOMContentLoaded', checkAdmin); 