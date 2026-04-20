<?php

namespace Controllers;

use Database;
use Utils\Response;
use AuthMiddleware;

class PharmacyController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getInventory() {
        $user = AuthMiddleware::handle();
        $email = $user['sub'];

        try {
            // Get pharmacy ID
            $stmt = $this->db->prepare("SELECT id FROM pharmacies WHERE email = ?");
            $stmt->execute([$email]);
            $pharmacyId = $stmt->fetchColumn();

            if (!$pharmacyId) {
                Response::json(404, null, "Pharmacy not found");
            }

            // Get inventory with medicine details
            $stmt = $this->db->prepare("
                SELECT m.id as med_id, m.generic_name, m.brand_name, pm.price, pm.count, pm.image_path
                FROM pharmacy_medicines pm
                JOIN medicines m ON pm.medicine_id = m.id
                WHERE pm.pharmacy_id = ?
            ");
            $stmt->execute([$pharmacyId]);
            $inventory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::json(200, $inventory);
        } catch (\PDOException $e) {
            Response::json(500, null, $e->getMessage());
        }
    }
}
