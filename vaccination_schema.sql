-- =============================
-- VACCINATIONS (Pet Vaccination Records)
-- =============================
CREATE TABLE vaccinations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pet_id BIGINT UNSIGNED NOT NULL,
    vaccine_name VARCHAR(150) NOT NULL,
    batch_number VARCHAR(100),
    dose_number INT DEFAULT 1 COMMENT '1=First dose, 2=Second dose, 3=Booster, etc.',
    vaccination_date DATE NOT NULL,
    next_due_date DATE,
    expiry_date DATE COMMENT 'Expiration date of the vaccine batch',
    veterinarian_id BIGINT UNSIGNED,
    route_of_administration ENUM('intramuscular','subcutaneous','intranasal','oral') DEFAULT 'subcutaneous',
    site_of_injection VARCHAR(100),
    adverse_reactions TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (veterinarian_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_pet (pet_id),
    INDEX idx_vaccination_date (vaccination_date),
    INDEX idx_next_due (next_due_date),
    INDEX idx_expiry (expiry_date)
) ENGINE=InnoDB;
