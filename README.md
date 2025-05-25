# Bozarts - Plateforme pour Artisans et Créateurs

Bozarts est une plateforme web permettant aux artisans et créateurs indépendants de présenter et vendre leurs créations. Le site offre une vitrine virtuelle pour les artisans, un système de commandes personnalisées, et une section événements pour promouvoir les salons d'artisanat et ateliers.

## Fonctionnalités principales

- **Inscription et connexion** pour les artisans et clients
- **Gestion de profil utilisateur** avec modification des informations personnelles
- **Gestion des produits** : ajout, modification et suppression des créations
- **Système de commandes** personnalisées
- **Galerie virtuelle** pour découvrir les créations
- **Section événements** pour les salons et ateliers
  - Création et gestion d'événements
  - Gestion des participants
  - Système d'inscription aux événements
- **Système d'avis** pour les artisans
- **Recherche avancée** de produits
  - Historique des recherches récentes
  - Suggestions de recherche
- **Administration**
  - Gestion des utilisateurs
  - Gestion des CGU
  - Gestion des FAQ
- **Messagerie** entre utilisateurs

## Structure du projet

```
bozarts/
├── assets/
│   ├── css/
│   │   ├── style.css          # Styles généraux du site
│   │   ├── profil.css         # Styles de la page profil
│   │   ├── recherche.css      # Styles de la recherche
│   │   ├── form.css           # Styles des formulaires
│   │   └── events.css         # Styles de la gestion des événements
│   ├── icons/                 # Icônes du site
│   ├── articles/              # Images des produits
│   └── images/                # Autres images
├── config/
│   └── database.php           # Configuration de la base de données
├── includes/
│   ├── ajouter_avis.php       # Ajout d'avis sur les produits
│   ├── ajouter-evenement.php  # Ajout d'un événement
│   ├── ajouter-panier.php     # Ajout au panier
│   ├── ajouter-produit.php    # Ajout d'un produit
│   ├── afficher_panier.php    # Affichage du panier
│   ├── add_cgu.php            # Ajout des CGU
│   ├── add_faq.php            # Ajout de FAQ
│   ├── changer_profil.php     # Modification du profil
│   ├── check_session.php      # Vérification de session
│   ├── completer-profil.php   # Complétion du profil
│   ├── connexion.php          # Connexion utilisateur
│   ├── delete_cgu.php         # Suppression des CGU
│   ├── delete_faq.php         # Suppression de FAQ
│   ├── delete_user.php        # Suppression d'utilisateur
│   ├── envoyer-message.php    # Envoi de messages
│   ├── get-admin_id.php       # Récupération ID admin
│   ├── get-avis.php           # Récupération des avis
│   ├── get_cgu.php            # Récupération des CGU
│   ├── get-evenements.php     # Récupération des événements
│   ├── get_faq.php            # Récupération des FAQ
│   ├── get-mes-annonces.php   # Récupération des annonces
│   ├── get_users.php          # Récupération des utilisateurs
│   ├── inscription.php        # Inscription utilisateur
│   ├── logout.php             # Déconnexion
│   ├── messagerie.php         # Gestion des messages
│   ├── mes-annonces.php       # Gestion des annonces
│   ├── participer-evenement.php # Participation aux événements
│   ├── profil.php             # Gestion du profil
│   ├── produit.php            # Gestion des produits
│   ├── recherche.php          # Recherche de produits
│   ├── supprimer-evenement.php # Suppression d'événement
│   ├── supprimer-panier.php   # Suppression du panier
│   ├── supprimer-produit.php  # Suppression de produit
│   ├── toggle_admin.php       # Gestion des droits admin
│   └── update_cgu.php         # Mise à jour des CGU
├── js/
│   ├── admin.js               # Gestion de l'interface admin
│   ├── ajouter-evenement.js   # Gestion des événements
│   ├── avis.js                # Gestion des avis
│   ├── cgu.js                 # Gestion des CGU
│   ├── connexion.js           # Gestion de la connexion
│   ├── faq.js                 # Gestion des FAQ
│   ├── header.js              # Gestion de l'en-tête
│   ├── index.js               # Scripts de la page d'accueil
│   ├── inscription.js         # Gestion de l'inscription
│   ├── mes-annonces.js        # Gestion des annonces
│   ├── messagerie.js          # Gestion des messages
│   ├── panier.js              # Gestion du panier
│   ├── produit.js             # Gestion des produits
│   ├── profil.js              # Gestion du profil
│   ├── recent-searches.js     # Gestion des recherches récentes
│   └── recherche.js           # Gestion de la recherche
├── pages/
│   ├── admin.html             # Interface d'administration
│   ├── ajouter-evenement.html # Création d'événement
│   ├── ajouter-produit.html   # Ajout de produit
│   ├── CGU.html               # Conditions générales
│   ├── completer-profil.html  # Complétion du profil
│   ├── connexion.html         # Page de connexion
│   ├── faq.html               # FAQ
│   ├── index.html             # Page d'accueil
│   ├── inscription.html       # Page d'inscription
│   ├── mes-annonces.html      # Gestion des annonces
│   ├── messagerie.html        # Messagerie
│   ├── panier.html            # Panier d'achat
│   ├── produit.html           # Page produit
│   ├── profil.html            # Profil utilisateur
│   └── recherche.html         # Page de recherche
├── database/
│   └── init.sql               # Structure initiale de la base de données
└── uploads/
    ├── products/              # Images des produits
    └── events/                # Images des événements
```

## Fonctionnement de la gestion utilisateur

- **Inscription/Connexion**: Les utilisateurs peuvent s'inscrire comme client ou artisan
- **Session utilisateur**: Le système maintient la session utilisateur entre les pages
- **Profil personnalisé**: Chaque utilisateur peut voir et modifier ses informations
- **Types de profils**: Différenciation entre clients et artisans avec fonctionnalités adaptées

## Installation

1. Cloner le dépôt :
```bash
git clone https://github.com/votre-username/bozarts.git
```

2. Configurer la base de données :
- Créer une base de données MySQL nommée `bozarts`
- Importer le fichier `database/init.sql`
- Configurer les paramètres de connexion dans `config/database.php`

3. Configurer le serveur web :
- Placer les fichiers dans le répertoire de votre serveur web
- Assurez-vous que PHP a les droits d'écriture dans le dossier `uploads/`

4. Accéder au site :
- Ouvrir votre navigateur et accéder à l'URL du site

## Technologies utilisées

- HTML5
- CSS3
- JavaScript (vanilla)
- PHP 7.4+
- MySQL
- MAMP/WAMP pour l'environnement de développement

## Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## Contact

Pour toute question ou suggestion, n'hésitez pas à nous contacter à contact@bozarts.fr
