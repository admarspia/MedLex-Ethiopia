<?php

require_once __DIR__ . '/../services/services.medicine.php';
require_once __DIR__ . '/../services/services.pharmacy.php';
require_once __DIR__ . '/../models/models.medicines.php';
require_once __DIR__ . '/../models/models.medicineDetails.php';

class MedicineController {

    private $medicineService;
    private $pharmacyService;

    public function __construct() {
        $this->medicineService = new MedicineService();
        $this->pharmacyService = new PharmacyService();
    }

    public function getByGenericName($genericName) {
        try {
            $medicine = $this->medicineService->getMedicineByGenericName($genericName);
            
            if (!$medicine) {
                $external = $this->fetchMedicine($genericName);
                
                if (!$external) {
                    return ["status" => "error", "message" => "Medicine not found"];
                }
                
                $medicine = $external["medicine"];
                $detail = $external["detail"];
                
                $this->medicineService->addMedicine($medicine, $detail);
                $medicine = $this->medicineService->getMedicineByGenericName($genericName);
            }
            
            $detail = $this->medicineService->getMedicineDetail($medicine->getId());
            $pharmacies = $this->pharmacyService->getPharmaciesByMedicine($medicine->getId());
            
            return [
                "status" => "ok",
                "data" => [
                    "medicine" => $medicine,
                    "detail" => $detail,
                    "pharmacies" => $pharmacies
                ]
            ];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function searchByGenericName($genericName) {
        $result = $this->getByGenericName($genericName);
        
        if ($result["status"] === "error") {
            return $this->response(404, $result["message"]);
        }
        
        return $this->response(200, $result["data"]);
    }

    public function getById($medicineId) {
        try {
            $medicine = $this->medicineService->getMedicineById($medicineId);
            
            if (!$medicine) {
                return $this->response(404, "Medicine not found");
            }
            
            $detail = $this->medicineService->getMedicineDetail($medicineId);
            
            return $this->response(200, ["medicine" => $medicine, "detail" => $detail]);
        } catch (Exception $e) {
            return $this->response(500, $e->getMessage());
        }
    }

    public function cleanup() {
        try {
            $this->medicineService->removeLowSearchMedicines();
            return $this->response(200, "Cleanup completed");
        } catch (Exception $e) {
            return $this->response(500, $e->getMessage());
        }
    }

    private function fetchMedicine($name) {
        $url = "https://api.fda.gov/drug/label.json?search=openfda.generic_name:" . urlencode($name) . "&limit=1";
        $response = @file_get_contents($url);
        
        if (!$response) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['results'][0])) {
            return null;
        }
        
        $row = $data['results'][0];
        
        $medicine = new Medicine();
        $medicine
            ->setGenericName($row['openfda']['generic_name'][0] ?? null)
            ->setBrandName($row['openfda']['brand_name'][0] ?? null)
            ->setManufacturer($row['openfda']['manufacturer_name'][0] ?? null)
            ->setIndications($row['indications_and_usage'][0] ?? null);
        
        $detail = new MedicineDetail();
        $detail
            ->setDosageAndAdministration($row['dosage_and_administration'][0] ?? null)
            ->setWarningsAndPrecautions($row['warnings'][0] ?? null)
            ->setContraindications($row['contraindications'][0] ?? null)
            ->setSource("openFDA")
            ->setLastUpdated(date('Y-m-d H:i:s'));
        
        return ["medicine" => $medicine, "detail" => $detail];
    }

    private function response($status, $data) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode(["status" => $status, "data" => $data]);
        exit;
    }
}
