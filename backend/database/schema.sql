-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS medlex_db;
USE medlex_db;

-- Users table (For pharmacies and potentially admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('pharmacy', 'admin') DEFAULT 'pharmacy',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicines table
CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    generic_name VARCHAR(255) NOT NULL,
    brand_names VARCHAR(500),
    description TEXT,
    usage_guidelines TEXT,
    dosage TEXT,
    side_effects TEXT,
    safety_warnings TEXT,
    drug_interactions TEXT,
    
    -- Localization support (Amharic)
    description_am TEXT,
    usage_guidelines_am TEXT,
    dosage_am TEXT,
    side_effects_am TEXT,
    safety_warnings_am TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pharmacies table
CREATE TABLE IF NOT EXISTS pharmacies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    location_desc TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    contact_phone VARCHAR(50),
    contact_email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Pharmacy_Medicines (Junction table for inventory)
CREATE TABLE IF NOT EXISTS pharmacy_medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pharmacy_id INT NOT NULL,
    medicine_id INT NOT NULL,
    stock_status ENUM('available', 'out_of_stock', 'low_stock') DEFAULT 'available',
    price DECIMAL(10, 2),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pharmacy_id) REFERENCES pharmacies(id) ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    UNIQUE KEY (pharmacy_id, medicine_id)
);

-- Insert dummy data for medicines
INSERT INTO medicines (generic_name, brand_names, description, usage_guidelines, dosage, side_effects, safety_warnings, description_am) VALUES 
('Paracetamol', 'Panadol, Tylenol', 'Used to treat mild to moderate pain and reduce fever.', 'Take with water. Do not take on an empty stomach if you have an ulcer.', 'Adults: 500mg - 1000mg every 4-6 hours. Max 4000mg/day.', 'Nausea, stomach pain, loss of appetite.', 'Do not mix with alcohol. Avoid if you have liver disease.', 'ፓራሲታሞል ህመምን ለማስታገስ እና ትኩሳትን ለመቀነስ ይረዳል።'),
('Amoxicillin', 'Amoxil, Trimox', 'An antibiotic used to treat bacterial infections.', 'Complete the full course as prescribed.', 'Adults: 250mg - 500mg every 8 hours.', 'Diarrhea, nausea, skin rash.', 'Do not take if allergic to penicillin.', 'አሞክሲሲሊን የባክቴሪያ ኢንፌክሽንን ለማከም የሚያገለግል አንቲባዮቲክ ነው።');

-- Insert dummy data for users (Password is 'password123' hashed)
INSERT INTO users (username, password_hash, role) VALUES 
('pharmacy1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pharmacy');

-- Insert dummy data for pharmacies
INSERT INTO pharmacies (user_id, name, location_desc, contact_phone) VALUES 
(1, 'Central Pharmacy', 'Addis Ababa, Bole near Edna Mall', '+251911234567');

-- Insert dummy data for inventory
INSERT INTO pharmacy_medicines (pharmacy_id, medicine_id, stock_status, price) VALUES 
(1, 1, 'available', 15.00),
(1, 2, 'available', 45.00);

