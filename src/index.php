<?php
require 'config.php';
$cats = dbProduit()->query('SELECT * FROM categories ORDER BY nom')->fetchAll();
$cat = (int)($_GET['cat'] ?? 0);
if ($cat) {
    $st = dbProduit()->prepare('SELECT p.*, c.nom AS categorie FROM produits p JOIN categories c ON c.id=p.categorie_id WHERE categorie_id=? ORDER BY p.nom');
    $st->execute([$cat]);
} else {
    $st = dbProduit()->query('SELECT p.*, c.nom AS categorie FROM produits p JOIN categories c ON c.id=p.categorie_id ORDER BY c.nom, p.nom');
}
$produits = $st->fetchAll();
entete('Boutique');
?>
<section class="hero">
  <h1>Tout pour entrer sur le terrain</h1>
  <p>Crampons, protege-tibias, ballons et plus. Livraison sur commande.</p>
</section>

<div class="filtres">
  <a href="index.php" class="<?= !$cat ? 'actif' : '' ?>">Tout</a>
  <?php foreach ($cats as $c): ?>
    <a href="?cat=<?= $c['id'] ?>" class="<?= $cat === (int)$c['id'] ? 'actif' : '' ?>"><?= h($c['nom']) ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$produits): ?><p>Aucun produit dans cette catégorie.</p><?php endif; ?>
<div class="grille">
<?php foreach ($produits as $p): ?>
  <article class="produit">
    <div class="visuel"><?= h(mb_substr($p['categorie'], 0, 1)) ?></div>
    <div class="infos">
      <span class="cat"><?= h($p['categorie']) ?></span>
      <h2><?= h($p['nom']) ?></h2>
      <p><?= h($p['description']) ?></p>
      <div class="bas-carte">
        <strong><?= ar($p['prix']) ?></strong>
        <?php if ($p['stock'] > 0): ?>
          <form method="post" action="panier.php">
            <input type="hidden" name="action" value="ajouter">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button class="btn">Ajouter au panier</button>
          </form>
        <?php else: ?><span class="rupture">Rupture de stock</span><?php endif; ?>
      </div>
    </div>
  </article>
<?php endforeach; ?>
</div>
<?php pied();
