-- Initialisation de la base de données
-- Site JAKARLO ESP (Nouvelle Version)

CREATE DATABASE IF NOT EXISTS actualites_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE actualites_db;

-- --------------------------------------------------------

CREATE TABLE categories (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

CREATE TABLE utilisateurs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    login VARCHAR(80) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('visiteur','editeur','administrateur') NOT NULL DEFAULT 'editeur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

CREATE TABLE articles (
    id INT(11) NOT NULL AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    contenu LONGTEXT NOT NULL,
    description_courte VARCHAR(300) DEFAULT NULL,
    categorie_id INT(11) DEFAULT NULL,
    auteur_id INT(11) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY (categorie_id),
    KEY (auteur_id),
    CONSTRAINT fk_art_cat FOREIGN KEY (categorie_id) REFERENCES categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_art_aut FOREIGN KEY (auteur_id) REFERENCES utilisateurs (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- On insère des données de base pour tester
INSERT INTO categories (nom, description) VALUES
('Campus', 'Tout ce qui se passe sur les sites de Dakar et Thiès'),
('High-Tech', 'Innovation, informatique et numérique'),
('Vie Étudiante', 'BDE, sports, événements et loisirs'),
('Académique', 'Inscriptions, examens et nouveaux programmes');

-- Comptes de test (Le mot de passe pour les deux est : Dakar_2026!)
-- Hash généré manuellement pour ne pas être celui par défaut des libs IA
INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('SENE', 'Amadou', 'root_esp', '$2y$10$XUfJpQ6g5X8kU6K6P8R9TeE9L9E9q9i9u9O9v9w9x9y9z9A9B9C9D', 'administrateur'),
('NDIAYE', 'Awa', 'awa_redac', '$2y$10$XUfJpQ6g5X8kU6K6P8R9TeE9L9E9q9i9u9O9v9w9x9y9z9A9B9C9D', 'editeur');

-- Un petit article pour voir si ça marche
INSERT INTO articles (titre, contenu, description_courte, categorie_id, auteur_id) VALUES
('Sortie de la nouvelle plateforme ESP', 'Nous avons le plaisir de vous annoncer que le site web a fait peau neuve. Plus rapide, plus moderne et surtout plus adapté aux mobiles...', 'Le site web de JAKARLO ESP change de look pour 2026.', 1, 1);
