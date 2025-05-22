document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('../includes/check_session.php');
        const data = await response.json();
        
        if (!data.id) {
            // L'utilisateur n'est pas connecté
            const messagesContainer = document.getElementById('messages-container');
            messagesContainer.innerHTML = `
                <div class="not-connected-message">
                    <h2>Veuillez vous connecter</h2>
                    <p>Pour accéder à la messagerie, vous devez être connecté.</p>
                    <a href="../pages/connexion.html" class="send-button">Se connecter</a>
                </div>
            `;
            // Désactiver le formulaire et la liste des conversations
            document.getElementById('message-form').style.display = 'none';
            document.getElementById('conversations-list').style.display = 'none';
            return;
        }

        // Si l'utilisateur est connecté, initialiser la messagerie
        const conversationsList = document.getElementById('conversations-list');
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const searchInput = document.getElementById('search-conversations');
        let currentConversationId = null;
        let isAdminConversation = false;

        // Récupérer les paramètres de l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const vendeurId = urlParams.get('vendeur_id');
        const destinataire = urlParams.get('destinataire');

        // Initialiser la conversation avec l'admin si nécessaire
        async function initializeAdminConversation() {
            try {
                console.log('Initialisation de la conversation avec l\'admin...');
                const adminResponse = await fetch('../includes/get_admin_id.php');
                const adminData = await adminResponse.json();
                
                if (adminData.error) {
                    console.error('Erreur lors de la récupération de l\'admin:', adminData.error);
                    return false;
                }
                
                console.log('Données admin reçues:', adminData);
                
                if (adminData.admin_id) {
                    // Mettre à jour l'URL sans recharger la page
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('admin_id', adminData.admin_id);
                    newUrl.searchParams.delete('destinataire');
                    newUrl.searchParams.delete('vendeur_id');
                    window.history.replaceState({}, '', newUrl);
                    
                    console.log('Chargement de la conversation avec l\'admin ID:', adminData.admin_id);
                    
                    // Mettre à jour le titre de la conversation
                    const conversationTitle = document.getElementById('current-conversation-title');
                    if (conversationTitle) {
                        conversationTitle.textContent = `Support Bozarts`;
                    }
                    
                    isAdminConversation = true;
                    // Charger la conversation avec l'admin
                    await loadMessages(adminData.admin_id);
                    return true;
                }
            } catch (error) {
                console.error('Erreur lors de la récupération de l\'admin:', error);
            }
            return false;
        }

        // Charger les conversations
        async function loadConversations() {
            try {
                const response = await fetch('../includes/messagerie.php');
                const data = await response.json();
                
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                displayConversations(data.conversations);
                
                // Vérifier les paramètres d'URL
                const adminId = urlParams.get('admin_id');
                if (adminId) {
                    isAdminConversation = true;
                    loadMessages(adminId);
                } else if (vendeurId) {
                    isAdminConversation = false;
                    loadMessages(vendeurId);
                } else if (destinataire === 'admin') {
                    await initializeAdminConversation();
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // Afficher les conversations
        function displayConversations(conversations) {
            conversationsList.innerHTML = '';
            conversations.forEach(conv => {
                const div = document.createElement('div');
                div.className = 'conversation-item';
                div.innerHTML = `
                    <div class="conversation-name">${conv.prenom} ${conv.nom}</div>
                    <div class="conversation-date">${new Date(conv.last_message_date).toLocaleDateString()}</div>
                `;
                div.addEventListener('click', () => {
                    isAdminConversation = false;
                    loadMessages(conv.other_user_id);
                });
                conversationsList.appendChild(div);
            });
        }

        // Charger les messages d'une conversation
        function loadMessages(userId) {
            currentConversationId = userId;
            fetch(`../includes/messagerie.php?conversation_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    displayMessages(data.messages);
                    document.getElementById('destinataire_id').value = userId;
                })
                .catch(error => console.error('Erreur:', error));
        }

        // Afficher les messages
        function displayMessages(messages) {
            messagesContainer.innerHTML = '';
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = `message ${msg.expediteur_id == currentConversationId ? 'received' : 'sent'}`;
                div.innerHTML = `
                    <div class="message-sender">${msg.expediteur_prenom} ${msg.expediteur_nom}</div>
                    <div class="message-subject">${msg.sujet}</div>
                    <div class="message-content">${msg.contenu}</div>
                    <div class="message-time">${new Date(msg.date_envoi).toLocaleString()}</div>
                `;
                messagesContainer.appendChild(div);
            });
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Envoyer un message
        messageForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!currentConversationId) return;

            const formData = new FormData(messageForm);
            fetch('../includes/envoyer-message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                messageForm.reset();
                loadMessages(currentConversationId);
            })
            .catch(error => console.error('Erreur:', error));
        });

        // Recherche de conversations
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const items = conversationsList.getElementsByClassName('conversation-item');
            Array.from(items).forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Charger les conversations au démarrage
        loadConversations();

        // Rafraîchir les conversations toutes les 30 secondes
        setInterval(loadConversations, 30000);
    } catch (error) {
        console.error('Erreur:', error);
    }
}); 