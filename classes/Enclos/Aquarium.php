<?php
require_once __DIR__ . '/Enclos.php';

class Aquarium extends Enclos {

    protected $salinite; // niveau de salinite de l'eau

    public function __construct($nom, $salinite = 35) {
        parent::__construct($nom);
        $this->salinite = $salinite;
    }

    // verifie que l'animal peut nager avant de l'ajouter
    public function ajouterAnimal($animal) {
        if (!method_exists($animal, 'nager')) return false;

        return parent::ajouterAnimal($animal);
    }
}
