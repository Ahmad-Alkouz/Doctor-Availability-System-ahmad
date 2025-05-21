-- Create password_resets table
CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expiry DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email) REFERENCES doctors(email) ON DELETE CASCADE
);

-- Insert existing data
INSERT INTO password_resets (email, token, expiry) VALUES
('ahmad123@gmail.com', '31c42ec8c35d5f0def3d081b1b5702f00f3cbe2419429089bdd9b952b9aec8b8', '2025-05-14 12:10:51'),
('ahmad123@gmail.com', '7f0ac1d4a5d260ce760d5b9de5f1675aefc6935f499d36c509eb83d8d4b26b1f', '2025-05-14 13:15:04'); 