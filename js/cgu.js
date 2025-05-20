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
    const cguContent = document.getElementById('cguContent').innerHTML;
    document.getElementById('cguTextarea').value = cguContent;
  } catch (error) {
    console.error('Erreur:', error);
  }
}

// Sauvegarder les modifications
async function saveCGU() {
  const content = document.getElementById('cguTextarea').value;
  
  try {
    const response = await fetch('../includes/update_cgu.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ content })
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