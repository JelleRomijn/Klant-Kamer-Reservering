<?php
require_once 'assets/core/require_login.php';
require_user_login();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/verstuurd.css">

    <title>Verstuurd</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
</head>

<body>
    <?php
    // Controleer of er reserveringsgegevens in de sessie staan
    if (!isset($_SESSION['reservering'])) {
        // Als er geen gegevens zijn, stuur de gebruiker terug naar reserve.php
        header("Location: reserve.php");
        exit();
    }

    // Haal de gegevens uit de sessie
    $reservering = $_SESSION['reservering'];
    $lokaal = $reservering['lokaal'];
    $datum = $reservering['datum'];
    $start_tijd = $reservering['start_tijd'];
    $eind_tijd = $reservering['eind_tijd'];
    $klant = $reservering['klant'];
    $type = $reservering['type'];
    $student_nummer = $reservering['student_nummer'];
    $success = $reservering['success'];
    $error_message = $reservering['error_message'];

    // Datum formatteren naar Nederlands formaat (dd-mm-yyyy)
    $formatted_datum = date("d-m-Y", strtotime($datum));
    ?>

    <div class="header">
        <div class="header-logo">
            <img src="Layer 2.png" alt="HETBUREAU-LOGO-ZWART">
        </div>
        <div class="header-text-wrapper">
            <h1 class="header-text">Verstuurd</h1>
            <p>als <?php echo $student_nummer; ?></p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="status success">
            <h2>Reservering verstuurd</h2>
            <p>Uw reservering is verstuurd naar de beheerder</p>
            <button class="submit-button" onclick="window.location.href='index.php'">TERUG NAAR HOMEPAGE</button>
        </div>
    <?php else: ?>
        <div class="status failed">
            <h2>Reservering mislukt</h2>
            <p>Er is een fout opgetreden: <?php echo $error_message; ?></p>
            <button class="submit-button" onclick="window.location.href='reserve.php'">PROBEER OPNIEUW</button>
        </div>
    <?php endif; ?>

    <div class="data">
        <h3>Studenten Nummer</h3>
        <p><?php echo $student_nummer; ?></p>

        <h3>Lokaal</h3>
        <p><?php echo $lokaal; ?></p>

        <h3>Datum</h3>
        <p><?php echo $formatted_datum; ?></p>

        <h3>Start tijd</h3>
        <p><?php echo $start_tijd; ?></p>

        <h3>Eind tijd</h3>
        <p><?php echo $eind_tijd; ?></p>

        <h3>Klant</h3>
        <p><?php echo $klant; ?></p>

        <h3>Type</h3>
        <p><?php echo $type; ?></p>
    </div>


</body>

</html>
