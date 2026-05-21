
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function getConnection() {

  try {
    $dbPath = __DIR__ . '/../database/medlex.db';

    $conn = new PDO("sqlite:" . $dbPath);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("
            CREATE TABLE IF NOT EXISTS pharmacies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                address TEXT,
                phone TEXT,
                email TEXT UNIQUE,
                password_hash TEXT,
                license TEXT
            );
    ");

    $conn->exec("
            CREATE TABLE IF NOT EXISTS medicines (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                generic_name TEXT NOT NULL UNIQUE,
                brand_name TEXT,
                manufacturer TEXT,
                drug_class TEXT,
                therapeutic_class TEXT,
                dosage_form TEXT,
                strength TEXT,
                route_of_administration TEXT,
                indications TEXT,
                image_url TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

    $conn->exec("
    CREATE TABLE IF NOT EXISTS medicine_details (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        medicine_id INTEGER NOT NULL UNIQUE,
        mechanism_of_action TEXT,
        dosage_and_administration TEXT,
        boxed_warning TEXT,
        contraindications TEXT,
        warnings_and_precautions TEXT,
        adverse_reactions TEXT,
        drug_interactions TEXT,
        food_interactions TEXT,
        alcohol_warning TEXT,
        overdose_information TEXT,
        pregnancy_info TEXT,
        breastfeeding_info TEXT,
        pediatric_use TEXT,
        geriatric_use TEXT,
        renal_adjustment TEXT,
        hepatic_adjustment TEXT,
        pharmacodynamics TEXT,
        pharmacokinetics TEXT,
        ingredients TEXT,
        storage_conditions TEXT,
        half_life TEXT,
        clinical_notes TEXT,
        source TEXT,
        last_updated DATETIME,
        FOREIGN KEY (medicine_id) REFERENCES medicines(id)
    );
");

    $conn->exec("
                CREATE TABLE IF NOT EXISTS pharmacy_medicines (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    pharmacy_id INTEGER,
                    medicine_id INTEGER,
                    count INTEGER,
                    price DOUBLE,
                    image_path TEXT,
                    FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id),
                    FOREIGN KEY (medicine_id) REFERENCES medicines(id)
                );
            ");

    $conn->exec("
            CREATE TABLE IF NOT EXISTS medicine_search_stats (
            medicine_id INTEGER,
            search_count INTEGER DEFAULT 0,
            month TEXT NOT NULL,
            PRIMARY KEY (medicine_id, month),
            FOREIGN KEY (medicine_id) REFERENCES medicines(id)
        );
    ");

    $conn->exec("
                CREATE TABLE IF NOT EXISTS reservations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    pharmacy_id INTEGER NOT NULL,
                    reserver_email TEXT NOT NULL,
                    generic_name TEXT NOT NULL,
                    quantity INTEGER NOT NULL,
                    reservation_date DATETIME NOT NULL,
                    expiration_date DATETIME NOT NULL,
                    prescription TEXT,
                    FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id)
                );
            ");
        return $conn;

    } catch (PDOException $e) {
        die($e->getMessage());
    }
}
