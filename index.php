<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Choose Date | Car Workshop';
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
                <a href="index.php">Book Appointment</a>
                <a href="admin.php">Admin Panel</a>
            </nav>
        </header>

        <main>
            <?php if (!empty($_GET['msg'])): ?>
                <p class="form-message show error"><?php echo htmlspecialchars($_GET['msg']); ?></p>
            <?php endif; ?>
            <section class="help-section">
                <h2>How it works</h2>
                <ul>
                    <li>Choose an appointment date below, then continue to the booking form.</li>
                    <li>On the next page you will select your preferred mechanic and enter your details.</li>
                    <li>Each mechanic can take up to 4 appointments per day; you’ll see available slots there.</li>
                    <li>You can have only one appointment per day (any mechanic).</li>
                </ul>
            </section>

            <form id="dateForm" class="appointment-form" action="book.php" method="get">
                <h2>Step 1: Choose appointment date</h2>
                <div class="form-group">
                    <label for="date">Appointment Date <span class="required">*</span></label>
                    <input type="date" id="date" name="date" required>
                    <span class="error" id="err_date"></span>
                </div>
                <button type="submit" class="btn btn-primary">Continue to booking</button>
            </form>
        </main>

        <footer>
            <p>CSE 391 – Car Workshop Appointment System</p>
        </footer>
    </div>
    <script>
        (function () {
            var dateInput = document.getElementById('date');
            var today = new Date().toISOString().split('T')[0];
            if (dateInput) dateInput.setAttribute('min', today);
        })();
    </script>
</body>
</html>
