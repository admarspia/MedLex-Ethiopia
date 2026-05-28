<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../models/models.reservation.php';
require_once __DIR__ . '/../services/services.reservation.php';
require_once __DIR__ . '/../validators/validator.reservation.php';
require_once __DIR__ . '/../helpers/logger.php';

class ReservationController {

    private $service;
    private $validator;

    public function __construct() {
        $this->service = new ReservationService();
        $this->validator = new ReservationValidator();
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(405, "Method Not Allowed");
        }

        try {
            // Handle both JSON and FormData
            $inputData = [];
            if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                $raw = file_get_contents("php://input");
                $inputData = json_decode($raw, true);
            } else {
                $inputData = $_POST;
            }

            $pharmacyId = intval($inputData['pharmacy_id'] ?? $inputData['pharmacyId'] ?? 0);
            if ($pharmacyId <= 0) {
                return $this->response(400, "Pharmacy ID is required and must be valid.");
            }

            // Prepare data for validation
            $validationData = [
                'pharmacy_id' => $pharmacyId,
                'reserver_email' => $inputData['reserver_email'] ?? $inputData['email'] ?? '',
                'generic_name' => $inputData['generic_name'] ?? $inputData['medicine_name'] ?? '',
                'quantity' => $inputData['quantity'] ?? 1
            ];

            $data = $this->validator->validate($validationData);
            
            // Check for prescription file
            $filePath = null;
            if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] === UPLOAD_ERR_OK) {
                $this->validator->validatePrescription($_FILES['prescription']);
                $filePath = $this->savePrescription($_FILES['prescription']);
            }

            $reservation = new Reservation();
            $reservation
                ->setPharmacyId($pharmacyId)
                ->setReserverEmail($data['reserver_email'])
                ->setGenericName($data['generic_name'])
                ->setQuantity($data['quantity'])
                ->setReservationDate(date("Y-m-d H:i:s"))
                ->setExpirationDate(date("Y-m-d H:i:s", strtotime("+24 hours")))
                ->setPrescription($filePath);

            $result = $this->service->create($reservation, $pharmacyId);

            if ($result['status'] === 'error') {
                return $this->response(400, $result['message']);
            }

            // Send email notification
            $emailSent = $this->sendEmail(
                $reservation->getReserverEmail(),
                "Reservation Confirmed - MedLex Ethiopia",
                "Dear Customer,\n\n" .
                "Your reservation for " . $reservation->getGenericName() . " has been confirmed!\n\n" .
                "Reservation Details:\n" .
                "• Medicine: " . $reservation->getGenericName() . "\n" .
                "• Quantity: " . $reservation->getQuantity() . "\n" .
                "• Reservation Date: " . $reservation->getReservationDate() . "\n" .
                "• Expiration Date: " . $reservation->getExpirationDate() . "\n\n" .
                "Please collect your medicine before the expiration date.\n\n" .
                "Thank you for using MedLex Ethiopia!\n" .
                "www.medlex.et"
            );
            
            Helper::logger(201, "Reservation created successfully for: " . $reservation->getReserverEmail());

            $msg = "Reservation created successfully! Your reservation code: #RES" . time();
            if (!$emailSent) {
                $msg .= " (Note: confirmation email could not be sent)";
            }

            return $this->response(201, $msg);
        } catch (Exception $e) {
            Helper::logger(400, $e->getMessage());
            return $this->response(400, $e->getMessage());
        }
    }

    public function cancel() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response(405, "Method Not Allowed");
        }

        // Decode JSON or fallback to standard post
        $raw = file_get_contents("php://input");
        $jsonData = json_decode($raw, true);
        $reservationId = intval($jsonData['id'] ?? $_POST['id'] ?? $_GET['id'] ?? 0);

        if ($reservationId <= 0) {
            return $this->response(422, "Invalid reservation id");
        }

        $result = $this->service->cancel($reservationId);

        if ($result['status'] === 'error') {
            return $this->response(404, $result['message']);
        }

        $reservation = $result['data'];

        $this->sendEmail(
            $reservation['reserver_email'],
            "Reservation Cancelled - MedLex Ethiopia",
            "Dear Customer,\n\n" .
            "Your reservation for " . $reservation['generic_name'] . " has been cancelled as requested.\n\n" .
            "If you did not request this cancellation, please contact us immediately.\n\n" .
            "Thank you for using MedLex Ethiopia!"
        );

        Helper::logger(200, "Reservation cancelled: #" . $reservationId);
        return $this->response(200, "Reservation cancelled successfully");
    }

    public function getReservations() {
        $pharmacyId = $_SESSION["pharmacy_id"] ?? $_GET['pharmacy_id'] ?? null;
        $userEmail = $_GET['email'] ?? null;
        
        if ($pharmacyId) {
            $reservations = $this->service->getReservationsByPharmacy($pharmacyId);
            return $this->response(200, $reservations);
        } elseif ($userEmail) {
            $reservations = $this->service->getReservationsByEmail($userEmail);
            return $this->response(200, $reservations);
        } else {
            return $this->response(401, "Authentication required");
        }
    }

    public function getUserReservations() {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);
        $email = $data['email'] ?? $_GET['email'] ?? null;
        
        if (!$email) {
            return $this->response(400, "Email is required");
        }
        
        $reservations = $this->service->getReservationsByEmail($email);
        return $this->response(200, $reservations);
    }

    public function autoCancelExpired() {
        // This endpoint should be called by cron job every hour
        $result = $this->service->autoCancelExpiredReservations();
        
        if ($result['status'] === 'ok') {
            Helper::logger(200, "Auto-cancelled " . $result['cancelled'] . " expired reservations");
            return $this->response(200, "Auto-cancelled " . $result['cancelled'] . " expired reservations");
        } else {
            return $this->response(500, $result['message']);
        }
    }

    public function notifyExpiringReservations() {
        $from = date("Y-m-d H:i:s", strtotime("+2 hours"));
        $to = date("Y-m-d H:i:s", strtotime("+3 hours"));

        $reservations = $this->service->getExpiringSoon($from, $to);
        
        $notified = 0;
        foreach ($reservations as $reservation) {
            $sent = $this->sendEmail(
                $reservation['reserver_email'],
                "Reminder: Your Reservation Expires Soon - MedLex Ethiopia",
                "Dear Customer,\n\n" .
                "This is a reminder that your reservation for " . $reservation['generic_name'] . " will expire in about 3 hours.\n\n" .
                "• Expiration Time: " . $reservation['expiration_date'] . "\n" .
                "• Pharmacy: " . ($reservation['pharmacy_name'] ?? 'Your pharmacy') . "\n\n" .
                "Please collect your medicine before the expiration time to avoid cancellation.\n\n" .
                "Thank you for choosing MedLex Ethiopia!"
            );
            
            if ($sent) $notified++;
        }
        
        Helper::logger(200, "Sent " . $notified . " expiration reminders");
        return $this->response(200, "Sent " . $notified . " notifications");
    }

    private function savePrescription(array $file) {
        $dir = __DIR__ . '/../uploads/prescriptions/';
        
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid() . "_prescription." . $extension;
        $fullPath = $dir . $name;
        
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return "uploads/prescriptions/" . $name;
        }
        
        return null;
    }

    private function sendEmail($to, $subject, $message) {
        // For development without SMTP, just log it
        if (empty($_ENV["MAIL_USERNAME"]) || empty($_ENV["MAIL_PASSWORD"])) {
            error_log("EMAIL: To: $to, Subject: $subject, Message: $message");
            return true; // Return true for development
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV["MAIL_HOST"] ?? "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV["MAIL_USERNAME"];
            $mail->Password = $_ENV["MAIL_PASSWORD"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV["MAIL_PORT"] ?? 587;

            $mail->setFrom($_ENV["MAIL_USERNAME"], "MedLex Ethiopia");
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer failed: " . $mail->ErrorInfo);
            return false;
        }
    }

    private function response($status, $data) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            "status" => $status,
            "success" => $status >= 200 && $status < 300,
            "data" => $data
        ]);
        exit;
    }
}
