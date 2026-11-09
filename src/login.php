<?php
require 'config.php';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbAchat();
    if (($_POST['mode'] ?? '') === 'inscription') {
        $nom = trim($_POST['nom']); $email = trim($_POST['email']); $mdp = $_POST['mot_de_passe'];
        if (!$nom || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($mdp) < 6) {
            $erreur = 'Renseignez un nom, un e-mail valide et un mot de passe de 6 caractères minimum.';
        } else {
            $st = $db->prepare('SELECT id FROM clients WHERE email=?'); $st->execute([$email]);
            if ($st->fetch()) { $erreur = 'Cet e-mail a déjà un compte. Connectez-vous.'; }
            else {
                $db->prepare('INSERT INTO clients (nom,email,mot_de_passe,adresse,telephone) VALUES (?,?,?,?,?)')
                   ->execute([$nom, $email, password_hash($mdp, PASSWORD_DEFAULT), trim($_POST['adresse']), trim($_POST['telephone'])]);
                $_SESSION['client'] = ['id' => $db->lastInsertId(), 'nom' => $nom, 'adresse' => trim($_POST['adresse'])];
                header('Location: ' . (!empty($_SESSION['panier']) ? 'commande.php' : 'index.php')); exit;
            }
        }
    } else {
        $st = $db->prepare('SELECT * FROM clients WHERE email=?'); $st->execute([trim($_POST['email'])]);
        $c = $st->fetch();
        if ($c && password_verify($_POST['mot_de_passe'], $c['mot_de_passe'])) {
            $_SESSION['client'] = ['id' => $c['id'], 'nom' => $c['nom'], 'adresse' => $c['adresse']];
            header('Location: ' . (!empty($_SESSION['panier']) ? 'commande.php' : 'index.php')); exit;
        }
        $erreur = 'E-mail ou mot de passe incorrect.';
    }
}
entete('Connexion');
?>
<?php if ($erreur): ?><p class="alerte"><?= h($erreur) ?></p><?php endif; ?>
<div class="deux">
  <section>
    <h1>Se connecter</h1>
    <form method="post" class="form">
      <input type="hidden" name="mode" value="connexion">
      <label>E-mail<input type="email" name="email" required></label>
      <label>Mot de passe<input type="password" name="mot_de_passe" required></label>
      <button class="btn">Se connecter</button>
    </form>
  </section>
  <section>
    <h1>Créer un compte</h1>
    <form method="post" class="form">
      <input type="hidden" name="mode" value="inscription">
      <label>Nom complet<input name="nom" required></label>
      <label>E-mail<input type="email" name="email" required></label>
      <label>Mot de passe (6 caractères min.)<input type="password" name="mot_de_passe" required></label>
      <label>Adresse de livraison<input name="adresse"></label>
      <label>Téléphone<input name="telephone"></label>
      <button class="btn">Créer mon compte</button>
    </form>
  </section>
</div>
<?php pied();
