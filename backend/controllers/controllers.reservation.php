<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../models/models.reservation.php';
require_once __DIR__ . '/../services/services.reservation.php';
require_once __DIR__ . '/../validators/validator.reservation.php';

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
      $pharmacyId = $_SESSION['pharmacy_id'];
      if (!$pharmacyId){
        return $this->response(401, 'unautorized');
      }

      $data = $this->validator->validate($_POST);
      $this->validator->validatePrescription($_FILES['prescription'] ?? []);

      $filePath = $this->savePrescription($_FILES['prescription']);

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

      $this->sendEmail(
        $reservation->getReserverEmail(),
        "Reservation Created",
        "Your reservation for " . $reservation->getGenericName() . " was created successfully.\nIt expires at: " . $reservation->getExpirationDate()
      );

      return $this->response(201, "Reservation created successfully");
    } catch (Exception $e) {
      return $this->response(400, $e->getMessage());
    }
  }

  public function cancel() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->response(405, "Method Not Allowed");
    }

    $reservationId = intval($_POST['id'] ?? 0);

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
      "Reservation Cancelled",
      "Your reservation for " . $reservation['generic_name'] . " has been cancelled."
    );

    return $this->response(200, "Reservation cancelled");
  }

  private function sendEmail($to, $subject, $message) {
    
    $mail = new PHPMailer(true);

    try {

      $mail->isSMTP();

      $mail->Host =
        "smtp.gmail.com";

      $mail->SMTPAuth =
        true;

      $mail->Username =
        $_ENV["MAIL_USERNAME"];

      $mail->Password =
        $_ENV["MAIL_PASSWORD"];

      $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;

      $mail->Port =
        587;

      $mail->setFrom(
        $_ENV["MAIL_USERNAME"],
        "MedLex"
      );

      $mail->addAddress(
        $to
      );

      $mail->Subject =
        $subject;

      $mail->Body =
        $message;

      $mail->send();

      return true;

    } catch (Exception $e) {

      error_log(
        $mail->ErrorInfo
      );

      return false;
    }
  }

  public function notifyExpiringReservations() {
    $from = date("Y-m-d H:i:s", strtotime("+2 hours"));
    $to = date("Y-m-d H:i:s", strtotime("+3 hours"));

    $reservations = $this->service->getExpiringSoon($from, $to);
        
        foreach ($reservations as $reservation) {
            $this->sendEmail(
                $reservation['reserver_email'],
                "Reservation Expiring Soon",
                "Your reservation for " . $reservation['generic_name'] . " will expire in about 3 hours.\nExpiration time: " . $reservation['expiration_date']
            );
        }
        
        return $this->response(200, "Notifications sent");
    }

    private function savePrescription(array $file) {
        $dir = __DIR__ . '/../uploads/prescriptions/';
        
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $name = uniqid() . "_" . basename($file['name']);
        $fullPath = $dir . $name;
        
        move_uploaded_file($file['tmp_name'], $fullPath);

        return "uploads/prescriptions/" . $name;
    }

    private function response($status, $data) {
      http_response_code($status);
      header('Content-Type: application/json');
      echo json_encode(["status" => $status, "data" => $data]);
      exit;
    }
}
