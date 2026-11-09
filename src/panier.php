<?php
require 'config.php';
$_SESSION['panier'] ??= [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    switch ($_POST['action'] ?? '') {
        case 'ajouter':
            $_SESSION['panier'][$id] = ($_SESSION['panier'][$id] ?? 0) + 1; break;
        case 'maj':
            $q = max(0, (int)$_POST['quantite']);
            if ($q) $_SESSION['panier'][$id] = $q; else unset($_SESSION['panier'][$id]);
            break;
        case 'supprimer':
            unset($_SESSION['panier'][$id]); break;
    }
    header('Location: panier.php'); exit;
}

$lignes = []; $total = 0;
if ($_SESSION['panier']) {
    $ids = array_keys($_SESSION['panier']);
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = dbProduit()->prepare("SELECT * FROM produits WHERE id IN ($in)");
    $st->execute($ids);
    foreach ($st->fetchAll() as $p) {
        $p['quantite'] = $_SESSION['panier'][$p['id']];
        $p['sous_total'] = $p['quantite'] * $p['prix'];
        $total += $p['sous_total'];
        $lignes[] = $p;
    }
}
entete('Panier');
?>
<h1>Votre panier</h1>
<?php if (!$lignes): ?>
  <p>Votre panier est vide. <a href="index.php">Voir la boutique</a></p>
<?php else: ?>
<table class="tableau">
  <tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Sous-total</th><th></th></tr>
  <?php foreach ($lignes as $l): ?>
  <tr>
    <td><?= h($l['nom']) ?></td>
    <td><?= ar($l['prix']) ?></td>
    <td>
      <form method="post" class="en-ligne">
        <input type="hidden" name="action" value="maj"><input type="hidden" name="id" value="<?= $l['id'] ?>">
        <input type="number" name="quantite" min="0" max="<?= $l['stock'] ?>" value="<?= $l['quantite'] ?>">
        <button class="btn petit">Mettre à jour</button>
      </form>
    </td>
    <td><?= ar($l['sous_total']) ?></td>
    <td>
      <form method="post"><input type="hidden" name="action" value="supprimer"><input type="hidden" name="id" value="<?= $l['id'] ?>">
      <button class="lien">Retirer</button></form>
    </td>
  </tr>
  <?php endforeach; ?>
  <tr class="total"><td colspan="3">Total</td><td colspan="2"><?= ar($total) ?></td></tr>
</table>
<p><a class="btn" href="commande.php">Passer la commande</a></p>
<?php endif; pied();
