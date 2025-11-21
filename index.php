<?php
require_once __DIR__ . '/classes/Animaux/Tigre.php';
require_once __DIR__ . '/classes/Animaux/Ours.php';
require_once __DIR__ . '/classes/Animaux/Aigle.php';
require_once __DIR__ . '/classes/Animaux/Poisson.php';
require_once __DIR__ . '/classes/Enclos/Enclos.php';
require_once __DIR__ . '/classes/Enclos/Voliere.php';
require_once __DIR__ . '/classes/Enclos/Aquarium.php';
require_once __DIR__ . '/classes/Employe.php';
require_once __DIR__ . '/classes/Zoo.php';

$employe = new Employe("Jean", 30, "M");
$zoo = new Zoo("Mon Zoo", $employe);

$enclos1 = new Enclos("Territoire Tigres");
$enclos2 = new Enclos("Territoire Ours");
$voliere = new Voliere("Voliere", 10);
$aquarium = new Aquarium("Aquarium", 35);

$tigre = new Tigre("Tigre", 180, 2.1, 5);
$ours = new Ours("Ours", 250, 2.5, 10);
$aigle = new Aigle("Aigle", 6, 0.7, 4);
$poisson = new Poisson("Poisson", 0.2, 0.1, 1);

$enclos1->ajouterAnimal($tigre);
$enclos2->ajouterAnimal($ours);
$voliere->ajouterAnimal($aigle);
$aquarium->ajouterAnimal($poisson);

$zoo->ajouterEnclos($enclos1);
$zoo->ajouterEnclos($enclos2);
$zoo->ajouterEnclos($voliere);
$zoo->ajouterEnclos($aquarium);

$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'examiner') {
        $i = intval($_POST['enclos_index']);
        $messages[] = "Examen de l'enclos " . $zoo->getEnclos()[$i]->getNom();
    }

    if ($action === 'nettoyer') {
        $i = intval($_POST['enclos_index']);
        $en = $zoo->getEnclos()[$i];
        if ($employe->nettoyerEnclos($en)) {
            $messages[] = "Enclos nettoyé : " . $en->getNom();
        } else {
            $messages[] = "Impossible de nettoyer (enclos non vide ou autre) : " . $en->getNom();
        }
    }

    if ($action === 'nourrir') {
        $i = intval($_POST['enclos_index']);
        $en = $zoo->getEnclos()[$i];
        $nb = $employe->nourrirEnclos($en);
        $messages[] = "Animaux nourris dans " . $en->getNom() . " : $nb";
    }

    if ($action === 'ajouter_animal') {
        $i = intval($_POST['enclos_index']);
        $espece = $_POST['espece'] ?? '';
        $nom = trim($_POST['nom'] ?? 'SansNom');
        $poids = floatval($_POST['poids'] ?? 1);
        $taille = floatval($_POST['taille'] ?? 0.1);
        $age = intval($_POST['age'] ?? 1);

        $animal = null;
        if ($espece === 'Tigre') $animal = new Tigre($nom, $poids, $taille, $age);
        if ($espece === 'Ours')  $animal = new Ours($nom, $poids, $taille, $age);
        if ($espece === 'Aigle') $animal = new Aigle($nom, $poids, $taille, $age);
        if ($espece === 'Poisson') $animal = new Poisson($nom, $poids, $taille, $age);

        if ($animal === null) {
            $messages[] = "Espèce inconnue ou non autorisée.";
        } else {
            $en = $zoo->getEnclos()[$i];
            if ($employe->ajouterAnimal($en, $animal)) {
                $messages[] = "Animal ajouté dans " . $en->getNom();
            } else {
                $messages[] = "Impossible d'ajouter l'animal (capacité / espèce incompatible).";
            }
        }
    }

    if ($action === 'enlever_animal') {
        $i = intval($_POST['enclos_index']);
        $idx = intval($_POST['animal_index']);
        $en = $zoo->getEnclos()[$i];
        $res = $employe->enleverAnimal($en, $idx);
        if ($res === null) {
            $messages[] = "Aucun animal trouvé à cet index.";
        } else {
            $messages[] = "Animal enlevé : " . $res->getEspece() . " - " . $res->getNom();
        }
    }

    if ($action === 'transferer') {
        $src = intval($_POST['src_enclos_index']);
        $idx = intval($_POST['animal_index']);
        $dst = intval($_POST['dst_enclos_index']);
        $sourceEn = $zoo->getEnclos()[$src];
        $destEn = $zoo->getEnclos()[$dst];

        if ($employe->transfererAnimal($sourceEn, $destEn, $idx)) {
            $messages[] = "Transfert réussi de l'enclos {$sourceEn->getNom()} vers {$destEn->getNom()}";
        } else {
            $messages[] = "Échec du transfert (vérifier index, capacité, compatibilité d'espèce).";
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>PooZoo</title></head>
<body>
<h1>PooZoo - Interface minimal</h1>

<?php foreach ($messages as $m) echo "<div>" . htmlspecialchars($m) . "</div>"; ?>

<h2>Enclos</h2>

<?php
$all = $zoo->getEnclos();
foreach ($all as $i => $en) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($en->getNom()) . " (propreté: " . htmlspecialchars($en->getProprete()) . ")</h3>";
    if ($en->isEmpty()) {
        echo "<p>Aucun animal</p>";
    } else {
        echo "<ul>";
        foreach ($en->getAnimaux() as $idx => $a) {
            echo "<li>[$idx] " . htmlspecialchars($a->getNom() . " (" . $a->getEspece() . ")") . "</li>";
        }
        echo "</ul>";
    }

    echo '<form method="post" style="display:inline-block;margin-right:6px;">';
    echo '<input type="hidden" name="action" value="nettoyer">';
    echo '<input type="hidden" name="enclos_index" value="' . $i . '">';
    echo '<button type="submit">Nettoyer</button>';
    echo '</form>';

    echo '<form method="post" style="display:inline-block;margin-right:6px;">';
    echo '<input type="hidden" name="action" value="nourrir">';
    echo '<input type="hidden" name="enclos_index" value="' . $i . '">';
    echo '<button type="submit">Nourrir</button>';
    echo '</form>';

    echo '<form method="post" style="margin-top:8px;">';
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
    echo '<button type="submit">Ajouter animal</button>';
    echo '</form>';

    echo '<form method="post" style="margin-top:6px;">';
    echo '<input type="hidden" name="action" value="enlever_animal">';
    echo '<input type="hidden" name="enclos_index" value="' . $i . '">';
    echo 'Index animal: <input name="animal_index" size="2" required> ';
    echo '<button type="submit">Enlever animal</button>';
    echo '</form>';

    echo "</div><hr>";
}
?>

<h2>Transférer un animal</h2>
<form method="post">
    <input type="hidden" name="action" value="transferer">
    Source (index enclos): <select name="src_enclos_index">
        <?php foreach ($all as $i => $en) echo '<option value="'.$i.'">'.htmlspecialchars($en->getNom()).'</option>'; ?>
    </select>
    Animal index: <input name="animal_index" size="2" required>
    Destination (index enclos): <select name="dst_enclos_index">
        <?php foreach ($all as $i => $en) echo '<option value="'.$i.'">'.htmlspecialchars($en->getNom()).'</option>'; ?>
    </select>
    <button type="submit">Transférer</button>
</form>

<p><em>Note :</em> tout est en mémoire (pas de BDD).</p>
</body>
</html>
