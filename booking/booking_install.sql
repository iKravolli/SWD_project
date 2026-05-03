ALTER TABLE workshops
ADD COLUMN capacity INT NOT NULL DEFAULT 20;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    workshop_id INT NOT NULL,
    status ENUM('booked', 'cancelled') DEFAULT 'booked',
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_user_workshop (user_id, workshop_id)
);