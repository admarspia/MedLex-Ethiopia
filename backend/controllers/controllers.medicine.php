<?php

require_once __DIR__ . '/../services/services.medicine.php';
require_once __DIR__ . '/../services/services.pharmacy.php';
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../models/models.medicine.php';
require_once __DIR__ . '/../models/models.medicineDetails.php';

class MedicineController {

  private $medicineService;
  private $pharmacyService;

  public function __construct() {

    $this->medicineService =
      new MedicineService();

    $this->pharmacyService =
      new PharmacyService();
  }




  public function medicineToArray(
    $medicine
  ) {

    if (!$medicine) {
      return null;
    }

    return [

      "id" =>
      $medicine->getId(),

      "generic_name" =>
      $medicine->getGenericName(),

      "brand_name" =>
      $medicine->getBrandName(),

      "manufacturer" =>
      $medicine->getManufacturer(),

      "drug_class" =>
      $medicine->getDrugClass(),

      "therapeutic_class" =>
      $medicine->getTherapeuticClass(),

      "dosage_form" =>
      $medicine->getDosageForm(),

      "strength" =>
      $medicine->getStrength(),

      "route_of_administration" =>
      $medicine->getRouteOfAdministration(),

      "indications" =>
      $medicine->getIndications(),

      "image_url" =>
      $medicine->getImageUrl(),

      "created_at" =>
      $medicine->getCreatedAt()
    ];
  }

  public function medicineDetailsToArray(
    $medicineDetails
  ) {

    if (!$medicineDetails) {
      return null;
    }

    return [

      "id" =>
      $medicineDetails->getId(),

      "medicine_id" =>
      $medicineDetails->getMedicineId(),

      "mechanism_of_action" =>
      $medicineDetails->getMechanismOfAction(),

      "dosage_and_administration" =>
      $medicineDetails->getDosageAndAdministration(),

      "boxed_warning" =>
      $medicineDetails->getBoxedWarning(),

      "contraindications" =>
      $medicineDetails->getContraindications(),

      "warnings_and_precautions" =>
      $medicineDetails->getWarningsAndPrecautions(),

      "adverse_reactions" =>
      $medicineDetails->getAdverseReactions(),

      "drug_interactions" =>
      $medicineDetails->getDrugInteractions(),

      "food_interactions" =>
      $medicineDetails->getFoodInteractions(),

      "alcohol_warning" =>
      $medicineDetails->getAlcoholWarning(),

      "overdose_information" =>
      $medicineDetails->getOverdoseInformation(),

      "pregnancy_info" =>
      $medicineDetails->getPregnancyInfo(),

      "breastfeeding_info" =>
      $medicineDetails->getBreastfeedingInfo(),

      "pediatric_use" =>
      $medicineDetails->getPediatricUse(),

      "geriatric_use" =>
      $medicineDetails->getGeriatricUse(),

      "renal_adjustment" =>
      $medicineDetails->getRenalAdjustment(),

      "hepatic_adjustment" =>
      $medicineDetails->getHepaticAdjustment(),

      "pharmacodynamics" =>
      $medicineDetails->getPharmacodynamics(),

      "pharmacokinetics" =>
      $medicineDetails->getPharmacokinetics(),

      "ingredients" =>
      $medicineDetails->getIngredients(),

      "storage_conditions" =>
      $medicineDetails->getStorageConditions(),

      "half_life" =>
      $medicineDetails->getHalfLife(),

      "clinical_notes" =>
      $medicineDetails->getClinicalNotes(),

      "source" =>
      $medicineDetails->getSource(),

      "last_updated" =>
      $medicineDetails->getLastUpdated()
    ];
  }


  public function getByGenericName(
    $genericName
  ) {

    try {

      $genericName =
        trim($genericName);

      if (
        strlen($genericName)
        < 2
      ) {

        return $this->response(
          422,
          "Invalid medicine name"
        );
      }

      error_log($genericName);

      $medicine =
        $this->medicineService
             ->getMedicineByGenericName(
               $genericName
             );
      if ($medicine){

      error_log("medicine found");
      error_log($medicine->getGenericName());
      error_log($medicine->getId());
      }

      if (!$medicine) {

        error_log("medicine not found fetching...");

        $external =
          $this->fetchMedicine(
            $genericName
          );


        if (!$external) {

          return $this->response(
            404,
            "Medicine not found"
          );
        }


        $med = $external['medicine'];
        error_log("fetched medicine");
        error_log($med->getManufacturer());

        $this->medicineService
             ->addMedicine(
               $external['medicine'],
               $external['detail']
             );
        error_log("added to database");

        $medicine =
          $this->medicineService
               ->getMedicineByGenericName(
                 $genericName
               );
      }

      if ($medicine){
      $detail =
        $this->medicineService
             ->getMedicineDetail(
               $medicine
                 ->getId()
             );
      

      $pharmacies =
          $this->pharmacyService
              ->getPharmaciesByMedicineId(
                  $medicine
                      ->getId()
              );

      $medArray = $this->medicineToArray($medicine);
      $medDetailsArray = $this->medicineDetailsToArray($detail);
      }
      return $this->response(
        200,
        [
          "medicine" =>
          $medArray,

          "detail" =>
          $medDetailsArray,
          "pharmacies" =>
          $pharmacies
        ]
      );

    } catch (Exception $e) {

      return $this->response(
        500,
        $e->getMessage()
      );
    }
  }

  public function searchByGenericName(
    $genericName
  ) {

    return
      $this->getByGenericName(
        $genericName
      );
  }

  public function getById(
    $id
  ) {

    try {

      $medicine =
        $this->medicineService
             ->getMedicineById(
               $id
             );

      if (!$medicine) {

        return $this->response(
          404,
          "Medicine not found"
        );
      }




      $detail =
        $this->medicineService
             ->getMedicineDetail(
               $id
             );

      $pharmacies =
        $this->pharmacyService
             ->getPharmaciesByMedicineId(
               $id
             );

      return $this->response(
        200,
        [
          "medicine" =>
          $this->medicineToArray($medicine),

          "detail" =>
          $this->medicineDetailsToArray($detail),

          "pharmacies" =>
          $pharmacies
        ]
      );

    } catch (Exception $e) {
      Helper::logger(500, $e->getMessage());
      return $this->response(
        500,
        $e->getMessage()
      );
    }
  }

  public function getAll() {
    try {
      $medicines = $this->medicineService->getAllMedicines();
      return $this->response(200, $medicines);
    } catch (Exception $e) {
      Helper::logger(500, $e->getMessage());
      return $this->response(500, $e->getMessage());
    }
  }

  public function cleanup() {

    try {

      $this->medicineService
           ->removeLowSearchMedicines();

      return $this->response(
        200,
        "Cleanup completed"
      );

    } catch (Exception $e) {
      Helper::logger(500, $e->getMessage());

      return $this->response(
        500,
        $e->getMessage()
      );
    }
  }

  private function fetchMedicine(
    $name
  ) {

    $url =
      "https://api.fda.gov/drug/label.json?search=openfda.generic_name:"
      . urlencode($name)
      . "&limit=1";

    $response =
      @file_get_contents($url);

    if (!$response) return null;

    $data =
      json_decode(
        $response,
        true
      );

    if (
      !isset(
        $data['results'][0]
      )
    ) return null;

    $row =
      $data['results'][0];

    $medicine =
      new Medicine();

    $medicine
      ->setGenericName($row['openfda']['generic_name'][0] ?? $name)
      ->setBrandName($row['openfda']['brand_name'][0] ?? null)
      ->setManufacturer($row['openfda']['manufacturer_name'][0] ?? null)
      ->setDrugClass($row['openfda']['pharm_class_epc'][0] ?? null)
      ->setTherapeuticClass($row['openfda']['pharm_class_moa'][0] ?? null)
      ->setDosageForm($row['dosage_form'][0] ?? null)
      ->setStrength($row['active_ingredient'][0] ?? null)
      ->setRouteOfAdministration($row['openfda']['route'][0] ?? null)
      ->setIndications($row['indications_and_usage'][0] ?? null)
      ->setImageUrl(null);

    $detail =
      new MedicineDetail();

    $detail
      ->setMechanismOfAction($row['description'][0] ?? null)
      ->setDosageAndAdministration($row['dosage_and_administration'][0] ?? null)
      ->setBoxedWarning($row['boxed_warning'][0] ?? null)
      ->setContraindications($row['contraindications'][0] ?? null)
      ->setWarningsAndPrecautions($row['warnings'][0] ?? null)
      ->setAdverseReactions($row['adverse_reactions'][0] ?? null)
      ->setDrugInteractions($row['drug_interactions'][0] ?? null)
      ->setFoodInteractions($row['food_interactions'][0] ?? null)
      ->setAlcoholWarning($row['alcohol_warning'][0] ?? null)
      ->setOverdoseInformation($row['overdosage'][0] ?? null)
      ->setPregnancyInfo($row['pregnancy_or_breast_feeding'][0] ?? null)
      ->setBreastfeedingInfo($row['nursing_mothers'][0] ?? null)
      ->setPediatricUse($row['pediatric_use'][0] ?? null)
      ->setGeriatricUse($row['geriatric_use'][0] ?? null)
      ->setRenalAdjustment($row['renal_impairment'][0] ?? null)
      ->setHepaticAdjustment($row['hepatic_impairment'][0] ?? null)
      ->setPharmacodynamics($row['pharmacodynamics'][0] ?? null)
      ->setPharmacokinetics($row['pharmacokinetics'][0] ?? null)
      ->setIngredients(
        isset($row['active_ingredient'])
        ? implode(", ", $row['active_ingredient'])
        : null
      )
      ->setStorageConditions($row['storage_and_handling'][0] ?? null)
      ->setHalfLife($row['half_life'][0] ?? null)
      ->setClinicalNotes($row['clinical_pharmacology'][0] ?? null)
      ->setSource("openFDA")
      ->setLastUpdated(date("Y-m-d H:i:s"));

    return [
      "medicine" =>
      $medicine,

      "detail" =>
      $detail
    ];
  }

  private function response(
    $status,
    $data
  ) {

    http_response_code(
      $status
    );

    header(
      "Content-Type: application/json"
    );

    echo json_encode([
      "status" => $status,
      "success" => $status >= 200 && $status < 300,
      "data" => $data
    ]);

    exit;
  }
}
