document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'accordéon pour les questions-réponses
    const questionButtons = document.querySelectorAll('.faq-question');
    
    questionButtons.forEach(button => {
        button.addEventListener('click', function () {
            const allAnswers = document.querySelectorAll('.faq-answer');
            const currentAnswer = this.nextElementSibling;
        
            allAnswers.forEach(answer => {
                if (answer !== currentAnswer) {
                    answer.style.maxHeight = null;
                    answer.classList.remove('active');
                }
            });
        
            if (currentAnswer.classList.contains('active')) {
                currentAnswer.style.maxHeight = null;
                currentAnswer.classList.remove('active');
            } else {
                currentAnswer.classList.add('active');
        
                // Petit délai pour que .active soit appliqué AVANT de lire scrollHeight
                setTimeout(() => {
                    currentAnswer.style.maxHeight = currentAnswer.scrollHeight + 'px';
                }, 10);
            }
        });
        
    });
    
    // Gestion de la recherche
    const searchInput = document.querySelector('.search-bar input');
    const clearButton = document.querySelector('.close-search');
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (searchInput && clearButton) {
        searchInput.addEventListener('input', filterFAQ);
        clearButton.addEventListener('click', clearSearch);
    }
    
    function filterFAQ() {
        const searchTerm = searchInput.value.toLowerCase();
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    function clearSearch() {
        searchInput.value = '';
        faqItems.forEach(item => {
            item.style.display = 'block';
        });
    }
}); 

// Fonctions d'administration de la FAQ
async function checkAdmin() {
    try {
        const response = await fetch('../includes/check_session.php');
        const data = await response.json();
        
        if (data.droit === 'admin') {
            document.getElementById('adminEdit').classList.add('active');
            loadFAQForEdit();
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Charger le contenu de la FAQ pour l'édition
async function loadFAQForEdit() {
    try {
        const response = await fetch('faq.html');
        const content = await response.text();
        document.getElementById('faqContent').value = content;
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Sauvegarder les modifications
async function saveFAQ() {
    const content = document.getElementById('faqContent').value;
    
    try {
        const response = await fetch('../includes/update_faq.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ content })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('FAQ mise à jour avec succès !');
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour de la FAQ');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour de la FAQ');
    }
}

// Vérifier le statut admin au chargement de la page
document.addEventListener('DOMContentLoaded', checkAdmin); 