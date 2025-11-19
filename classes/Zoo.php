<?php
require_once __DIR__ . '/Employe.php';

class Zoo {

    protected $nom;
    protected $employe;
    protected $enclos = [];

    public function __construct($nom, $employe) {
        $this->nom = $nom;
        $this->employe = $employe;
    }

    public function ajouterEnclos($enclos) {
        $this->enclos[] = $enclos;
    }

    public function afficherTousEnclos() {
        echo "<h2>Zoo : " . $this->nom . "</h2>";

        foreach ($this->enclos as $enclos) {
            $enclos->afficher();
        }
    }

    public function nombreTotalAnimaux() {
        $total = 0;

        foreach ($this->enclos as $enclos) {
            $total += count($enclos->getAnimaux());
        }

        return $total;
    }
}
