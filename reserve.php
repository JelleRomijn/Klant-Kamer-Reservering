<?php
// Controleer of het formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once "assets/core/connect.php";

    // Gegevens ophalen en in variabelen stoppen
    $lokaal = htmlspecialchars($_POST['lokaal']);
    $datum = htmlspecialchars($_POST['datum']);
    $start_tijd = htmlspecialchars($_POST['start_tijd']);
    $eind_tijd = htmlspecialchars($_POST['eind_tijd']);
    $klant = htmlspecialchars($_POST['klant']);
    $type = htmlspecialchars($_POST['type']);
    $student_nummer = htmlspecialchars($_POST['student']); // Zorg ervoor dat het veilig is

    // Gegevens in database invoegen
    $sql = "INSERT INTO reserveringen (lokaal, datum, start_tijd, eind_tijd, klant, type, student_nummer)
    VALUES ('$lokaal', '$datum', '$start_tijd', '$eind_tijd', '$klant', '$type', '$student_nummer')";

    $success = true;
    $error_message = "";

    if ($conn->query($sql) !== TRUE) {
        $success = false;
        $error_message = $conn->error;
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
                <input class="input-field" type="text" name="student" placeholder="Student nummer" pattern="[0-9]{6}" maxlength="6" required>
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