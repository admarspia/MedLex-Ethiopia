<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/models.reservation.php';

class ReservationService {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function create(Reservation $r, $pharmacy_id) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT count FROM pharmacy_medicines
                 WHERE pharmacy_id = :p AND medicine_id = (
                     SELECT id FROM medicines WHERE generic_name LIKE :g OR brand_name LIKE :b
                 )"
            );

            $gname = $r->getGenericName();
            error_log($gname." ".$pharmacy_id);

            $stmt->execute([
                ":p" => $pharmacy_id,
                ":g" => "%$gname%",
                ":b" => "%$gname%"
            ]);
            
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stock) {
                return ["status" => "error", "message" => "Medicine not available"];
            }
            
            if ($stock['count'] < $r->getQuantity()) {
                return ["status" => "error", "message" => "Not enough stock"];
            }
            
            $stmt = $this->conn->prepare(
                "INSERT INTO reservations (
                    pharmacy_id, reserver_email, generic_name, quantity,
                    reservation_date, expiration_date, prescription
                ) VALUES (
                    :p, :e, :g, :q, :d, :x, :file
                )"
            );
            
            $stmt->execute([
                ":p" => $r->getPharmacyId(),
                ":e" => $r->getReserverEmail(),
                ":g" => $r->getGenericName(),
                ":q" => $r->getQuantity(),
                ":d" => $r->getReservationDate(),
                ":x" => $r->getExpirationDate(),
                ":file" => $r->getPrescription()
            ]);
            
            return ["status" => "ok", "message" => "Reservation created"];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function cancel($id) {
        $stmt = $this->conn->prepare("SELECT * FROM reservations WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$res) {
            return ["status" => "error", "message" => "Not found"];
        }
        
        $del = $this->conn->prepare("DELETE FROM reservations WHERE id = :id");
        $del->execute([":id" => $id]);
        
        return ["status" => "ok", "data" => $res];
    }

    public function getExpiringSoon($from, $to) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM reservations WHERE expiration_date BETWEEN :f AND :t"
        );
        
        $stmt->execute([":f" => $from, ":t" => $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
