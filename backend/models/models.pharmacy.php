<?php

class Pharmacy {
    private $id;
    private $name;
    private $address;
    private $phone;
    private $email;
    private $passwordHash;
    private $license;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash() {
        return $this->passwordHash;
    }

    public function setPasswordHash($passwordHash) {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getLicense() {
        return $this->license;
    }

    public function setLicense($license) {
        $this->license = $license;
        return $this;
    }
}
