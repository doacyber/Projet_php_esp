CREATE DATABASE IF NOT EXISTS actualites_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE actualites_db;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    login VARCHAR(80) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('visiteur','editeur','administrateur') DEFAULT 'editeur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    description_courte VARCHAR(300),
    categorie_id INT,
    auteur_id INT,
    image VARCHAR(255) DEFAULT NULL,
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO categories (nom, description) VALUES
('Technologie', 'Articles sur la tech et l\'informatique'),
('Sport', 'Actualites sportives'),
('Politique', 'Vie politique nationale et internationale'),
('Education', 'Actualites du monde de l\'education'),
('Culture', 'Art, cinema, musique...');

INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Diallo', 'Mamadou', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrateur'),
('Sow', 'Fatou', 'editeur1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editeur');

INSERT INTO articles (titre, contenu, description_courte, categorie_id, auteur_id) VALUES
('Lancement du nouveau centre informatique', 'L\'Ecole Superieure Polytechnique vient d\'inaugurer son nouveau centre informatique equipe de 80 postes derniere generation. Cet espace modernise va permettre aux etudiants de travailler dans de meilleures conditions...', 'L\'ESP inaugure un nouveau centre informatique ultra-moderne.', 1, 2),
('Les etudiants de l\'ESP remportent le hackathon national', 'Une equipe de cinq etudiants du departement genie informatique a remporte le hackathon organise par le ministere du numerique. Leur projet portait sur une solution de gestion des transports urbains...', 'Victoire d\'une equipe ESP au hackathon national du numerique.', 1, 2),
('Resultats du championnat interscolaire de football', 'L\'equipe de football de l\'ESP a termine a la deuxieme place du championnat interscolaire apres une finale disputee contre l\'Universite Cheikh Anta Diop. Un match tres accroche qui s\'est termine aux penaltys...', 'L\'equipe de foot de l\'ESP finaliste du championnat interscolaire.', 2, 2),
('Reforme du systeme LMD en discussion', 'Le ministere de l\'enseignement superieur a engage des consultations avec les universites et grandes ecoles pour une eventuelle reforme du systeme Licence-Master-Doctorat...', 'Des consultations en cours pour reformer le systeme LMD.', 4, 2),
('Festival culturel de fin d\'annee', 'Le bureau estudiantin organise son festival annuel du 20 au 22 mars. Au programme : expositions, concerts, theatre et bien d\'autres activites ouvertes a tous...', 'Le bureau estudiantin prepare son grand festival de fin d\'annee.', 5, 2);
