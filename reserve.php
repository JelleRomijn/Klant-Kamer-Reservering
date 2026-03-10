<?php
require_once 'assets/core/require_login.php';
require_user_login();

$logged_in_student_nummer = isset($_SESSION['student_nummer']) ? (string) $_SESSION['student_nummer'] : '';

// Controleer of het formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once "assets/core/connect.php";
    include_once "assets/core/reservation_conflicts.php";

    // Gegevens ophalen en in variabelen stoppen
    $lokaal = trim($_POST['lokaal'] ?? '');
    $datum = trim($_POST['datum'] ?? '');
    $start_tijd = trim($_POST['start_tijd'] ?? '');
    $eind_tijd = trim($_POST['eind_tijd'] ?? '');
    $klant = trim($_POST['klant'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $student_nummer = $logged_in_student_nummer;

    $success = false;
    $error_message = "";

    if ($lokaal === '' || $datum === '' || $start_tijd === '' || $eind_tijd === '' || $klant === '' || $type === '' || $student_nummer === '') {
        $error_message = "Vul alle velden in.";
    } elseif (!preg_match('/^\d{6}$/', $student_nummer)) {
        $error_message = "Studentnummer moet uit 6 cijfers bestaan.";
    } elseif ($eind_tijd <= $start_tijd) {
        $error_message = "Eindtijd moet na starttijd zijn.";
    } else {
        try {
            $conflicts = find_conflicting_reservations($conn, $lokaal, $datum, $start_tijd, $eind_tijd);

            if (!empty($conflicts)) {
                $time_blocks = [];
                foreach ($conflicts as $conflict) {
                    $time_blocks[] = $conflict['lokaal'] . " (" . date('H:i', strtotime($conflict['start_tijd'])) . " - " . date('H:i', strtotime($conflict['eind_tijd'])) . ")";
                }
                $error_message = "Dit lokaal is al gereserveerd tijdens: " . implode(', ', $time_blocks) . ".";
            } else {
                $insert_sql = "INSERT INTO reserveringen (lokaal, datum, start_tijd, eind_tijd, klant, type, student_nummer)
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);

                if (!$stmt) {
                    throw new RuntimeException("Kon reservering niet voorbereiden: " . $conn->error);
                }

                $student_nummer_int = (int) $student_nummer;
                $stmt->bind_param("ssssssi", $lokaal, $datum, $start_tijd, $eind_tijd, $klant, $type, $student_nummer_int);

                if ($stmt->execute()) {
                    $success = true;
                } else {
                    $error_message = $stmt->error;
                }

                $stmt->close();
            }
        } catch (Throwable $exception) {
            $error_message = $exception->getMessage();
        }
    }

    // Sla de gegevens op in de sessie om ze in verstuurd.php te kunnen gebruiken
    $_SESSION['reservering'] = [
        'lokaal' => $lokaal,
        'datum' => $datum,
        'start_tijd' => $start_tijd,
        'eind_tijd' => $eind_tijd,
        'klant' => $klant,
        'type' => $type,
        'student_nummer' => $student_nummer,
        'success' => $success,
        'error_message' => $error_message
    ];

    $conn->close();

    // Redirect naar verstuurd.php
    header("Location: verstuurd.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/reserve.css">


    <title>Reserveren</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">

</head>

<body>
    <div class="header">
        <div class="header-logo">
            <img src="Layer 2.png" alt="HETBUREAU-LOGO-ZWART">
        </div>
        <!-- <div class="header-text-wrapper">
            <h1 class="header-text">Reserveren</h1>
            <p>als <?php echo $student_nummer; ?></p>
        </div> -->
    </div>
    <div class="reserve-info">
        <h2>Reserveren</h2>
        <p>reserveer een lokaal <br> voor een groepsbespreking, klant gesprek, ect</p>
    </div>
    <div class="form">
        <form id="reservationForm" method="post" action="">
            <div class="form-group"> <!-- Input field for student nummer -->
                <input class="input-field" type="text" name="student" value="<?php echo htmlspecialchars($logged_in_student_nummer); ?>" pattern="[0-9]{6}" maxlength="6" readonly required>
                <div class="error-message" id="student-error"></div>
            </div>
            <div class="form-group"> <!-- Input field for Lokaal -->
                <select class="input-field lokaal-select" name="lokaal" required>
                    <option value="" disabled selected>Selecteer een lokaal</option>
                    <option class="lokaal-option">W002a</option>
                    <option class="lokaal-option">W002b</option>
                    <option class="lokaal-option">W003a</option>
                    <option class="lokaal-option">W003b</option>
                </select>
            </div>
            <div class="form-group"> <!-- Input field for Datum -->
                <input class="input-field" type="date" name="datum" required>
            </div>
            <div class="form-group"> <!-- Input field for Start tijd -->
                <input class="input-field" type="time" min="08:00" max="19:00" name="start_tijd" required>
                <div class="error-message" id="start-tijd-error"></div>
            </div>
            <div class="form-group"> <!-- Input field for Eind tijd -->
                <input class="input-field" type="time" min="08:00" max="19:00" name="eind_tijd" required>
                <div class="error-message" id="eind-tijd-error"></div>
            </div>
            <div class="form-group"> <!-- Input field for Klant -->
                <input class="input-field" type="text" name="klant" placeholder="Klant" required>
                <div class="error-message" id="klant-error"></div>
            </div>
            <div class="form-group"> <!-- Input field for Type -->
                <select class="input-field type-select" name="type" required>
                    <option value="" disabled selected>Selecteer een type</option>
                    <option class="type-option">Klant gesprek</option>
                    <option class="type-option">Team vergadering</option>
                    <option class="type-option">Workshop</option>
                </select>
            </div>
            <button class="submit-button" type="submit">VERSTUUR</button>
        </form>
    </div>

</body>
<script src="assets/js/validation.js"></script>
</html>
