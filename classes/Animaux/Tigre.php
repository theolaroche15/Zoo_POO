<?php
require_once "Animal.php";

class Tigre extends Animal {

    public function __construct($nom, $poids, $taille, $age) {
        parent::__construct($nom, $poids, $taille, $age, "Tigre");
    }

    public function vagabonder() {
    }
}
