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
    amortissement DECIMAL(12,2) NOT NULL,
    interet DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_pret) REFERENCES pret(id)
);

--Data
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, statut) VALUES
('Randria', 'Michel', 'admin@example.com', '$2y$10$1ZrhDxJ1nH8vQONQ8C3IHuJCVKk.Qd4vF3D8vlQb8EfwD3Wn7FJ1G', 'ADMIN', 'actif'), -- mot de passe : admin123
('Rakoto', 'Sarah', 'sarah@example.com', '$2y$10$F2EvmI/6hw5WxNE4IoSvMeNAxS5SvmE.0w1.HpJqZ3Jrh2RBPdZd2', 'CLIENT', 'actif'), -- mot de passe : client123
('Rasoa', 'Jean', 'jean@example.com', '$2y$10$F2EvmI/6hw5WxNE4IoSvMeNAxS5SvmE.0w1.HpJqZ3Jrh2RBPdZd2', 'CLIENT', 'actif');  -- client123

INSERT INTO fonds_etablissement (montant, source, date_ajout) VALUES
(10000000.00, 'Capital Initial', '2025-07-01'),
(2500000.00, 'Investissement Externe', '2025-07-03'),
(500000.00, 'Revenus intérêt', '2025-07-05');

INSERT INTO type_pret (nom, taux_interet) VALUES
('Prêt personnel', 7.50),
('Crédit immobilier', 5.00),
('Micro-crédit', 12.00);

INSERT INTO pret (id_utilisateur, id_type_pret, montant, taux, duree, date_debut, statut) VALUES
(2, 1, 500000.00, 7.50, 12, '2025-07-06', 'en cours'),
(3, 2, 3000000.00, 5.00, 60, '2025-07-02', 'refusé');



-- Supposons que tu as des prêts avec id 1, 2 et 3
INSERT INTO remboursement (id_pret, date_paiement, amortissement, interet) VALUES
(1, '2024-01-01', 50000.00, 5000.00),
(1, '2024-02-01', 50000.00, 4000.00),
(1, '2024-03-01', 50000.00, 3000.00),
(1, '2024-04-01', 50000.00, 2000.00),
(1, '2024-05-01', 50000.00, 1000.00),

(2, '2024-06-01', 25000.00, 2000.00),
(2, '2024-07-01', 25000.00, 1500.00),
(2, '2024-08-01', 25000.00, 1000.00),
(2, '2024-09-01', 25000.00, 500.00);

-- (3, '2024-10-01', 30000.00, 3000.00),
-- (3, '2024-11-01', 30000.00, 2500.00),
-- (3, '2024-12-01', 30000.00, 2000.00),
-- (3, '2025-01-01', 30000.00, 1500.00),
-- (3, '2025-02-01', 30000.00, 1000.00);
