CREATE TABLE IF NOT EXISTS commandes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  total DECIMAL(12,0) NOT NULL,
  statut ENUM('en_attente','payee','expediee','livree','annulee') DEFAULT 'en_attente',
  adresse_livraison VARCHAR(255) NOT NULL,
  cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS commande_lignes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  commande_id INT NOT NULL,
  produit_id INT NOT NULL,
  nom_produit VARCHAR(120) NOT NULL,
  prix_unitaire DECIMAL(10,0) NOT NULL,
  quantite INT NOT NULL,
  FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
);
