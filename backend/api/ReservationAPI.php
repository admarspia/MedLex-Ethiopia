<?php

require_once __DIR__ . '/../controllers/controllers.reservation.php';

class ReservationAPI {
    private $controller;
    
    public function __construct() {
        $this->controller = new ReservationController();
    }
    
    public function create() {
        $this->controller->create();
    }
    
    public function cancel() {
        $this->controller->cancel();
    }
    
    public function notifyExpiringReservations() {
        $this->controller->notifyExpiringReservations();
    }
    
    public function getReservations() {
        $this->controller->getReservations();
    }
}
