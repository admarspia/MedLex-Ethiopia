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
                "SELECT pm.count, pm.id as pharmacy_medicine_id 
                 FROM pharmacy_medicines pm
                 JOIN medicines m ON pm.medicine_id = m.id
                 WHERE pm.pharmacy_id = :p AND (m.generic_name LIKE :g OR m.brand_name LIKE :b)"
            );

            $gname = $r->getGenericName();
            error_log("Searching for medicine: " . $gname . " in pharmacy: " . $pharmacy_id);

            $stmt->execute([
                ":p" => $pharmacy_id,
                ":g" => "%$gname%",
                ":b" => "%$gname%"
            ]);
            
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stock) {
                return ["status" => "error", "message" => "Medicine not available in this pharmacy"];
            }
            
            if ($stock['count'] < $r->getQuantity()) {
                return ["status" => "error", "message" => "Not enough stock. Available: " . $stock['count']];
            }
            
            // Start transaction
            $this->conn->beginTransaction();
            
            // Create reservation
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
            
            // Deduct from stock
            $updateStock = $this->conn->prepare(
                "UPDATE pharmacy_medicines 
                 SET count = count - :qty 
                 WHERE id = :pm_id"
            );
            
            $updateStock->execute([
                ":qty" => $r->getQuantity(),
                ":pm_id" => $stock['pharmacy_medicine_id']
            ]);
            
            $this->conn->commit();
            
            return ["status" => "ok", "message" => "Reservation created successfully"];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function cancel($id) {
        try {
            // Get reservation details before cancelling
            $stmt = $this->conn->prepare("SELECT * FROM reservations WHERE id = :id");
            $stmt->execute([":id" => $id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$res) {
                return ["status" => "error", "message" => "Reservation not found"];
            }
            
            // Check if expired
            if (strtotime($res['expiration_date']) < time()) {
                return ["status" => "error", "message" => "Reservation has already expired"];
            }
            
            // Start transaction
            $this->conn->beginTransaction();
            
            // Return stock to pharmacy
            $updateStock = $this->conn->prepare(
                "UPDATE pharmacy_medicines 
                 SET count = count + :qty 
                 WHERE pharmacy_id = :p AND medicine_id = (
                     SELECT id FROM medicines WHERE generic_name = :g
                 )"
            );
            
            $updateStock->execute([
                ":qty" => $res['quantity'],
                ":p" => $res['pharmacy_id'],
                ":g" => $res['generic_name']
            ]);
            
            // Delete reservation
            $del = $this->conn->prepare("DELETE FROM reservations WHERE id = :id");
            $del->execute([":id" => $id]);
            
            $this->conn->commit();
            
            return ["status" => "ok", "data" => $res];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function getExpiringSoon($from, $to) {
        $stmt = $this->conn->prepare(
            "SELECT r.*, p.name as pharmacy_name, p.email as pharmacy_email
             FROM reservations r
             JOIN pharmacies p ON r.pharmacy_id = p.id
             WHERE r.expiration_date BETWEEN :f AND :t"
        );
        
        $stmt->execute([":f" => $from, ":t" => $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservationsByPharmacy($pharmacyId) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT r.*, 
                        CASE 
                            WHEN datetime(r.expiration_date) < datetime('now') THEN 'expired'
                            WHEN datetime(r.expiration_date) < datetime('now', '+1 hour') THEN 'expiring_soon'
                            ELSE 'active'
                        END as status
                 FROM reservations r
                 WHERE r.pharmacy_id = :p 
                 ORDER BY r.reservation_date DESC"
            );
            $stmt->execute([":p" => $pharmacyId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting reservations: " . $e->getMessage());
            return [];
        }
    }

    public function getReservationsByEmail($email) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT r.*, p.name as pharmacy_name, p.phone as pharmacy_phone
                 FROM reservations r
                 JOIN pharmacies p ON r.pharmacy_id = p.id
                 WHERE r.reserver_email = :e
                 ORDER BY r.reservation_date DESC"
            );
            $stmt->execute([":e" => $email]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting reservations by email: " . $e->getMessage());
            return [];
        }
    }

    public function autoCancelExpiredReservations() {
        try {
            // Get expired reservations
            $stmt = $this->conn->prepare(
                "SELECT * FROM reservations 
                 WHERE datetime(expiration_date) < datetime('now')"
            );
            $stmt->execute();
            $expired = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $cancelled = 0;
            foreach ($expired as $res) {
                // Return stock
                $updateStock = $this->conn->prepare(
                    "UPDATE pharmacy_medicines 
                     SET count = count + :qty 
                     WHERE pharmacy_id = :p AND medicine_id = (
                         SELECT id FROM medicines WHERE generic_name = :g
                     )"
                );
                
                $updateStock->execute([
                    ":qty" => $res['quantity'],
                    ":p" => $res['pharmacy_id'],
                    ":g" => $res['generic_name']
                ]);
                
                // Delete reservation
                $del = $this->conn->prepare("DELETE FROM reservations WHERE id = :id");
                $del->execute([":id" => $res['id']]);
                $cancelled++;
            }
            
            return ["status" => "ok", "cancelled" => $cancelled];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}

