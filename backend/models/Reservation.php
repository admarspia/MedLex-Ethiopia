<?php
class Reservation {
    private $id;
    private $userId;
    private $pharmacyId;
    private $medicineId;
    private $prescriptionFile;
    private $status; // pending, approved, rejected
    private $createdAt;
    private $expiresAt;

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getPharmacyId() { return $this->pharmacyId; }
    public function getMedicineId() { return $this->medicineId; }
    public function getPrescriptionFile() { return $this->prescriptionFile; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getExpiresAt() { return $this->expiresAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setPharmacyId($pharmacyId) { $this->pharmacyId = $pharmacyId; }
    public function setMedicineId($medicineId) { $this->medicineId = $medicineId; }
    public function setPrescriptionFile($file) { $this->prescriptionFile = $file; }
    public function setStatus($status) { $this->status = $status; }
    public function setCreatedAt($timestamp) { $this->createdAt = $timestamp; }
    public function setExpiresAt($timestamp) { $this->expiresAt = $timestamp; }
}
?>
