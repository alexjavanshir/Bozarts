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

-- Insertion des données de test

-- Insertion des clients
INSERT INTO utilisateurs (type, nom, prenom, email, mot_de_passe, adresse, telephone) VALUES
('client', 'Dupont', 'Jean', 'jean.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 rue de Paris, 75001 Paris', '0612345678'),
('client', 'Martin', 'Sophie', 'sophie.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 avenue des Champs, 75008 Paris', '0623456789'),
('client', 'Bernard', 'Pierre', 'pierre.bernard@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '789 boulevard Saint-Germain, 75006 Paris', '0634567890');

-- Insertion des artisans
INSERT INTO utilisateurs (type, nom, prenom, email, mot_de_passe, description, adresse, telephone) VALUES
('artisan', 'Leroy', 'Marie', 'marie.leroy@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Artisan céramiste passionnée depuis 10 ans', '321 rue de la Poterie, 75011 Paris', '0645678901'),
('artisan', 'Petit', 'Luc', 'luc.petit@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sculpteur sur bois spécialisé dans le mobilier artisanal', '654 avenue du Bois, 75012 Paris', '0656789012'),
('artisan', 'Moreau', 'Julie', 'julie.moreau@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Créatrice de bijoux en argent et pierres naturelles', '987 rue des Artisans, 75013 Paris', '0667890123');

-- Insertion des produits
INSERT INTO produits (artisan_id, nom6, description, prix, image_url, categorie) VALUES
(4, 'Vase en céramique bleu', 'Vase artisanal en céramique émaillée bleue, pièce unique', 89.99, 'vase_bleu.jpg', 'Céramique'),
(4, 'Service à thé', 'Service à thé complet en céramique blanche avec motifs floraux', 149.99, 'service_the.jpg', 'Céramique'),
(5, 'Table basse en chêne', 'Table basse sculptée en chêne massif, design contemporain', 299.99, 'table_basse.jpg', 'Mobilier'),
(5, 'Chaise artisanale', 'Chaise en bois massif avec dossier sculpté', 199.99, 'chaise.jpg', 'Mobilier'),
(6, 'Collier en argent', 'Collier en argent 925 avec pierre de lune', 79.99, 'collier.jpg', 'Bijoux'),
(6, 'Boucles d\'oreilles', 'Boucles d\'oreilles en argent avec améthyste', 59.99, 'boucles_oreilles.jpg', 'Bijoux');