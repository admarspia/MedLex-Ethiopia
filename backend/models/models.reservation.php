<?php

class Reservation {

    private $id;
    private $pharmacyId;
    private $reserverEmail;
    private $genericName;
    private $quantity;
    private $reservationDate;
    private $expirationDate;
    private $prescription;



    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }



    public function getPharmacyId() {
        return $this->pharmacyId;
    }

    public function setPharmacyId($pharmacyId) {
        $this->pharmacyId = $pharmacyId;
        return $this;
    }



    public function getReserverEmail() {
        return $this->reserverEmail;
    }

    public function setReserverEmail($reserverEmail) {
        $this->reserverEmail = $reserverEmail;
        return $this;
    }



    public function getGenericName() {
        return $this->genericName;
    }

    public function setGenericName($genericName) {
        $this->genericName = $genericName;
        return $this;
    }



    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }



    public function getReservationDate() {
        return $this->reservationDate;
    }

    public function setReservationDate($reservationDate) {
        $this->reservationDate = $reservationDate;
        return $this;
    }



    public function getExpirationDate() {
        return $this->expirationDate;
    }

    public function setExpirationDate($expirationDate) {
        $this->expirationDate = $expirationDate;
        return $this;
    }



    public function getPrescription() {
        return $this->prescription;
    }

    public function setPrescription($prescription) {
        $this->prescription = $prescription;
        return $this;
    }
}
