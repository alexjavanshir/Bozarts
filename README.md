# Bozarts - Plateforme pour Artisans et Créateurs

Bozarts est une plateforme web permettant aux artisans et créateurs indépendants de présenter et vendre leurs créations. Le site offre une vitrine virtuelle pour les artisans, un système de commandes personnalisées, et une section événements pour promouvoir les salons d'artisanat et ateliers.

## Fonctionnalités principales

- **Inscription et connexion** pour les artisans
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
│   │   ├── style.css
│   │   └── gallery.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── register.php
│   ├── login.php
│   ├── add_product.php
│   ├── add_event.php
│   ├── place_order.php
│   ├── add_review.php
│   ├── search.php
│   └── gallery.php
├── database/
│   └── init.sql
└── uploads/
    └── products/
```

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
- JavaScript
- PHP 7.4+
- MySQL
- Apache/Nginx

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
