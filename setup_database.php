<?php

$host = 'localhost';
$user = 'tuhin';
$pass = '123';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS taxi_driver CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE taxi_driver");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS mechanics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS appointments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_name VARCHAR(100) NOT NULL,
            address VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            car_license VARCHAR(50) NOT NULL,
            car_engine VARCHAR(50) NOT NULL,
            appointment_date DATE NOT NULL,
            mechanic_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE CASCADE,
            INDEX idx_date (appointment_date),
            INDEX idx_mechanic_date (mechanic_id, appointment_date),
            INDEX idx_phone_date (phone, appointment_date)
        )
    ");

    // 5 sample mechanics
    $stmt = $pdo->query("SELECT COUNT(*) FROM mechanics");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("
            INSERT INTO mechanics (name) VALUES
            ('Ahmed Hassan'),
            ('Karim Rahman'),
            ('Fatima Khan'),
            ('Omar Ali'),
            ('Sara Mahmud')
        ");
    }

    echo "<p style='color:green;'>Database and tables created successfully. You can now use the application.</p>";
    echo "<p><a href='index.php'>Go to Appointment Form</a> | <a href='admin.php'>Go to Admin Panel</a></p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
