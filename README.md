# Bozarts - Plateforme pour Artisans et Créateurs

Bozarts est une plateforme web permettant aux artisans et créateurs indépendants de présenter et vendre leurs créations. Le site offre une vitrine virtuelle pour les artisans, un système de commandes personnalisées, et une section événements pour promouvoir les salons d'artisanat et ateliers.

## Fonctionnalités principales

- **Inscription et connexion** pour les artisans et clients
- **Gestion de profil utilisateur** avec modification des informations personnelles
- **Gestion des produits** : ajout, modification et suppression des créations
- **Système de commandes** personnalisées
- **Galerie virtuelle** pour découvrir les créations
- **Section événements** pour les salons et ateliers
- **Système d'avis** pour les artisans
- **Recherche avancée** de produits

## Structure du projet

```
bozarts/
├── assets/
│   ├── css/
│   │   ├── style.css          # Styles généraux du site
│   │   ├── profil.css         # Styles de la page profil
│   │   ├── recherche.css      # Styles de la recherche
│   │   └── form.css           # Styles des formulaires
│   ├── icons/                 # Icônes du site
│   ├── articles/              # Images des produits
│   └── images/                # Autres images
├── config/
│   └── database.php           # Configuration de la base de données
├── includes/
│   ├── register.php           # Traitement de l'inscription
│   ├── connexion.php          # Traitement de la connexion
│   ├── check_session.php      # Vérification de session utilisateur
│   ├── logout.php             # Déconnexion utilisateur
│   ├── profil.php             # Récupération des données utilisateur
│   ├── changer_profil.php     # Modification des informations utilisateur
│   ├── add_product.php        # Ajout de produit
│   ├── add_event.php          # Ajout d'événement
│   ├── place_order.php        # Gestion des commandes
│   ├── add_review.php         # Gestion des avis
│   └── search.php             # Recherche de produits
├── js/
│   ├── profil.js              # Gestion de la page profil
│   ├── header.js              # Gestion dynamique de l'en-tête
│   └── recherche.js           # Fonctionnalités de recherche
├── pages/
│   ├── index.html             # Page d'accueil
│   ├── connexion.html         # Page de connexion
│   ├── inscription.html       # Page d'inscription
│   ├── profil.html            # Page de profil utilisateur
│   ├── mes-annonces.html      # Gestion des annonces de l'artisan
│   ├── mes-transactions.html  # Suivi des transactions
│   ├── panier.html            # Panier d'achat
│   ├── produit.html           # Page produit
│   └── recherche.html         # Page de recherche
├── database/
│   └── init.sql               # Structure initiale de la base de données
└── uploads/
    └── products/              # Dossier pour les images de produits uploadées
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
