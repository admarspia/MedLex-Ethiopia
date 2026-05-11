<?php

require_once __DIR__ . '/../controllers/controllers.pharmacy.php';

class PharmacyAPI {
    private $controller;
    
    public function __construct() {
        $this->controller = new PharmacyController();
    }
    
    public function register() {
        $this->controller->register();
    }
    
    public function login() {
        $this->controller->login();
    }
    
    public function addMedicine() {
        $this->controller->addMedicine();
    }
    
    public function removeMedicine() {
        $this->controller->removeMedicine();
    }
    
    public function getMedicines() {
        $this->controller->getMedicines();
    }
    
    public function getSession() {
        $this->controller->getSession();
    }
    
    public function getPharmacies() {
        $this->controller->getPharmacies();
    }
}
