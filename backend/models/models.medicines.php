<?php

class Medicine {

    private $id;
    private $genericName;
    private $brandName;
    private $manufacturer;

    private $drugClass;
    private $therapeuticClass;

    private $dosageForm;
    private $strength;
    private $routeOfAdministration;

    private $indications;

    private $imageUrl;

    private $createdAt;


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }


    public function getGenericName() {
        return $this->genericName;
    }

    public function setGenericName($genericName) {
        $this->genericName = $genericName;
        return $this;
    }


    public function getBrandName() {
        return $this->brandName;
    }

    public function setBrandName($brandName) {
        $this->brandName = $brandName;
        return $this;
    }


    public function getManufacturer() {
        return $this->manufacturer;
    }

    public function setManufacturer($manufacturer) {
        $this->manufacturer = $manufacturer;
        return $this;
    }


    public function getDrugClass() {
        return $this->drugClass;
    }

    public function setDrugClass($drugClass) {
        $this->drugClass = $drugClass;
        return $this;
    }


    public function getTherapeuticClass() {
        return $this->therapeuticClass;
    }

    public function setTherapeuticClass($therapeuticClass) {
        $this->therapeuticClass = $therapeuticClass;
        return $this;
    }


    public function getDosageForm() {
        return $this->dosageForm;
    }

    public function setDosageForm($dosageForm) {
        $this->dosageForm = $dosageForm;
        return $this;
    }


    public function getStrength() {
        return $this->strength;
    }

    public function setStrength($strength) {
        $this->strength = $strength;
        return $this;
    }


    public function getRouteOfAdministration() {
        return $this->routeOfAdministration;
    }

    public function setRouteOfAdministration($route) {
        $this->routeOfAdministration = $route;
        return $this;
    }


    public function getIndications() {
        return $this->indications;
    }

    public function setIndications($indications) {
        $this->indications = $indications;
        return $this;
    }


    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
        return $this;
    }


    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }
}
