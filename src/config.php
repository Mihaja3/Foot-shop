<?php
session_start();

function db(string $host, string $name): PDO {
    static $cx = [];
    return $cx[$host] ??= new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4", 'root', '123456', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
function dbProduit(): PDO  { return db('mysql-produit',  'produit_db'); }
function dbAchat(): PDO    { return db('mysql-achat',    'achat_db'); }
function dbCommande(): PDO { return db('mysql-commande', 'commande_db'); }

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function ar($n): string { return number_format((float)$n, 0, ',', ' ') . ' Ar'; }
function nbPanier(): int { return array_sum($_SESSION['panier'] ?? []); }

function entete(string $titre): void { ?>
<!DOCTYPE html>
<html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($titre) ?> – Tribune Foot</title>
<link rel="stylesheet" href="style.css">
</head><body>
<header class="haut">
  <div class="contenu barre">
    <a class="logo" href="index.php">Tribune Foot</a>
    <nav>
      <a href="index.php">Boutique</a>
      <a href="panier.php">Panier (<?= nbPanier() ?>)</a>
      <?php if (!empty($_SESSION['client'])): ?>
        <span class="salut"><?= h($_SESSION['client']['nom']) ?></span>
        <a href="logout.php">Se déconnecter</a>
      <?php else: ?>
        <a href="login.php">Se connecter</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="contenu">
<?php }

function pied(): void { ?>
</main>
<footer class="bas"><div class="contenu">Tribune Foot – équipement de football</div></footer>
</body></html>
<?php }
