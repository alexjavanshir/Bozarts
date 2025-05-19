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
    } catch (error) {
        console.error('Erreur:', error);
        window.location.href = '../pages/index.html';
    }
});

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