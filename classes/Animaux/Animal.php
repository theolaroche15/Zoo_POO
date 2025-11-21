<?php

class Animal {

    protected $espece;
    protected $nom;
    protected $poids;
    protected $taille;
    protected $age;

    protected $faim = false;
    protected $dort = false;
    protected $malade = false;

    public function __construct($nom, $poids, $taille, $age, $espece) {
        $this->nom = $nom;
        $this->poids = $poids;
        $this->taille = $taille;
        $this->age = $age;
        $this->espece = $espece;
    }

    public function afficher() {
        echo $this->nom . " (" . $this->espece . ") - " . $this->poids . "kg, "
         . $this->taille . "m, " . $this->age . " ans";
        }
        
    public function getNom() {
        return $this->nom;
    }

    public function getEspece() {
        return $this->espece;
    }

    public function isDort() {
        return $this->dort;
    }

    public function manger() {
        $this->faim = false;
    }

}

