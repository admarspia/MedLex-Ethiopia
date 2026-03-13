<?php
class MedicineCache {
    private $id;
    private $medicineName;
    private $apiSource;
    private $dataJson;
    private $cachedAt;

    // Getters
    public function getId() { return $this->id; }
    public function getMedicineName() { return $this->medicineName; }
    public function getApiSource() { return $this->apiSource; }
    public function getDataJson() { return $this->dataJson; }
    public function getCachedAt() { return $this->cachedAt; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setMedicineName($name) { $this->medicineName = $name; }
    public function setApiSource($source) { $this->apiSource = $source; }
    public function setDataJson($json) { $this->dataJson = $json; }
    public function setCachedAt($timestamp) { $this->cachedAt = $timestamp; }
}
?>
