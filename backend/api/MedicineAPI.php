<?php

require_once __DIR__ . '/../services/MedicineService.php';
require_once __DIR__ . '/../services/PharmacyService.php';

class MedicineAPI {

    private $medicineService;
    private $pharmacyService;

    public function __construct() {
        $this->medicineService = new MedicineService();
        $this->pharmacyService = new PharmacyService();
    }

    public function getByGenericName($generic_name) {

        $result = $this->medicineService->search($generic_name);

        if ($result["status"] === "ok") {

            $medicine = $result["data"];

            $pharmacy_ids = $this->medicineService
                ->searchPharmacies($medicine["id"]);

            $pharmacies = [];

            if ($pharmacy_ids["status"] === "ok") {
                foreach ($pharmacy_ids["data"] as $pid) {
                    $p = $this->pharmacyService->findById($pid);
                    if ($p["status"] === "ok") {
                        $pharmacies[] = $p["data"];
                    }
                }
            }

            return ["status" => 200, "data" => [
                "medicine" => $medicine,
                "pharmacies" => $pharmacies
            ]];
        }

        $medicine = $this->fetchMedicine($generic_name);

        if (!$medicine) {
            return $this->response(404, "Medicine not found anywhere");
        }

        $this->medicineService->addMedicine($medicine);

        return ["status" => 200, "data" => [
                "medicine" => $medicine,
                "pharmacies" => $pharmacies
            ]];
    }

    public function searchByGenericName($generic_name) {
      $result = $this->getByGenericName($generic_name);
      return $this->response($result["status"], $result["data"]);
    }

    private function fetchMedicine($name) {
        $url = "https://api.fda.gov/drug/label.json?search=openfda.generic_name:"
            . urlencode($name) . "&limit=1";

        $res = file_get_contents($url);
        if ($res === false) return null;

        $data = json_decode($res, true);

        if (!isset($data['results'][0])) return null;

        $m = $data['results'][0];

        return [
            "generic_name" => $m['openfda']['generic_name'][0] ?? null,
            "brand_name" => $m['openfda']['brand_name'][0] ?? null,
            "purpose" => $m['purpose'][0] ?? null,
            "usage" => $m['indications_and_usage'][0] ?? null,
            "warnings" => $m['warnings'][0] ?? null,
            "stop_use" => $m['stop_use'][0] ?? null,
            "ask_a_doctor" => $m['ask_a_doctor'][0] ?? null,
            "dosage" => $m['dosage_and_administration'][0] ?? null,
            "manufacturer" => $m['openfda']['manufacturer_name'][0] ?? null
        ];
    }

    private function response($status, $data) {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode([
            "status" => $status,
            "data" => $data
        ]);
        exit;
    }
}



