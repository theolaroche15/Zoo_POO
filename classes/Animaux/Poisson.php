<?php
require_once "Animal.php";

class Poisson extends Animal {

    public function __construct($nom, $poids, $taille, $age) {
        parent::__construct($nom, $poids, $taille, $age, "Poisson");
    }

    public function nager() {
    }
}
