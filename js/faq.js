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