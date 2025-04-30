CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    date_time DATETIME NOT NULL,
    agenda TEXT NOT NULL,
    notes TEXT,
    action_items TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    assigned_to INT,
    due_date DATE,  
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    recipient INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (recipient) REFERENCES users(id) ON DELETE CASCADE
);

