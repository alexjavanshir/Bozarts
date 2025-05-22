document.addEventListener('DOMContentLoaded', async () => {
    // Vérifier si l'utilisateur est admin
    try {
        const response = await fetch('../includes/check_session.php');
        const data = await response.json();
        console.log('Session data:', data); // Debug log
        
        if (!data.id || data.droit !== 'admin') {
            console.log('Access denied:', data); // Debug log
            window.location.href = '../pages/index.html';
            return;
        }

        // Initialiser la page admin
        loadUsers();
        setupSearch();
        setupTabs();
        loadFAQ();
        setupFAQForm();
        loadCGU();
        setupCGUForm();
    } catch (error) {
        console.error('Erreur:', error);
        window.location.href = '../pages/index.html';
    }
});

// Gestion des onglets
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Désactiver tous les onglets
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Activer l'onglet sélectionné
            button.classList.add('active');
            const tabId = button.getAttribute('data-tab');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
}

// Charger les FAQ
async function loadFAQ() {
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

// Afficher les FAQ
function displayFAQs(faqs) {
    const faqList = document.getElementById('faq-list');
    faqList.innerHTML = '';

    faqs.forEach(faq => {
        const faqCard = document.createElement('div');
        faqCard.className = 'faq-card';
        faqCard.innerHTML = `
            <div class="faq-info">
                <div class="faq-question">${faq.question}</div>
                <div class="faq-titre">${faq.titre_reponse}</div>
                <div class="faq-reponse">${faq.reponse}</div>
            </div>
            <div class="faq-actions">
                <button class="btn-admin btn-edit" onclick="editFAQ(${faq.id}, '${faq.question.replace(/'/g, "\\'")}', '${faq.titre_reponse.replace(/'/g, "\\'")}', '${faq.reponse.replace(/'/g, "\\'")}')">
                    Modifier
                </button>
                <button class="btn-admin btn-delete" onclick="deleteFAQ(${faq.id})">
                    Supprimer
                </button>
            </div>
        `;
        faqList.appendChild(faqCard);
    });
}

// Éditer une FAQ
function editFAQ(id, question, titre, reponse) {
    // Afficher le formulaire
    const faqFormContainer = document.getElementById('faq-form-container');
    faqFormContainer.style.display = 'flex';
    
    // Changer le titre du formulaire
    document.getElementById('form-title').textContent = 'Modifier la question';
    
    // Remplir le formulaire avec les données existantes
    document.getElementById('faq-id').value = id;
    document.getElementById('faq-question').value = question;
    document.getElementById('faq-titre').value = titre;
    document.getElementById('faq-reponse').value = reponse;
}

// Configuration du formulaire FAQ
function setupFAQForm() {
    const addFaqBtn = document.getElementById('add-faq-btn');
    const faqFormContainer = document.getElementById('faq-form-container');
    const faqForm = document.getElementById('faq-form');
    const cancelFaqBtn = document.getElementById('cancel-faq-btn');
    
    // Afficher le formulaire pour ajouter une nouvelle FAQ
    addFaqBtn.addEventListener('click', () => {
        document.getElementById('form-title').textContent = 'Ajouter une question';
        document.getElementById('faq-id').value = '';
        document.getElementById('faq-question').value = '';
        document.getElementById('faq-titre').value = '';
        document.getElementById('faq-reponse').value = '';
        faqFormContainer.style.display = 'flex';
    });
    
    // Masquer le formulaire
    cancelFaqBtn.addEventListener('click', () => {
        faqFormContainer.style.display = 'none';
    });
    
    // Soumettre le formulaire
    faqForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const faqId = document.getElementById('faq-id').value;
        const question = document.getElementById('faq-question').value;
        const titre = document.getElementById('faq-titre').value;
        const reponse = document.getElementById('faq-reponse').value;
        
        try {
            const url = faqId ? '../includes/update_faq.php' : '../includes/add_faq.php';
            const data = faqId ? 
                { id: faqId, question, titre_reponse: titre, reponse } : 
                { question, titre_reponse: titre, reponse };
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Debug: Log the raw response text
            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            // Try to parse the JSON
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Response text:', responseText);
                throw new Error('Invalid JSON response from server');
            }
            
            if (result.success) {
                alert(result.message);
                faqFormContainer.style.display = 'none';
                loadFAQ(); // Recharger la liste des FAQ
            } else {
                alert(result.error || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'enregistrement');
        }
    });
}

// Supprimer une FAQ
async function deleteFAQ(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette question ?')) {
        return;
    }

    try {
        const response = await fetch('../includes/delete_faq.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            loadFAQ(); // Recharger la liste des FAQ
        } else {
            alert(result.error || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la suppression');
    }
}

// Recherche dans les FAQ
function setupFAQSearch() {
    const searchInput = document.getElementById('search-faq');
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const faqCards = document.querySelectorAll('.faq-card');
        
        faqCards.forEach(card => {
            const question = card.querySelector('.faq-question').textContent.toLowerCase();
            const titre = card.querySelector('.faq-titre').textContent.toLowerCase();
            const reponse = card.querySelector('.faq-reponse').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || titre.includes(searchTerm) || reponse.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Appeler setupFAQSearch après le chargement des FAQ
async function loadUsers() {
    try {
        const response = await fetch('../includes/get_users.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const users = await response.json();
        console.log('Users loaded:', users); // Debug log
        displayUsers(users);
    } catch (error) {
        console.error('Erreur lors du chargement des utilisateurs:', error);
    }
}

function displayUsers(users) {
    const usersList = document.getElementById('users-list');
    usersList.innerHTML = '';

    users.forEach(user => {
        const userCard = document.createElement('div');
        userCard.className = 'user-card';
        userCard.innerHTML = `
            <div class="user-info">
                <div class="user-email">${user.email}</div>
                <div class="user-role">${user.is_admin ? 'Administrateur' : 'Utilisateur'}</div>
            </div>
            <div class="user-actions">
                <button class="btn-admin btn-toggle-admin ${user.is_admin ? 'is-admin' : ''}" 
                        onclick="toggleAdmin(${user.id}, ${!user.is_admin})">
                    ${user.is_admin ? 'Retirer admin' : 'Donner admin'}
                </button>
                <button class="btn-admin btn-delete" onclick="deleteUser(${user.id})">
                    Supprimer
                </button>
            </div>
        `;
        usersList.appendChild(userCard);
    });
}

async function toggleAdmin(userId, makeAdmin) {
    if (!confirm(`Êtes-vous sûr de vouloir ${makeAdmin ? 'donner' : 'retirer'} les droits d'administrateur à cet utilisateur ?`)) {
        return;
    }

    try {
        const response = await fetch('../includes/toggle_admin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId, makeAdmin })
        });

        if (response.ok) {
            loadUsers(); // Recharger la liste des utilisateurs
        } else {
            alert('Erreur lors de la modification des droits administrateur');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification des droits administrateur');
    }
}

async function deleteUser(userId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        return;
    }

    try {
        const response = await fetch('../includes/delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ userId })
        });

        if (response.ok) {
            loadUsers(); // Recharger la liste des utilisateurs
        } else {
            alert('Erreur lors de la suppression de l\'utilisateur');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression de l\'utilisateur');
    }
}

function setupSearch() {
    const searchInput = document.getElementById('search-users');
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const userCards = document.querySelectorAll('.user-card');
        
        userCards.forEach(card => {
            const email = card.querySelector('.user-email').textContent.toLowerCase();
            card.style.display = email.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Charger les CGU
async function loadCGU() {
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

// Afficher les CGU
function displayCGUs(cgus) {
    const cguList = document.getElementById('cgu-list');
    cguList.innerHTML = '';

    cgus.forEach(cgu => {
        const cguCard = document.createElement('div');
        cguCard.className = 'cgu-card';
        cguCard.innerHTML = `
            <div class="cgu-info">
                <div class="cgu-titre">${cgu.titre}</div>
                <div class="cgu-contenu">${cgu.contenu}</div>
            </div>
            <div class="cgu-actions">
                <button class="btn-admin btn-edit" onclick="editCGU(${cgu.id}, '${cgu.titre.replace(/'/g, "\\'")}', '${cgu.contenu.replace(/'/g, "\\'")}')">
                    Modifier
                </button>
                <button class="btn-admin btn-delete" onclick="deleteCGU(${cgu.id})">
                    Supprimer
                </button>
            </div>
        `;
        cguList.appendChild(cguCard);
    });
}

// Éditer une CGU
function editCGU(id, titre, contenu) {
    // Afficher le formulaire
    const cguFormContainer = document.getElementById('cgu-form-container');
    cguFormContainer.style.display = 'flex';
    
    // Changer le titre du formulaire
    document.getElementById('cgu-form-title').textContent = 'Modifier la section';
    
    // Remplir le formulaire avec les données existantes
    document.getElementById('cgu-id').value = id;
    document.getElementById('cgu-titre').value = titre;
    document.getElementById('cgu-contenu').value = contenu;
}

// Configuration du formulaire CGU
function setupCGUForm() {
    const addCguBtn = document.getElementById('add-cgu-btn');
    const cguFormContainer = document.getElementById('cgu-form-container');
    const cguForm = document.getElementById('cgu-form');
    const cancelCguBtn = document.getElementById('cancel-cgu-btn');
    
    // Afficher le formulaire pour ajouter une nouvelle section
    addCguBtn.addEventListener('click', () => {
        document.getElementById('cgu-form-title').textContent = 'Ajouter une section';
        document.getElementById('cgu-id').value = '';
        document.getElementById('cgu-titre').value = '';
        document.getElementById('cgu-contenu').value = '';
        cguFormContainer.style.display = 'flex';
    });
    
    // Masquer le formulaire
    cancelCguBtn.addEventListener('click', () => {
        cguFormContainer.style.display = 'none';
    });
    
    // Soumettre le formulaire
    cguForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const cguId = document.getElementById('cgu-id').value;
        const titre = document.getElementById('cgu-titre').value;
        const contenu = document.getElementById('cgu-contenu').value;
        
        try {
            const url = cguId ? '../includes/update_cgu.php' : '../includes/add_cgu.php';
            const data = cguId ? 
                { id: cguId, titre, contenu } : 
                { titre, contenu };
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                cguFormContainer.style.display = 'none';
                loadCGU(); // Recharger la liste des CGU
            } else {
                alert(result.error || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'enregistrement');
        }
    });
}

// Supprimer une CGU
async function deleteCGU(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette section ?')) {
        return;
    }

    try {
        const response = await fetch('../includes/delete_cgu.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            loadCGU(); // Recharger la liste des CGU
        } else {
            alert(result.error || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la suppression');
    }
}

// Recherche dans les CGU
function setupCGUSearch() {
    const searchInput = document.getElementById('search-cgu');
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const cguCards = document.querySelectorAll('.cgu-card');
        
        cguCards.forEach(card => {
            const titre = card.querySelector('.cgu-titre').textContent.toLowerCase();
            const contenu = card.querySelector('.cgu-contenu').textContent.toLowerCase();
            
            if (titre.includes(searchTerm) || contenu.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Appeler setupCGUSearch après le chargement des CGU 