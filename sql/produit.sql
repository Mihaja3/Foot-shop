CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(60) NOT NULL
);
CREATE TABLE IF NOT EXISTS produits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categorie_id INT NOT NULL,
  nom VARCHAR(120) NOT NULL,
  description TEXT,
  prix DECIMAL(10,0) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  FOREIGN KEY (categorie_id) REFERENCES categories(id)
);
INSERT INTO categories (nom) VALUES ('Crampons'),('Protège-tibias'),('Ballons'),('Gants de gardien'),('Maillots'),('Chaussettes');
INSERT INTO produits (categorie_id, nom, description, prix, stock) VALUES
(1,'Crampons Vitesse Pro FG','Terrain sec et naturel, tige ultra-légère.',320000,15),
(1,'Crampons Contrôle MG','Multi-terrains, touché de balle précis.',250000,20),
(1,'Crampons Salle Indoor','Semelle gomme pour futsal.',140000,25),
(2,'Protège-tibias Carbone','Coque rigide, maintien par manchon.',60000,40),
(2,'Protège-tibias Junior','Léger, mousse EVA, pour enfants.',25000,50),
(3,'Ballon Match Taille 5','Thermocollé, homologué compétition.',120000,30),
(3,'Ballon Entraînement T4','Cousu main, résistant.',45000,35),
(4,'Gants Gardien Grip+','Paume latex 4 mm, barrettes de maintien.',90000,18),
(5,'Maillot Domicile Équipe','Tissu respirant séchage rapide.',85000,60),
(6,'Chaussettes de match','Renfort talon et pointe.',12000,100);
