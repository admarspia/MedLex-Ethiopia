<?php

class MedicineDetail {

    private $id;
    private $medicineId;

    private $mechanismOfAction;
    private $dosageAndAdministration;

    private $boxedWarning;
    private $contraindications;
    private $warningsAndPrecautions;

    private $adverseReactions;

    private $drugInteractions;
    private $foodInteractions;
    private $alcoholWarning;

    private $overdoseInformation;

    private $pregnancyInfo;
    private $breastfeedingInfo;
    private $pediatricUse;
    private $geriatricUse;

    private $renalAdjustment;
    private $hepaticAdjustment;

    private $pharmacodynamics;
    private $pharmacokinetics;

    private $ingredients;
    private $storageConditions;
    private $halfLife;

    private $clinicalNotes;

    private $source;
    private $lastUpdated;


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }


    public function getMedicineId() {
        return $this->medicineId;
    }

    public function setMedicineId($medicineId) {
        $this->medicineId = $medicineId;
        return $this;
    }


    public function getMechanismOfAction() {
        return $this->mechanismOfAction;
    }

    public function setMechanismOfAction($mechanismOfAction) {
        $this->mechanismOfAction = $mechanismOfAction;
        return $this;
    }


    public function getDosageAndAdministration() {
        return $this->dosageAndAdministration;
    }

    public function setDosageAndAdministration($dosageAndAdministration) {
        $this->dosageAndAdministration = $dosageAndAdministration;
        return $this;
    }


    public function getBoxedWarning() {
        return $this->boxedWarning;
    }

    public function setBoxedWarning($boxedWarning) {
        $this->boxedWarning = $boxedWarning;
        return $this;
    }


    public function getContraindications() {
        return $this->contraindications;
    }

    public function setContraindications($contraindications) {
        $this->contraindications = $contraindications;
        return $this;
    }


    public function getWarningsAndPrecautions() {
        return $this->warningsAndPrecautions;
    }

    public function setWarningsAndPrecautions($warningsAndPrecautions) {
        $this->warningsAndPrecautions = $warningsAndPrecautions;
        return $this;
    }


    public function getAdverseReactions() {
        return $this->adverseReactions;
    }

    public function setAdverseReactions($adverseReactions) {
        $this->adverseReactions = $adverseReactions;
        return $this;
    }


    public function getDrugInteractions() {
        return $this->drugInteractions;
    }

    public function setDrugInteractions($drugInteractions) {
        $this->drugInteractions = $drugInteractions;
        return $this;
    }


    public function getFoodInteractions() {
        return $this->foodInteractions;
    }

    public function setFoodInteractions($foodInteractions) {
        $this->foodInteractions = $foodInteractions;
        return $this;
    }


    public function getAlcoholWarning() {
        return $this->alcoholWarning;
    }

    public function setAlcoholWarning($alcoholWarning) {
        $this->alcoholWarning = $alcoholWarning;
        return $this;
    }


    public function getOverdoseInformation() {
        return $this->overdoseInformation;
    }

    public function setOverdoseInformation($overdoseInformation) {
        $this->overdoseInformation = $overdoseInformation;
        return $this;
    }


    public function getPregnancyInfo() {
        return $this->pregnancyInfo;
    }

    public function setPregnancyInfo($pregnancyInfo) {
        $this->pregnancyInfo = $pregnancyInfo;
        return $this;
    }


    public function getBreastfeedingInfo() {
        return $this->breastfeedingInfo;
    }

    public function setBreastfeedingInfo($breastfeedingInfo) {
        $this->breastfeedingInfo = $breastfeedingInfo;
        return $this;
    }


    public function getPediatricUse() {
        return $this->pediatricUse;
    }

    public function setPediatricUse($pediatricUse) {
        $this->pediatricUse = $pediatricUse;
        return $this;
    }


    public function getGeriatricUse() {
        return $this->geriatricUse;
    }

    public function setGeriatricUse($geriatricUse) {
        $this->geriatricUse = $geriatricUse;
        return $this;
    }


    public function getRenalAdjustment() {
        return $this->renalAdjustment;
    }

    public function setRenalAdjustment($renalAdjustment) {
        $this->renalAdjustment = $renalAdjustment;
        return $this;
    }


    public function getHepaticAdjustment() {
        return $this->hepaticAdjustment;
    }

    public function setHepaticAdjustment($hepaticAdjustment) {
        $this->hepaticAdjustment = $hepaticAdjustment;
        return $this;
    }


    public function getPharmacodynamics() {
        return $this->pharmacodynamics;
    }

    public function setPharmacodynamics($pharmacodynamics) {
        $this->pharmacodynamics = $pharmacodynamics;
        return $this;
    }


    public function getPharmacokinetics() {
        return $this->pharmacokinetics;
    }

    public function setPharmacokinetics($pharmacokinetics) {
        $this->pharmacokinetics = $pharmacokinetics;
        return $this;
    }


    public function getIngredients() {
        return $this->ingredients;
    }

    public function setIngredients($ingredients) {
        $this->ingredients = $ingredients;
        return $this;
    }


    public function getStorageConditions() {
        return $this->storageConditions;
    }

    public function setStorageConditions($storageConditions) {
        $this->storageConditions = $storageConditions;
        return $this;
    }


    public function getHalfLife() {
        return $this->halfLife;
    }

    public function setHalfLife($halfLife) {
        $this->halfLife = $halfLife;
        return $this;
    }


    public function getClinicalNotes() {
        return $this->clinicalNotes;
    }

    public function setClinicalNotes($clinicalNotes) {
        $this->clinicalNotes = $clinicalNotes;
        return $this;
    }


    public function getSource() {
        return $this->source;
    }

    public function setSource($source) {
        $this->source = $source;
        return $this;
    }


    public function getLastUpdated() {
        return $this->lastUpdated;
    }

    public function setLastUpdated($lastUpdated) {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }

}
