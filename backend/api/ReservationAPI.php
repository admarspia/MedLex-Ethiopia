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
    
    public function getReservations() {
        $this->controller->getReservations();
    }
    
    public function getUserReservations() {
        $this->controller->getUserReservations();
    }
    
    public function notifyExpiring() {
        $this->controller->notifyExpiringReservations();
    }
    
    public function autoCancel() {
        $this->controller->autoCancelExpired();
    }
}
