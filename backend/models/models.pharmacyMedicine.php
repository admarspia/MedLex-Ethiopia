<?php

class PharmacyMedicine {
    private $id;
    private $pharmacyId;
    private $medicineId;
    private $count;
    private $price;
    private $imagePath;

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

    public function getMedicineId() {
        return $this->medicineId;
    }

    public function setMedicineId($medicineId) {
        $this->medicineId = $medicineId;
        return $this;
    }

    public function getCount() {
        return $this->count;
    }

    public function setCount($count) {
        $this->count = $count;
        return $this;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
        return $this;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;
        return $this;
    }
}
