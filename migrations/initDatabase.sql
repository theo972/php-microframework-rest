-- users (auth)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB;

-- stores (ressource)
CREATE TABLE IF NOT EXISTS stores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  city VARCHAR(64) NOT NULL,
  address VARCHAR(255) NULL,
  phone VARCHAR(32) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  KEY idx_city (city),
  KEY idx_name (name)
) ENGINE=InnoDB;

INSERT INTO users(email, password_hash, created_at)
VALUES ('admin@example.com', '$2y$10$6m3m1w0W9cBv0j8o6r1kT.4m0N0fCqH3J6o5l2n7vQy8Y8Ckq2x8S', NOW())
ON DUPLICATE KEY UPDATE email = email;
