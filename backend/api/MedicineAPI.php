<?php

require_once __DIR__ . '/../controllers/controllers.medicine.php';

class MedicineAPI {
    private $controller;
    
    public function __construct() {
        $this->controller = new MedicineController();
    }
    
    public function searchByGenericName($name) {
        $this->controller->searchByGenericName($name);
    }
    
    public function getById($id) {
        $this->controller->getById($id);
    }
    
    public function cleanup() {
        $this->controller->cleanup();
    }
}