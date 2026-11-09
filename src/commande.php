<?php
require 'config.php';
if (empty($_SESSION['client'])) { header('Location: login.php'); exit; }
if (empty($_SESSION['panier'])) { header('Location: panier.php'); exit; }

// Prix et stock relus depuis la base : on ne fait jamais confiance au navigateur
$ids = array_keys($_SESSION['panier']);
$in = implode(',', array_fill(0, count($ids), '?'));
$st = dbProduit()->prepare("SELECT * FROM produits WHERE id IN ($in)");
$st->execute($ids);
$produits = $st->fetchAll();
$total = 0; $erreur = '';
foreach ($produits as &$p) {
    $p['quantite'] = $_SESSION['panier'][$p['id']];
    if ($p['quantite'] > $p['stock']) $erreur = "Stock insuffisant pour « {$p['nom']} » (reste {$p['stock']}).";
    $total += $p['quantite'] * $p['prix'];
}
unset($p);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$erreur) {
    $adresse = trim($_POST['adresse']);
    if (!$adresse) { $erreur = 'Indiquez une adresse de livraison.'; }
    else {
        $dbC = dbCommande(); $dbP = dbProduit();
        try {
            $dbC->beginTransaction();
            $dbC->prepare('INSERT INTO commandes (client_id,total,adresse_livraison) VALUES (?,?,?)')
                ->execute([$_SESSION['client']['id'], $total, $adresse]);
            $cmdId = $dbC->lastInsertId();
            $ligne = $dbC->prepare('INSERT INTO commande_lignes (commande_id,produit_id,nom_produit,prix_unitaire,quantite) VALUES (?,?,?,?,?)');
            $stock = $dbP->prepare('UPDATE produits SET stock = stock - ? WHERE id=? AND stock >= ?');
            foreach ($produits as $p) {
                $stock->execute([$p['quantite'], $p['id'], $p['quantite']]);
                if ($stock->rowCount() === 0) throw new Exception("Stock épuisé pour {$p['nom']}.");
                $ligne->execute([$cmdId, $p['id'], $p['nom'], $p['prix'], $p['quantite']]);
            }
            $dbC->commit();
            $_SESSION['panier'] = [];
            entete('Commande confirmée');
            echo "<h1>Merci pour votre commande n° $cmdId</h1><p>Total : " . ar($total) . ". Nous vous contactons pour la livraison.</p><p><a class='btn' href='index.php'>Retour à la boutique</a></p>";
            pied(); exit;
        } catch (Exception $e) {
            if ($dbC->inTransaction()) $dbC->rollBack();
            $erreur = $e->getMessage();
        }
    }
}
entete('Commande');
?>
<h1>Valider la commande</h1>
<?php if ($erreur): ?><p class="alerte"><?= h($erreur) ?></p><?php endif; ?>
<table class="tableau">
  <?php foreach ($produits as $p): ?>
    <tr><td><?= h($p['nom']) ?> × <?= $p['quantite'] ?></td><td><?= ar($p['quantite'] * $p['prix']) ?></td></tr>
  <?php endforeach; ?>
  <tr class="total"><td>Total</td><td><?= ar($total) ?></td></tr>
</table>
<form method="post" class="form">
  <label>Adresse de livraison<input name="adresse" value="<?= h($_SESSION['client']['adresse'] ?? '') ?>" required></label>
  <button class="btn">Confirmer la commande</button>
</form>
<?php pied();
