<?php
require_once "Animal.php";

class Aigle extends Animal {

    public function __construct($nom, $poids, $taille, $age) {
        parent::__construct($nom, $poids, $taille, $age, "Aigle");
    }

    public function voler() {
    }
}
