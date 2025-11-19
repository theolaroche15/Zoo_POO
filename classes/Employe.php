<?php
require_once __DIR__ . '/Enclos/Enclos.php';

class Employe {

    protected $nom;
    protected $age;
    protected $sexe;

    public function __construct($nom, $age, $sexe) {
        $this->nom = $nom;
        $this->age = $age;
        $this->sexe = $sexe;
    }

    // afficher un enclos et ce qu'il contient
    public function examinerEnclos($enclos) {
        $enclos->afficher();
    }

    // nourrir les animaux qui ne dorment pas
    public function nourrirEnclos($enclos) {
        $count = 0;

        foreach ($enclos->getAnimaux() as $animal) {
            if (!$animal->dort()) {
                $animal->nourrir();
                $count++;
            }
        }

        return $count;
    }
}
