<?php
require_once __DIR__ . '/../models/Reservation.php';

class ReservationController
{
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(405, "Method Not Allowed");
        }

        $userId      = trim($_POST['user_id'] ?? '');
        $pharmacyId  = trim($_POST['pharmacy_id'] ?? '');
        $medicineId  = trim($_POST['medicine_id'] ?? '');
        $quantity    = trim($_POST['quantity'] ?? '');

        $errors = [];

        // ===== Basic Validation =====
        if (!is_numeric($userId) || $userId <= 0) {
            $errors[] = "Invalid user ID.";
        }

        if (!is_numeric($pharmacyId) || $pharmacyId <= 0) {
            $errors[] = "Invalid pharmacy ID.";
        }

        if (!is_numeric($medicineId) || $medicineId <= 0) {
            $errors[] = "Invalid medicine ID.";
        }

        if (!is_numeric($quantity) || $quantity <= 0) {
            $errors[] = "Quantity must be greater than 0.";
        }

        if (!isset($_FILES['medical_paper']) || $_FILES['medical_paper']['error'] !== 0) {
            $errors[] = "Medical prescription is required.";
        }

        if (!empty($errors)) {
            $this->jsonResponse(422, $errors);
        }

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (!in_array($_FILES['medical_paper']['type'], $allowedTypes)) {
            $this->jsonResponse(422, "Prescription must be PDF or image.");
        }

        if ($_FILES['medical_paper']['size'] > 5 * 1024 * 1024) {
            $this->jsonResponse(422, "Prescription file too large (Max 5MB).");
        }

        $uploadDir = __DIR__ . '/../uploads/prescriptions/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES['medical_paper']['name']);
        $filePath = $uploadDir . $fileName;

        move_uploaded_file($_FILES['medical_paper']['tmp_name'], $filePath);

        $reservation = new Reservation();
        $reservation->setUserId($userId);
        $reservation->setPharmacyId($pharmacyId);
        $reservation->setMedicineId($medicineId);
        $reservation->setQuantity($quantity);
        $reservation->setPrescriptionPath("uploads/prescriptions/" . $fileName);
        $reservation->setReservedUntil(date("Y-m-d H:i:s", strtotime("+1 day")));

        //  Check pharmacy stock in DB
        // Save reservation in DB

        $this->jsonResponse(201, "Reservation created successfully. Valid for 24 hours.");
    }

    private function jsonResponse($status, $message)
    {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode([
            "status" => $status,
            "message" => $message
        ]);
        exit;
    }
}
