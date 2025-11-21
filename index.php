<?php
// index.php - interface minimale pour PooZoo
// Place ce fichier à la racine du projet (à côté du dossier classes/)

// Activer l'affichage d'erreurs (utile pour debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Charger les classes nécessaires (vérifie que les chemins existent)
require_once __DIR__ . '/classes/Animaux/Tigre.php';
require_once __DIR__ . '/classes/Animaux/Ours.php';
require_once __DIR__ . '/classes/Animaux/Aigle.php';
require_once __DIR__ . '/classes/Animaux/Poisson.php';

require_once __DIR__ . '/classes/Enclos/Enclos.php';
require_once __DIR__ . '/classes/Enclos/Voliere.php';
require_once __DIR__ . '/classes/Enclos/Aquarium.php';

require_once __DIR__ . '/classes/Employe.php';
require_once __DIR__ . '/classes/Zoo.php';

// --- Seed minimal pour tester (tout en mémoire) ---
$employe = new Employe("Jean", 30, "M");
$zoo = new Zoo("MonPooZoo", $employe);

// créer quelques enclos
$e1 = new Enclos("Territoire Tigres");
$e2 = new Enclos("Territoire Ours");
$voliere = new Voliere("La Voliere", 10);
$aqua = new Aquarium("Petit Aquarium", 35);

// créer quelques animaux
$t1 = new Tigre("Tigrou", 180, 2.1, 5);
$t2 = new Tigre("Shere", 200, 2.3, 7);
$o1 = new Ours("Baloo", 250, 2.5, 10);
$a1 = new Aigle("Aiglou", 6, 0.7, 4);
$p1 = new Poisson("Nemo", 0.2, 0.1, 1);

// remplir les enclos
$e1->ajouterAnimal($t1);
$e1->ajouterAnimal($t2);
$e2->ajouterAnimal($o1);
$voliere->ajouterAnimal($a1);
$aqua->ajouterAnimal($p1);

// ajouter enclos au zoo
$zoo->ajouterEnclos($e1);
$zoo->ajouterEnclos($e2);
$zoo->ajouterEnclos($voliere);
$zoo->ajouterEnclos($aqua);

// messages pour feedback utilisateur
$messages = [];

// traiter POST (actions simples et explicites)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'nettoyer') {
        $i = intval($_POST['enclos_index']);
        $en = $zoo->getEnclos($i);
        if ($en && $employe->nettoyerEnclos($en)) {
            $messages[] = "Enclos nettoyé : " . $en->getNom();
        } else {
            $messages[] = "Impossible de nettoyer (enclos non vide ou erreur).";
        }
    }

    if ($action === 'nourrir') {
        $i = intval($_POST['enclos_index']);
        $en = $zoo->getEnclos($i);
        if ($en) {
            $nb = $employe->nourrirEnclos($en);
            $messages[] = "Animaux nourris dans " . $en->getNom() . " : $nb";
        }
    }

    if ($action === 'ajouter_animal') {
        $i = intval($_POST['enclos_index']);
        $espece = $_POST['espece'] ?? '';
        $nom = trim($_POST['nom'] ?? 'SansNom');
        $poids = floatval($_POST['poids'] ?? 1);
        $taille = floatval($_POST['taille'] ?? 0.1);
        $age = intval($_POST['age'] ?? 1);

        // créer l'instance selon l'espèce (strictement les 4 demandées)
        $animal = null;
        if ($espece === 'Tigre') $animal = new Tigre($nom, $poids, $taille, $age);
        if ($espece === 'Ours')  $animal = new Ours($nom, $poids, $taille, $age);
        if ($espece === 'Aigle') $animal = new Aigle($nom, $poids, $taille, $age);
        if ($espece === 'Poisson') $animal = new Poisson($nom, $poids, $taille, $age);

        $en = $zoo->getEnclos($i);
        if ($animal && $en && $employe->ajouterAnimal($en, $animal)) {
            $messages[] = "Animal ajouté dans " . $en->getNom();
        } else {
            $messages[] = "Impossible d'ajouter l'animal (capacité/espèce).";
        }
    }

    if ($action === 'enlever_animal') {
        $i = intval($_POST['enclos_index']);
        $idx = intval($_POST['animal_index']);
        $en = $zoo->getEnclos($i);
        if ($en) {
            $res = $employe->enleverAnimal($en, $idx);
            if ($res === null) $messages[] = "Aucun animal à cet index.";
            else $messages[] = "Animal enlevé : " . $res->getNom();
        }
    }

    if ($action === 'transferer') {
        $src = intval($_POST['src_enclos_index']);
        $idx = intval($_POST['animal_index']);
        $dst = intval($_POST['dst_enclos_index']);
        $sourceEn = $zoo->getEnclos($src);
        $destEn = $zoo->getEnclos($dst);
        if ($sourceEn && $destEn && $employe->transfererAnimal($sourceEn, $destEn, $idx)) {
            $messages[] = "Transfert réussi.";
        } else {
            $messages[] = "Échec du transfert.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>PooZoo - minimal</title>
</head>
<body>
<h1>PooZoo</h1>

<!-- Afficher messages -->
<?php foreach ($messages as $m) echo '<div>' . htmlspecialchars($m) . '</div>'; ?>

<!-- Liste des enclos et actions -->
<?php
$all = $zoo->getEnclos();
foreach ($all as $i => $en) {
    echo '<div>';
    echo '<h3>' . htmlspecialchars($en->getNom()) . ' (propreté: ' . htmlspecialchars($en->getProprete()) . ')</h3>';

    // animaux
    if ($en->isEmpty()) {
        echo '<p>Aucun animal</p>';
    } else {
        echo '<ul>';
        foreach ($en->getAnimaux() as $idx => $a) {
            echo '<li>[' . $idx . '] ' . htmlspecialchars($a->getNom() . ' (' . $a->getEspece() . ')') . '</li>';
        }
        echo '</ul>';
    }

    // Nettoyer
    echo '<form method="post" style="display:inline-block;margin-right:6px;">';
    echo '<input type="hidden" name="action" value="nettoyer">';
    echo '<input type="hidden" name="enclos_index" value="' . $i . '">';
    echo '<button type="submit">Nettoyer</button>';
    echo '</form>';

    // Nourrir
    echo '<form method="post" style="display:inline-block;margin-right:6px;">';
    echo '<input type="hidden" name="action" value="nourrir">';
    echo '<input type="hidden" name="enclos_index" value="' . $i . '">';
    echo '<button type="submit">Nourrir</button>';
    echo '</form>';

    // Ajouter animal
    echo '<form method="post" style="margin-top:6px;">';
    echo '<input type="hidden" name="action" value="ajouter_animal">';
    echo '<input type="hidden" name="enclos_index" value="' . $i . '">';
    echo 'Espèce: <select name="espece">
            <option value="Tigre">Tigre</option>
            <option value="Ours">Ours</option>
            <option value="Aigle">Aigle</option>
            <option value="Poisson">Poisson</option>
          </select> ';
    echo 'Nom: <input name="nom" size="8" required> ';
    echo 'Poids: <input name="poids" size="4" value="1"> ';
    echo 'Taille: <input name="taille" size="4" value="0.1"> ';
    echo 'Age: <input name="age" size="2" value="1"> ';
    echo '<button type="submit">Ajouter</button>';
    echo '</form>';

    // Enlever animal
    echo '<form method="post" style="margin-top:6px;">';
    echo '<input type="hidden" name="action" value="enlever_animal">';
    echo '<input type="hidden" name="enclos_index" value="' . $i . '">';
    echo 'Index: <input name="animal_index" size="2" required> ';
    echo '<button type="submit">Enlever</button>';
    echo '</form>';

    echo '</div><hr>';
}
?>

<!-- Transfert -->
<h2>Transférer un animal</h2>
<form method="post">
    <input type="hidden" name="action" value="transferer">
    Source:
    <select name="src_enclos_index">
        <?php foreach ($all as $i => $en) echo '<option value="'.$i.'">'.htmlspecialchars($en->getNom()).'</option>'; ?>
    </select>
    Index animal: <input name="animal_index" size="2" required>
    Destination:
    <select name="dst_enclos_index">
        <?php foreach ($all as $i => $en) echo '<option value="'.$i.'">'.htmlspecialchars($en->getNom()).'</option>'; ?>
    </select>
    <button type="submit">Transférer</button>
</form>

<p><em>Note :</em> tout est en mémoire (pas de BDD).</p>
</body>
</html>
