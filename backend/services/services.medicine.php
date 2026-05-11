<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/models.medicines.php';
require_once __DIR__ . '/../models/models.medicineDetails.php';

class MedicineService {

    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    private function incrementSearchCount($medicineId) {

        $month = date('Y-m');

        $stmt = $this->conn->prepare(
            "SELECT *
             FROM medicine_search_stats
             WHERE medicine_id = :id
             AND month = :month"
        );

        $stmt->execute([
            ":id" => $medicineId,
            ":month" => $month
        ]);

        $existing = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($existing) {

            $update = $this->conn->prepare(
                "UPDATE medicine_search_stats
                 SET search_count = search_count + 1
                 WHERE medicine_id = :id
                 AND month = :month"
            );

            $update->execute([
                ":id" => $medicineId,
                ":month" => $month
            ]);

        } else {

            $insert = $this->conn->prepare(
                "INSERT INTO medicine_search_stats
                (
                    medicine_id,
                    search_count,
                    month
                )
                VALUES
                (
                    :id,
                    1,
                    :month
                )"
            );

            $insert->execute([
                ":id" => $medicineId,
                ":month" => $month
            ]);
        }
    }

    private function mapToMedicine($row) {

        $medicine = new Medicine();

        $medicine
            ->setId($row['id'])
            ->setGenericName($row['generic_name'])
            ->setBrandName($row['brand_name'])
            ->setManufacturer($row['manufacturer'])
            ->setDrugClass($row['drug_class'])
            ->setTherapeuticClass($row['therapeutic_class'])
            ->setDosageForm($row['dosage_form'])
            ->setStrength($row['strength'])
            ->setRouteOfAdministration(
                $row['route_of_administration']
            )
            ->setIndications($row['indications'])
            ->setImageUrl($row['image_url'])
            ->setCreatedAt($row['created_at']);

        return $medicine;
    }


    private function mapToMedicineDetail($row) {

        $detail = new MedicineDetail();

        $detail
            ->setId($row['id'])
            ->setMedicineId($row['medicine_id'])
            ->setMechanismOfAction(
                $row['mechanism_of_action']
            )
            ->setDosageAndAdministration(
                $row['dosage_and_administration']
            )
            ->setBoxedWarning(
                $row['boxed_warning']
            )
            ->setContraindications(
                $row['contraindications']
            )
            ->setWarningsAndPrecautions(
                $row['warnings_and_precautions']
            )
            ->setAdverseReactions(
                $row['adverse_reactions']
            )
            ->setDrugInteractions(
                $row['drug_interactions']
            )
            ->setSource(
                $row['source']
            )
            ->setLastUpdated(
                $row['last_updated']
            );

        return $detail;
    }

    public function getMedicineById($medicineId) {

        try {

            $stmt = $this->conn->prepare(
                "SELECT *
                 FROM medicines
                 WHERE id = :id"
            );

            $stmt->execute([
                ":id" => $medicineId
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            $this->incrementSearchCount(
                $medicineId
            );

            return $this->mapToMedicine($row);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getMedicineByGenericName($name) {

        try {

            $stmt = $this->conn->prepare(
                "SELECT *
                 FROM medicines
                 WHERE generic_name = :name"
            );

            $stmt->execute([
                ":name" => $name
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            $this->incrementSearchCount(
                $row['id']
            );

            return $this->mapToMedicine($row);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getMedicineDetail($medicineId) {

        try {

            $stmt = $this->conn->prepare(
                "SELECT *
                 FROM medicine_details
                 WHERE medicine_id = :id"
            );

            $stmt->execute([
                ":id" => $medicineId
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            return $this->mapToMedicineDetail($row);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function addMedicine(
        Medicine $medicine,
        MedicineDetail $detail
    ) {

        try {

            $this->conn->beginTransaction();


            $stmt = $this->conn->prepare(
                "INSERT INTO medicines (
                    generic_name,
                    brand_name,
                    manufacturer,
                    drug_class,
                    therapeutic_class,
                    dosage_form,
                    strength,
                    route_of_administration,
                    indications,
                    image_url
                )
                VALUES (
                    :generic_name,
                    :brand_name,
                    :manufacturer,
                    :drug_class,
                    :therapeutic_class,
                    :dosage_form,
                    :strength,
                    :route,
                    :indications,
                    :image_url
                )"
            );


            $stmt->execute([

                ":generic_name" =>
                    $medicine->getGenericName(),

                ":brand_name" =>
                    $medicine->getBrandName(),

                ":manufacturer" =>
                    $medicine->getManufacturer(),

                ":drug_class" =>
                    $medicine->getDrugClass(),

                ":therapeutic_class" =>
                    $medicine->getTherapeuticClass(),

                ":dosage_form" =>
                    $medicine->getDosageForm(),

                ":strength" =>
                    $medicine->getStrength(),

                ":route" =>
                    $medicine->getRouteOfAdministration(),

                ":indications" =>
                    $medicine->getIndications(),

                ":image_url" =>
                    $medicine->getImageUrl()
            ]);


            $medicineId = $this->conn->lastInsertId();

            $detail->setMedicineId(
                $medicineId
            );


            $detailStmt = $this->conn->prepare(
                "INSERT INTO medicine_details (
                    medicine_id,
                    mechanism_of_action,
                    dosage_and_administration,
                    boxed_warning,
                    contraindications,
                    warnings_and_precautions,
                    adverse_reactions,
                    drug_interactions,
                    source,
                    last_updated
                )
                VALUES (
                    :medicine_id,
                    :mechanism,
                    :dosage,
                    :boxed,
                    :contra,
                    :warnings,
                    :adverse,
                    :interactions,
                    :source,
                    :updated
                )"
            );


            $detailStmt->execute([

                ":medicine_id" =>
                    $detail->getMedicineId(),

                ":mechanism" =>
                    $detail->getMechanismOfAction(),

                ":dosage" =>
                    $detail->getDosageAndAdministration(),

                ":boxed" =>
                    $detail->getBoxedWarning(),

                ":contra" =>
                    $detail->getContraindications(),

                ":warnings" =>
                    $detail->getWarningsAndPrecautions(),

                ":adverse" =>
                    $detail->getAdverseReactions(),

                ":interactions" =>
                    $detail->getDrugInteractions(),

                ":source" =>
                    $detail->getSource(),

                ":updated" =>
                    $detail->getLastUpdated()
            ]);


            $this->conn->commit();

            return true;

        } catch (PDOException $e) {

            $this->conn->rollBack();

            throw $e;
        }
    }

    public function removeLowSearchMedicines() {

        $month = date('Y-m');

        $stmt = $this->conn->prepare(
            "SELECT SUM(search_count) as total
             FROM medicine_search_stats
             WHERE month = :month"
        );

        $stmt->execute([
            ":month" => $month
        ]);

        $total = $stmt->fetch(
            PDO::FETCH_ASSOC
        )['total'];


        if (!$total) {
            return;
        }


        $threshold = $total * 0.10;


        $stmt = $this->conn->prepare(
            "DELETE FROM medicines
             WHERE id IN (
                SELECT medicine_id
                FROM medicine_search_stats
                WHERE month = :month
                AND search_count < :threshold
             )"
        );

        $stmt->execute([
            ":month" => $month,
            ":threshold" => $threshold
        ]);
    }
}
