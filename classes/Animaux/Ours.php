<?php
require_once "Animal.php";

class Ours extends Animal {

    public function __construct($nom, $poids, $taille, $age) {
        parent::__construct($nom, $poids, $taille, $age, "Ours");
    }

    public function grogner() {
    }
}
