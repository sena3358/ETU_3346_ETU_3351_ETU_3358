CREATE DATABASE tp_flight;

USE tp_flight;

CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'CLIENT') NOT NULL,
    statut ENUM('actif', 'inactif') DEFAULT 'actif'
);

CREATE TABLE fonds_etablissement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(12,2) NOT NULL,
    source VARCHAR(100),
    date_ajout DATE NOT NULL
);

CREATE TABLE fondTotal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montantTotal DECIMAL(12,2) NOT NULL
);

INSERT INTO fondTotal (montantTotal) VALUES (0);


CREATE TABLE type_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    taux_interet DECIMAL(5,2) NOT NULL
);

CREATE TABLE pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    id_type_pret INT NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    taux DECIMAL(5,2) NOT NULL,
    duree INT NOT NULL,
    assurance DECIMAL(5,2) DEFAULT 0,
    delai_grace INT DEFAULT 0,
    date_debut DATE NOT NULL,
    statut ENUM('en attente', 'en cours', 'soldé', 'en retard', 'refusé') DEFAULT 'en attente',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id),
    FOREIGN KEY (id_type_pret) REFERENCES type_pret(id)
);


CREATE TABLE remboursement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    date_paiement DATE NOT NULL,
    amortissement DECIMAL(12,2) NOT NULL,
    interet DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_pret) REFERENCES pret(id)
);

--Data

