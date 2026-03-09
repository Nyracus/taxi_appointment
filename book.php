<?php
require_once __DIR__ . '/config.php';

$date = isset($_GET['date']) ? trim($_GET['date']) : '';
$validDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);

if (!$validDate) {
    header('Location: index.php?msg=Choose a date first.');
    exit;
}


$today = date('Y-m-d');
if ($date < $today) {
    header('Location: index.php?msg=Please choose today or a future date.');
    exit;
}

$pageTitle = 'Book Appointment | Car Workshop';
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
            <h1>Car Workshop Appointment System</h1>
            <p class="tagline">Book your preferred mechanic online</p>
            <nav>
                <a href="index.php">Choose another date</a>
                <a href="admin.php">Admin Panel</a>
            </nav>
        </header>

        <main>
            <p class="hint booking-date">Appointment date: <strong><?php echo htmlspecialchars($date); ?></strong></p>

            <form id="appointmentForm" class="appointment-form" action="submit_appointment.php" method="post">
                <input type="hidden" name="appointment_date" value="<?php echo htmlspecialchars($date); ?>">

                <h2>Step 2: Your details & mechanic</h2>

                <div class="form-group">
                    <label for="client_name">Name <span class="required">*</span></label>
                    <input type="text" id="client_name" name="client_name" required placeholder="Your full name" maxlength="100">
                    <span class="error" id="err_name"></span>
                </div>

                <div class="form-group">
                    <label for="address">Address <span class="required">*</span></label>
                    <input type="text" id="address" name="address" required placeholder="Your address" maxlength="255">
                    <span class="error" id="err_address"></span>
                </div>

                <div class="form-group">
                    <label for="phone">Phone <span class="required">*</span></label>
                    <input type="text" id="phone" name="phone" required placeholder="e.g. 01XXXXXXXXX" maxlength="20">
                    <span class="error" id="err_phone"></span>
                </div>

                <div class="form-group">
                    <label for="car_license">Car License / Registration Number <span class="required">*</span></label>
                    <input type="text" id="car_license" name="car_license" required placeholder="e.g. Dhaka Metro Ga-1234" maxlength="50">
                    <span class="error" id="err_license"></span>
                </div>

                <div class="form-group">
                    <label for="car_engine">Car Engine Number <span class="required">*</span></label>
                    <input type="text" id="car_engine" name="car_engine" required placeholder="Numbers only" maxlength="50">
                    <span class="error" id="err_engine"></span>
                </div>

                <div class="form-group">
                    <label for="mechanic_id">Select Mechanic <span class="required">*</span></label>
                    <select id="mechanic_id" name="mechanic_id" class="mechanic-select">
                        <option value="">— Loading… —</option>
                    </select>
                    <p id="mechanicStatus" class="hint mechanic-status"></p>
                    <span class="error" id="err_mechanic"></span>
                </div>

                <div id="formMessage" class="form-message"></div>

                <button type="submit" class="btn btn-primary">Submit Appointment</button>
            </form>
        </main>

        <footer>
            <p>CSE 391 – Car Workshop Appointment System</p>
        </footer>
    </div>
    <script>
        window.bookingDate = <?php echo json_encode($date); ?>;
    </script>
    <script src="book.js"></script>
</body>
</html>
