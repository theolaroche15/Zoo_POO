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

    public function examinerEnclos($enclos) {
        echo $enclos->afficherCaracteristiques();
        echo $enclos->afficherAnimaux();
    }

    public function nettoyerEnclos($enclos) {
        if ($enclos->isVide()) {
            $enclos->setProprete("bonne");
            echo "Enclos nettoyé.<br>";
        } else {
            echo "Impossible nettoyer : enclos non vide.<br>";
        }
    }

    public function nourrirAnimaux($enclos) {
        foreach ($enclos->getAnimaux() as $animal) {
            if (!$animal->isDort()) {
                $animal->manger();
            }
        }
        echo "Animaux nourris.<br>";
    }

    public function ajouterAnimal($enclos, $animal) {
        $enclos->ajouterAnimal($animal);
    }

    public function enleverAnimal($enclos, $index) {
        $enclos->enleverAnimal($index);
    }

    public function transfererAnimal($enclosA, $enclosB, $index) {
        $animal = $enclosA->getAnimal($index);
        if ($animal === null) {
            echo "Animal introuvable.<br>";
            return;
        }

        if ($enclosB->ajouterAnimal($animal)) {
            $enclosA->enleverAnimal($index);
            echo "Transfert effectué.<br>";
        } else {
            echo "Transfert impossible.<br>";
        }
    }

    public function nourrirEnclos($enclos) {
        $count = 0;
        foreach ($enclos->getAnimaux() as $animal) {
            if (!$animal->isDort()) {
                $animal->manger();
                $count++;
            }
        }
        return $count;
    }
}
