document.addEventListener('DOMContentLoaded', function() {
    // Charger les FAQ au démarrage
    loadFAQs();
    
    // Gestion de la recherche
    const searchInput = document.querySelector('.search-bar input');
    const clearButton = document.querySelector('.close-search');
    
    if (searchInput && clearButton) {
        searchInput.addEventListener('input', filterFAQ);
        clearButton.addEventListener('click', clearSearch);
    }
});

// Charger les FAQ depuis la base de données
async function loadFAQs() {
    try {
        const response = await fetch('../includes/get_faq.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const faqs = await response.json();
        displayFAQs(faqs);
    } catch (error) {
        console.error('Erreur lors du chargement des FAQ:', error);
    }
}

// Afficher les FAQ dans la page
function displayFAQs(faqs) {
    const faqContent = document.getElementById('faq-content');
    faqContent.innerHTML = '';

    faqs.forEach(faq => {
        const faqItem = document.createElement('div');
        faqItem.className = 'faq-item';
        faqItem.innerHTML = `
            <button class="faq-question">
                ${faq.question}
            </button>
            <div class="faq-answer">
                <p class="answer-title"><br>${faq.titre_reponse}</p>
                <p class="answer-content">
                    ${faq.reponse}
                    <br><br>
                </p>
            </div>
        `;
        faqContent.appendChild(faqItem);
    });

    // Réinitialiser les événements d'accordéon
    setupAccordion();
}

// Configurer l'accordéon pour les questions-réponses
function setupAccordion() {
    const questionButtons = document.querySelectorAll('.faq-question');
    
    questionButtons.forEach(button => {
        button.addEventListener('click', function() {
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
        
                setTimeout(() => {
                    currentAnswer.style.maxHeight = currentAnswer.scrollHeight + 'px';
                }, 10);
            }
        });
    });
}

// Filtrer les FAQ selon la recherche
function filterFAQ() {
    const searchInput = document.querySelector('.search-bar input');
    const searchTerm = searchInput.value.toLowerCase();
    const faqItems = document.querySelectorAll('.faq-item');
    
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

// Effacer la recherche
function clearSearch() {
    const searchInput = document.querySelector('.search-bar input');
    searchInput.value = '';
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        item.style.display = 'block';
    });
}

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