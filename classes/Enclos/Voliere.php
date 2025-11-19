<?php
require_once __DIR__ . '/Enclos.php';

class Voliere extends Enclos {

    protected $hauteur; // hauteur de la voliere

    public function __construct($nom, $hauteur) {
        parent::__construct($nom);
        $this->hauteur = $hauteur;
    }

    // verifie que l'animal peut voler avant de l'ajouter
    public function ajouterAnimal($animal) {
        if (!method_exists($animal, 'voler')) return false;

        return parent::ajouterAnimal($animal);
    }
}
