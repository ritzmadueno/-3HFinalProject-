<?php
// Database connection parameters
    $host = 'localhost'; // Change this as needed (e.g., for remote servers)
    $username = 'root';  // Your MySQL username
    $password = '';      // Your MySQL password
    $database = 'booking_system'; // Database name

// Create a connection to MySQL
    $conn = new mysqli($host, $username, $password);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

// Select the database to use
$conn->select_db($database);

// 1. Users Table
$sql = "
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'therapist', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully.\n";
} else {
    echo "Error creating Users table: " . $conn->error . "\n";
}

// 2. Services Table
$sql = "
CREATE TABLE IF NOT EXISTS Services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    duration INT NOT NULL, -- Duration in minutes
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Services table created successfully.\n";
} else {
    echo "Error creating Services table: " . $conn->error . "\n";
}

// 3. Appointments Table
$sql = "
CREATE TABLE IF NOT EXISTS Appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- Customer
    therapist_id INT NOT NULL, -- Therapist
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'canceled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (therapist_id) REFERENCES Users(user_id),
    FOREIGN KEY (service_id) REFERENCES Services(service_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Appointments table created successfully.\n";
} else {
    echo "Error creating Appointments table: " . $conn->error . "\n";
}

// 4. Payments Table
$sql = "
CREATE TABLE IF NOT EXISTS Payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'paypal') NOT NULL,
    payment_status ENUM('paid', 'unpaid', 'refunded') NOT NULL DEFAULT 'unpaid',
    transaction_id VARCHAR(100) UNIQUE,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES Appointments(appointment_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Payments table created successfully.\n";
} else {
    echo "Error creating Payments table: " . $conn->error . "\n";
}

// 5. Availability Table
$sql = "
CREATE TABLE IF NOT EXISTS Availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    therapist_id INT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (therapist_id) REFERENCES Users(user_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Availability table created successfully.\n";
} else {
    echo "Error creating Availability table: " . $conn->error . "\n";
}

// 6. Reviews Table
$sql = "
CREATE TABLE IF NOT EXISTS Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    user_id INT NOT NULL, -- Customer
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES Appointments(appointment_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Reviews table created successfully.\n";
} else {
    echo "Error creating Reviews table: " . $conn->error . "\n";
}

// 7. Promotions Table (Optional)
$sql = "
CREATE TABLE IF NOT EXISTS Promotions (
    promo_id INT AUTO_INCREMENT PRIMARY KEY,
    promo_code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    discount_percent DECIMAL(5, 2) NOT NULL CHECK (discount_percent >= 0 AND discount_percent <= 100),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Promotions table created successfully.\n";
} else {
    echo "Error creating Promotions table: " . $conn->error . "\n";
}

// Additional Constraints & Indexes for optimization
$indexes = [
    "CREATE INDEX idx_user_email ON Users(email)",
    "CREATE INDEX idx_service_name ON Services(service_name)",
    "CREATE INDEX idx_appointment_user_id ON Appointments(user_id)",
    "CREATE INDEX idx_appointment_therapist_id ON Appointments(therapist_id)",
    "CREATE INDEX idx_availability_therapist_id ON Availability(therapist_id)",
    "CREATE INDEX idx_promotion_code ON Promotions(promo_code)"
];

foreach ($indexes as $index) {
    if ($conn->query($index) === TRUE) {
        echo "Index created successfully.\n";
    } else {
        echo "Error creating index: " . $conn->error . "\n";
    }
}

// Close the database connection
$conn->close();
?>
