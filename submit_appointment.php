<?php

require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$required = ['client_name', 'address', 'phone', 'car_license', 'car_engine', 'appointment_date', 'mechanic_id'];
foreach ($required as $key) {
    if (empty(trim($_POST[$key] ?? ''))) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }
}

$client_name     = trim($_POST['client_name']);
$address         = trim($_POST['address']);
$phone           = trim($_POST['phone']);
$car_license     = trim($_POST['car_license']);
$car_engine      = trim($_POST['car_engine']);
$appointment_date = trim($_POST['appointment_date']);
$mechanic_id     = (int) $_POST['mechanic_id'];

// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointment_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment date format.']);
    exit;
}

// Validate phone number
if (!preg_match('/^01[3-9][0-9]{8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Phone number must be 11 digits and start with 013-019.']);
    exit;
}

// car license validation
if (!preg_match('/^[A-Za-z]{3,15}\s+(Metro\s+)?[A-Za-z]{1,3}-\d{1,4}$/', $car_license)) {
    echo json_encode(['success' => false, 'message' => 'Car license must look like: Dhaka Metro Ga-1234.']);
    exit;
}

// Car engine: 6–12 digits
if (!preg_match('/^\d{6,12}$/', $car_engine)) {
    echo json_encode(['success' => false, 'message' => 'Car engine number must be 6–12 digits.']);
    exit;
}

$pdo = getDBConnection();

// Check if client already has an appointment
$stmt = $pdo->prepare("
    SELECT id FROM appointments
    WHERE appointment_date = ?
      AND (phone = ? OR car_license = ? OR car_engine = ?)
");
$stmt->execute([$appointment_date, $phone, $car_license, $car_engine]);
if ($stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'You already have an appointment on this date. One appointment per client per day is allowed.'
    ]);
    exit;
}

// Check mechanic capacity
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM appointments WHERE mechanic_id = ? AND appointment_date = ?
");
$stmt->execute([$mechanic_id, $appointment_date]);
$count = (int) $stmt->fetchColumn();
if ($count >= 4) {
    echo json_encode([
        'success' => false,
        'message' => 'This mechanic has no available slots for the selected date. Please choose another mechanic or date.'
    ]);
    exit;
}

// Verify mechanic exists
$stmt = $pdo->prepare("SELECT id FROM mechanics WHERE id = ?");
$stmt->execute([$mechanic_id]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Invalid mechanic selected.']);
    exit;
}

// Insert appointment
$stmt = $pdo->prepare("
    INSERT INTO appointments (client_name, address, phone, car_license, car_engine, appointment_date, mechanic_id)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$client_name, $address, $phone, $car_license, $car_engine, $appointment_date, $mechanic_id]);

echo json_encode([
    'success' => true,
    'message' => 'Your appointment has been confirmed successfully!',
    'id' => $pdo->lastInsertId()
]);
?>
