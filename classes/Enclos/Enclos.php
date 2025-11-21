<?php 
require_once __DIR__ . '/../Animaux/Animal.php';

class Enclos {

    protected $nom;
    protected $proprete = "bonne";
    protected $animaux = [];
    protected $capacite = 6;

    public function __construct($nom) {
        $this->nom = $nom;
    }

    public function ajouterAnimal($animal) {

        if (count($this->animaux) >= $this->capacite) return false;

        if (!empty($this->animaux)) {
            $especeExistante = $this->animaux[0]->getEspece();
            if ($animal->getEspece() !== $especeExistante) return false;
        }

        $this->animaux[] = $animal;
        return true;
    }

    public function enleverAnimal($index) {
        if (!isset($this->animaux[$index])) return null;

        $animal = $this->animaux[$index];

        array_splice($this->animaux, $index, 1);

        return $animal;
    }

    public function getAnimaux() {
        return $this->animaux;
    }

    public function afficher() {
        echo "<b>Enclos : </b>" . $this->nom . " (propreté : " . $this->proprete . ")<br>";

        if (empty($this->animaux)) {
            echo " - Aucun animal<br><br>";
            return;
        }

        foreach ($this->animaux as $animal) {
            $animal->afficher();
        }

        echo "<br>";
    }
    
    public function getNom() {
        return $this->nom;
    }

    public function getProprete() {
        return $this->proprete;
    }

    public function setProprete($p) {
        $this->proprete = $p;
    }

    public function getNombreAnimaux() {
        return count($this->animaux);
    }

    public function isEmpty() {
        return $this->getNombreAnimaux() === 0;
    }

    public function isEmptyPublic() {
        return $this->isEmpty();
    }

    public function isVide() {
        return $this->isEmpty();
    }

    public function getAnimal($index) {
        return $this->animaux[$index] ?? null;
    }

    public function afficherCaracteristiques() {
        return "Nom : $this->nom, Propreté : $this->proprete, Nombre animaux : " . $this->getNombreAnimaux() . "<br>";
    }

    public function afficherAnimaux() {
        if (empty($this->animaux)) return "Aucun animal.<br>";
        $txt = "";
        foreach ($this->animaux as $i => $a) {
            $txt .= "[$i] " . $a->getNom() . " (" . $a->getEspece() . ")<br>";
        }
        return $txt;
    }
}
