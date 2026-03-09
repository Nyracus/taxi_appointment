<?php

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$date = isset($_GET['date']) ? trim($_GET['date']) : '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['error' => 'Invalid date format. Use YYYY-MM-DD.']);
    exit;
}

try {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("
        SELECT m.id, m.name,
               COALESCE(COUNT(a.id), 0) AS booked
        FROM mechanics m
        LEFT JOIN appointments a ON a.mechanic_id = m.id AND a.appointment_date = ?
        GROUP BY m.id, m.name
    ");
    $stmt->execute([$date]);
    $mechanics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $maxPerMechanic = 4;
    foreach ($mechanics as &$m) {
        $m['booked'] = (int) $m['booked'];
        $m['free_slots'] = max(0, $maxPerMechanic - $m['booked']);
        $m['available'] = $m['free_slots'] > 0;
    }
    unset($m);

    echo json_encode(['mechanics' => $mechanics]);
} catch (Throwable $e) {
    echo json_encode([
        'error' => 'Could not load mechanics. ' . $e->getMessage()
    ]);
}
