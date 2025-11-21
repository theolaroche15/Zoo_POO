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

$employe = new Employe("Yves", 25, "M");
$zoo = new Zoo("Mon Zoo", $employe);

$enclos1 = new Enclos("Enclos 1");
$enclos2 = new Enclos("Enclos 2");
$voliere = new Voliere("Voliere", 10);
$aquarium = new Aquarium("Aquarium", 35);

$tigre1 = new Tigre("Tigre", 180, 2.1, 5);
$ours1 = new Ours("Ours", 250, 2.5, 10);
$aigle1 = new Aigle("Aigle", 6, 0.7, 4);
$poisson1 = new Poisson("Poisson", 0.2, 0.1, 1);

$enclos1->ajouterAnimal($tigre1);
$enclos2->ajouterAnimal($ours1);
$voliere->ajouterAnimal($aigle1);
$aquarium->ajouterAnimal($poisson1);

$zoo->ajouterEnclos($enclos1);
$zoo->ajouterEnclos($enclos2);
$zoo->ajouterEnclos($voliere);
$zoo->ajouterEnclos($aquarium);

$messages = [];

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

    if ($action === 'creer_enclos') {
        $nom = trim($_POST['nom_enclos'] ?? '');
        $type = $_POST['type_enclos'] ?? 'Enclos';

        $enclos = null;
        if ($type === 'Enclos') $enclos = new Enclos($nom);
        if ($type === 'Voliere') $enclos = new Voliere($nom, 10); // hauteur par défaut
        if ($type === 'Aquarium') $enclos = new Aquarium($nom, 35); // salinité par défaut

        if ($enclos) {
            $zoo->ajouterEnclos($enclos);
            $messages[] = "Nouvel enclos créé : $nom ($type)";
        } else {
            $messages[] = "Erreur création de l'enclos.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mon Zoo</title>
    <style>
        body { font-family: Arial, sans-serif;
                background-color: #f0f0f5;
                margin: 20px;
        }

        h1, h2, h3 {
            color: #333
        }

        div.enclos {
            border: 2px solid #888;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
        }

        ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0 0 10px 0;
        }

        ul li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            margin-bottom: 6px;
            border-left: 5px solid #4CAF50;
            border-radius: 4px;
            font-weight: 500;
        }
        
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        form {
            margin-top: 5px;
        }
    </style>
</head>
<body>
<h1>Mon Zoo</h1>

<?php foreach ($messages as $m) echo '<div>' . htmlspecialchars($m) . '</div>'; ?>

<?php
$all = $zoo->getEnclos();
foreach ($all as $i => $en) {
    echo '<div class="enclos">';
    echo '<h3>' . htmlspecialchars($en->getNom()) . ' (propreté: ' . htmlspecialchars($en->getProprete()) . ')</h3>';
    echo '</div>';
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
    echo 'Poids: <input name="poids" size="4" value="1">(Kg) / ';
    echo 'Taille: <input name="taille" size="4" value="0.1">(Mètres) / ';
    echo 'Age: <input name="age" size="2" value="1">(Ans) ';
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
<h2>Transférer un animal</h2>
<form method="post">
    <input type="hidden" name="action" value="transferer">
    Enclos actuel:
    <select name="src_enclos_index">
        <?php foreach ($all as $i => $en) echo '<option value="'.$i.'">'.htmlspecialchars($en->getNom()).'</option>'; ?>
    </select>
    Index animal: <input name="animal_index" size="2" required>
    Vers enclos:
    <select name="dst_enclos_index">
        <?php foreach ($all as $i => $en) echo '<option value="'.$i.'">'.htmlspecialchars($en->getNom()).'</option>'; ?>
    </select>
    <button type="submit">Transférer</button>
</form>
<h2>Créer un nouvel enclos</h2>
<form method="post">
    <input type="hidden" name="action" value="creer_enclos">
    Nom de l'enclos : <input name="nom_enclos" required>
    Type :
    <select name="type_enclos">
        <option value="Enclos">Normal</option>
        <option value="Voliere">Volière</option>
        <option value="Aquarium">Aquarium</option>
    </select>
    <button type="submit">Créer</button>
</form>
</body>
</html>
