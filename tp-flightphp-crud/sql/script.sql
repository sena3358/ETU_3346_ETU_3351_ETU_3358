CREATE DATABASE tp_flight CHARACTER SET utf8mb4;

USE tp_flight;

CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'CLIENT') NOT NULL,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
);

CREATE TABLE fonds_etablissement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(12,2) NOT NULL,
    source VARCHAR(100), -- Ex: Capital, Investisseur, Bénéfice
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
);

CREATE TABLE type_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    taux_interet DECIMAL(5,2) NOT NULL, -- en pourcentage ex: 7.50
    montant_min DECIMAL(12,2) NOT NULL,
    montant_max DECIMAL(12,2) NOT NULL,
    duree_min INT NOT NULL, -- en mois
    duree_max INT NOT NULL,
);

CREATE TABLE pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL, -- client
    id_type_pret INT NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    taux DECIMAL(5,2) NOT NULL, -- taux appliqué
    duree INT NOT NULL, -- en mois
    date_debut DATE NOT NULL,
    statut ENUM('en cours', 'soldé', 'en retard', 'refusé') DEFAULT 'en cours',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id),
    FOREIGN KEY (id_type_pret) REFERENCES type_pret(id)
);

CREATE TABLE remboursement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    date_paiement DATE NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    statut ENUM('payé', 'en retard', 'impayé') DEFAULT 'payé',
    FOREIGN KEY (id_pret) REFERENCES pret(id)
);
