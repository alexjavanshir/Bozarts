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
    droit ENUM('sans-droit', 'admin') DEFAULT 'sans-droit' NOT NULL,
    statut VARCHAR(20) DEFAULT 'actif',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les CGU
CREATE TABLE IF NOT EXISTS cgu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    ordre INT DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table pour les FAQ
CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    titre_reponse VARCHAR(255) NOT NULL,
    reponse TEXT NOT NULL,
    ordre INT DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
    FOREIGN KEY (client_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id),
    UNIQUE KEY unique_panier (client_id, produit_id)
);

-- Insertion des données de test


-- Insertion des clients
INSERT INTO utilisateurs (type, nom, prenom, email, mot_de_passe, adresse, telephone, droit) VALUES
('client', 'Dupont', 'Jean', 'jean.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 rue de Paris, 75001 Paris', '0612345678', 'sans-droit'),
('client', 'Martin', 'Sophie', 'sophie.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 avenue des Champs, 75008 Paris', '0623456789', 'sans-droit'),
('client', 'Bernard', 'Pierre', 'pierre.bernard@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '789 boulevard Saint-Germain, 75006 Paris', '0634567890', 'sans-droit');

-- Insertion des artisans
INSERT INTO utilisateurs (type, nom, prenom, email, mot_de_passe, description, adresse, telephone, droit) VALUES
('artisan', 'Leroy', 'Marie', 'marie.leroy@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Artisan céramiste passionnée depuis 10 ans', '321 rue de la Poterie, 75011 Paris', '0645678901', 'sans-droit'),
('artisan', 'Petit', 'Luc', 'luc.petit@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sculpteur sur bois spécialisé dans le mobilier artisanal', '654 avenue du Bois, 75012 Paris', '0656789012', 'sans-droit'),
('artisan', 'Moreau', 'Julie', 'julie.moreau@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Créatrice de bijoux en argent et pierres naturelles', '987 rue des Artisans, 75013 Paris', '0667890123', 'sans-droit');

-- Insertion des produits
INSERT INTO produits (artisan_id, nom, description, prix, image_url, categorie) VALUES
(4, 'Vase en céramique bleu', 'Vase artisanal en céramique émaillée bleue, pièce unique', 89.99, '../assets/articles/article1.jpg', 'Céramique'),
(4, 'Service à thé', 'Service à thé complet en céramique blanche avec motifs floraux', 149.99, '../assets/articles/article2.jpg', 'Céramique'),
(5, 'Table basse en chêne', 'Table basse sculptée en chêne massif, design contemporain', 299.99, '../assets/articles/article3.jpg', 'Mobilier'),
(5, 'Chaise artisanale', 'Chaise en bois massif avec dossier sculpté', 199.99, '../assets/articles/article4.jpg', 'Mobilier'),
(6, 'Collier en argent', 'Collier en argent 925 avec pierre de lune', 79.99, '../assets/articles/article5.jpg', 'Bijoux'),
(6, 'Boucles d\'oreilles', 'Boucles d\'oreilles en argent avec améthyste', 59.99, '../assets/articles/article6.jpg', 'Bijoux');

-- Insertion des avis sur les produits
INSERT INTO avis (client_id, produit_id, note, commentaire, date_creation) VALUES
(1, 1, 5, 'Superbe vase, la qualité est exceptionnelle et les couleurs sont magnifiques !', '2024-03-15 14:30:00'),
(2, 1, 4, 'Très beau vase, un peu plus petit que prévu mais très élégant.', '2024-03-16 09:15:00'),
(3, 2, 5, 'Service à thé magnifique, la finition est parfaite.', '2024-03-14 16:45:00'),
(1, 3, 5, 'Table basse de grande qualité, le bois est magnifique.', '2024-03-13 11:20:00'),
(2, 4, 4, 'Belle chaise, très confortable et solide.', '2024-03-12 15:40:00'),
(3, 5, 5, 'Collier magnifique, la pierre de lune est superbe.', '2024-03-11 10:30:00'),
(1, 6, 4, 'Jolies boucles d\'oreilles, très élégantes.', '2024-03-10 13:25:00');

-- Insertion des données de FAQ
INSERT INTO faq (question, titre_reponse, reponse, ordre) VALUES
('Comment devenir artisan sur Bozarts ?', 'Processus d\'inscription pour les artisans', 'Pour devenir artisan sur Bozarts, il vous suffit de créer un compte en sélectionnant "Artisan" comme type de profil lors de l\'inscription. Vous devrez ensuite compléter votre profil avec vos informations professionnelles, une description de votre activité et télécharger quelques photos de vos créations. Notre équipe validera votre compte dans un délai de 48h ouvrées.', 1),
('Comment passer commande sur Bozarts ?', 'Processus de commande', 'Pour commander sur Bozarts, parcourez notre galerie de produits et ajoutez les articles souhaités à votre panier. Vous pouvez également contacter directement un artisan pour une commande personnalisée. Une fois vos articles sélectionnés, rendez-vous dans votre panier, vérifiez votre commande et procédez au paiement. Vous recevrez une confirmation par email avec le suivi de votre commande.', 2),
('Quels sont les délais de livraison ?', 'Délais de livraison', 'Les délais de livraison varient selon les artisans et le type de produit. Pour les articles en stock, comptez 3 à 5 jours ouvrés. Pour les créations sur mesure, le délai est indiqué par l\'artisan lors de la validation de votre commande. Vous pouvez suivre l\'avancement de votre commande dans la section "Mes transactions" de votre compte.', 3),
('Quels sont les modes de paiement acceptés ?', 'Options de paiement', 'Bozarts accepte plusieurs modes de paiement sécurisés : carte bancaire (Visa, Mastercard), PayPal, et virement bancaire. Pour les commandes personnalisées importantes, un acompte peut être demandé avant la réalisation. Tous les paiements sont sécurisés et vos informations bancaires ne sont jamais stockées sur notre plateforme.', 4),
('Comment contacter un artisan pour une commande personnalisée ?', 'Commandes personnalisées', 'Pour demander une création sur mesure, visitez le profil de l\'artisan qui vous intéresse et cliquez sur le bouton "Contacter l\'artisan" ou "Commander sur mesure". Décrivez précisément votre projet, les dimensions souhaitées, matériaux, couleurs, etc. L\'artisan vous répondra avec un devis et un délai estimatif. Une fois les détails finalisés, vous pourrez procéder au paiement pour lancer la création.', 5),
('Quelle est la politique de retour et remboursement ?', 'Retours et remboursements', 'Si vous n\'êtes pas satisfait de votre achat, vous disposez de 14 jours à compter de la réception pour nous signaler votre intention de retour. Les articles doivent être retournés dans leur état d\'origine et dans leur emballage. Pour les créations personnalisées, les retours ne sont généralement pas acceptés, sauf en cas de défaut avéré. Contactez notre service client pour plus d\'informations sur la procédure de retour.', 6),
('Comment devenir vendeur sur Bozarts et quelles sont les commissions ?', 'Devenir vendeur et commissions', 'Pour vendre sur Bozarts, inscrivez-vous comme artisan et complétez votre profil professionnel. Bozarts prélève une commission de 10% sur chaque vente réalisée sur la plateforme. Cette commission comprend les frais de traitement des paiements et l\'utilisation de nos services. Les paiements sont versés aux artisans tous les 15 jours, après déduction de la commission. Vous pouvez consulter vos ventes et revenus dans votre espace vendeur.', 7),
('Comment promouvoir mes créations sur Bozarts ?', 'Promotion de vos créations', 'Pour mettre en valeur vos créations, ajoutez des photos de qualité, des descriptions détaillées et utilisez des mots-clés pertinents. Participez activement à la communauté en répondant rapidement aux messages et en publiant régulièrement de nouvelles créations. Bozarts propose également des options de mise en avant payantes comme l\'affichage en tête de liste ou la présentation dans notre newsletter mensuelle. Contactez-nous pour plus d\'informations sur ces services.', 8),
('Comment fonctionne le système d\'évaluation des artisans ?', 'Système d\'évaluation', 'Après chaque achat, les clients peuvent évaluer l\'artisan sur une échelle de 1 à 5 étoiles et laisser un commentaire. Ces évaluations portent sur la qualité du produit, la communication et le respect des délais. La note moyenne est affichée sur le profil de l\'artisan. Ce système permet de valoriser les artisans de qualité et d\'aider les clients dans leurs choix. Les avis inappropriés peuvent être signalés à notre équipe de modération.', 9),
('Comment participer aux événements et salons organisés par Bozarts ?', 'Événements et salons', 'Bozarts organise régulièrement des salons d\'artisanat et des expositions physiques pour permettre aux artisans de rencontrer leur public. Les artisans inscrits sur la plateforme sont prioritaires pour participer à ces événements. Pour vous inscrire, rendez-vous dans la section "Événements" de votre espace artisan et sélectionnez les événements qui vous intéressent. Les frais de participation et les modalités sont indiqués pour chaque événement.', 10);

-- Insertion des données de CGU
INSERT INTO cgu (titre, contenu, ordre) VALUES
('1. Acceptation des conditions', 'En accédant et en utilisant le site Bozarts, vous acceptez d\'être lié par les présentes conditions générales d\'utilisation. Si vous n\'acceptez pas ces conditions, veuillez ne pas utiliser notre site...', 1),
('2. Description du service', 'Bozarts est une plateforme mettant en relation des artisans et des clients pour la vente et l\'achat de créations artisanales. Le site permet aux artisans de présenter leurs œuvres et aux clients de les découvrir et de les acquérir.', 2),
('3. Inscription et compte utilisateur', 'Pour utiliser certaines fonctionnalités du site, vous devez créer un compte. Vous êtes responsable de maintenir la confidentialité de votre compte et de votre mot de passe. Vous acceptez de nous informer immédiatement de toute utilisation non autorisée de votre compte.', 3),
('4. Obligations des utilisateurs', 'En tant qu\'utilisateur, vous vous engagez à :\n- Fournir des informations exactes et complètes lors de l\'inscription\n- Ne pas utiliser le site à des fins illégales ou interdites\n- Ne pas perturber le fonctionnement normal du site\n- Respecter les droits de propriété intellectuelle', 4),
('5. Propriété intellectuelle', 'Tout le contenu présent sur Bozarts (textes, images, logos, etc.) est protégé par les droits de propriété intellectuelle. Toute reproduction ou utilisation non autorisée est strictement interdite.', 5),
('6. Transactions et paiements', 'Les transactions entre artisans et clients sont gérées par notre plateforme. Les paiements sont sécurisés et traités conformément à notre politique de confidentialité.', 6),
('7. Protection des données personnelles', 'Nous collectons et traitons vos données personnelles conformément à notre politique de confidentialité. En utilisant notre site, vous acceptez ce traitement.', 7),
('8. Limitation de responsabilité', 'Bozarts ne peut être tenu responsable des dommages directs ou indirects résultant de l\'utilisation du site ou de l\'impossibilité d\'y accéder.', 8),
('9. Modification des conditions', 'Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications prennent effet dès leur publication sur le site.', 9),
('10. Contact', 'Pour toute question concernant ces conditions, veuillez nous contacter via notre formulaire de contact.', 10);
