-- Création de la base de données
CREATE DATABASE IF NOT EXISTS bozarts;
USE bozarts;

-- Table des utilisateurs (artisans)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('artisan', 'client', 'admin') NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    description TEXT,
    adresse TEXT,
    telephone VARCHAR(20),
    statut VARCHAR(20) DEFAULT 'actif',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artisan_id INT,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    materiau VARCHAR(100),
    dimensions VARCHAR(100),
    delai_fabrication INT, -- en jours
    image_url VARCHAR(255),
    categorie VARCHAR(100),
    en_stock BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artisan_id) REFERENCES utilisateurs(id)
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    statut VARCHAR(20) DEFAULT 'en attente',
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_livraison_prevue DATETIME,
    adresse_livraison TEXT,
    montant_total DECIMAL(10,2),
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id)
);


-- Table des lignes de commande
CREATE TABLE IF NOT EXISTS commande_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT,
    produit_id INT,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Table des événements
CREATE TABLE IF NOT EXISTS evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artisan_id INT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    lieu VARCHAR(255),
    type VARCHAR(100), -- salon, atelier, exposition
    FOREIGN KEY (artisan_id) REFERENCES utilisateurs(id)
);


-- Table des avis
CREATE TABLE IF NOT EXISTS avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    artisan_id INT,
    produit_id INT,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    approuve BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (artisan_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Table pour la galerie virtuelle
CREATE TABLE IF NOT EXISTS galerie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artisan_id INT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artisan_id) REFERENCES utilisateurs(id)
);

-- Table pour la messagerie
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    sujet VARCHAR(255),
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id)
);

-- Table pour le panier
CREATE TABLE IF NOT EXISTS paniers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id),
    UNIQUE KEY unique_panier (client_id, produit_id)
);

-- Table FAQ
CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    reponse TEXT NOT NULL,
    categorie VARCHAR(100),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
);