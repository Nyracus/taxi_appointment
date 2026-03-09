<?php

require_once 'config.php';

$pdo = getDBConnection();

// Handle actions
$updated = false;
$deleted = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $new_date = trim($_POST['appointment_date'] ?? '');
        $new_mechanic_id = (int) ($_POST['mechanic_id'] ?? 0);

        if ($id > 0 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $new_date) && $new_mechanic_id > 0) {
            // Check mechanic exists and has capacity 
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM appointments
                WHERE mechanic_id = ? AND appointment_date = ? AND id != ?
            ");
            $stmt->execute([$new_mechanic_id, $new_date, $id]);
            if ($stmt->fetchColumn() >= 4) {
                $error = 'That mechanic already has 4 appointments on the selected date.';
            } else {
                $stmt = $pdo->prepare("UPDATE appointments SET appointment_date = ?, mechanic_id = ? WHERE id = ?");
                $stmt->execute([$new_date, $new_mechanic_id, $id]);
                $updated = true;
            }
        } else {
            $error = 'Invalid update data.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
            $stmt->execute([$id]);
            $deleted = true;
        } else {
            $error = 'Invalid delete request.';
        }
    }
}

// Fetch all appointments with mechanic 
$stmt = $pdo->query("
    SELECT a.id, a.client_name, a.phone, a.car_license, a.appointment_date, a.mechanic_id,
           m.name AS mechanic_name
    FROM appointments a
    JOIN mechanics m ON m.id = a.mechanic_id
    ORDER BY a.appointment_date ASC, a.id ASC
");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// All mechanicsdropdown
$mechanics = $pdo->query("SELECT id, name FROM mechanics ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Admin – Appointment List';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin – Appointment List</h1>
            <p class="tagline">View and edit appointments</p>
            <nav>
                <a href="index.php">Book Appointment</a>
                <a href="admin.php">Admin Panel</a>
            </nav>
        </header>

        <main>
            <?php if ($updated): ?>
                <p class="form-message show success">Appointment updated successfully.</p>
            <?php endif; ?>
            <?php if ($deleted): ?>
                <p class="form-message show success">Appointment deleted successfully.</p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p class="form-message show error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <section class="table-wrap">
                <?php if (count($appointments) === 0): ?>
                    <p class="empty-state">No appointments yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Client Name</th>
                                <th>Phone</th>
                                <th>Car Registration</th>
                                <th>Appointment Date</th>
                                <th>Mechanic</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $a): ?>
                                <tr id="row-<?php echo (int)$a['id']; ?>">
                                    <td><?php echo htmlspecialchars($a['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($a['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($a['car_license']); ?></td>
                                    <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($a['mechanic_name']); ?></td>
                                    <td>
                                        <form class="admin-actions" method="post" action="admin.php" onsubmit="return confirm('Are you sure you want to update this appointment?');">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                            <input type="date" name="appointment_date" value="<?php echo htmlspecialchars($a['appointment_date']); ?>" required>
                                            <select name="mechanic_id" required>
                                                <?php foreach ($mechanics as $m): ?>
                                                    <option value="<?php echo (int)$m['id']; ?>" <?php echo $m['id'] == $a['mechanic_id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($m['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit">Update</button>
                                        </form>
                                        <form class="admin-actions" method="post" action="admin.php" onsubmit="return confirm('Delete this appointment permanently?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                            <button type="submit" class="cancel">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>CSE 391 – Car Workshop Appointment System (Admin)</p>
        </footer>
    </div>
</body>
</html>
