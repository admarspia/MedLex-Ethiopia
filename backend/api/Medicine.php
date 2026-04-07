<?php

require_once __DIR__ . '/../services/MedicineService.php';

class MedicineController
{
    private $medicineService;

    public function __construct()
    {
        $this->medicineService = new MedicineService();
    }

    public function search()
    {
        $name = trim($_GET['name'] ?? '');
        $lang = trim($_GET['lang'] ?? 'en');

        if (empty($name)) {
            $this->jsonResponse(422, "Medicine name is required.");
        }

        $results = $this->medicineService->search($name, $lang);

        $this->jsonResponse(200, $results);
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            $this->jsonResponse(422, "Invalid medicine ID.");
        }

        $medicine = $this->medicineService->findById($id);

        if (!$medicine) {
            $this->jsonResponse(404, "Medicine not found.");
        }

        $this->jsonResponse(200, $medicine);
    }

    private function jsonResponse($status, $data)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
public function searchMedicine($name){
    if($name == null){
        return "No medicine provided";
    }

    return $this->formatResponse($name);
}

private function formatResponse($name){
    return "You searched for: " . $name;
}
