<?php
class Pharmacy {
    private $id;
    private $name;
    private $address;
    private $phone;
    private $latitude;
    private $longitude;
    private $email;
    private $passwordHash;

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getAddress() { return $this->address; }
    public function getPhone() { return $this->phone; }
    public function getLatitude() { return $this->latitude; }
    public function getLongitude() { return $this->longitude; }
    public function getEmail() { return $this->email; }
    public function getPasswordHash() { return $this->passwordHash; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setAddress($address) { $this->address = $address; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setLatitude($lat) { $this->latitude = $lat; }
    public function setLongitude($lon) { $this->longitude = $lon; }
    public function setEmail($email) { $this->email = $email; }
    public function setPasswordHash($passwordHash) { $this->passwordHash = $passwordHash; }
}
?>
