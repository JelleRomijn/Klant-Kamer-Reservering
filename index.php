<?php
// Database verbinding maken
include_once "assets/core/connect.php";

// Huidige datum in Y-m-d formaat
$today = date("Y-m-d");

// Query om reserveringen van vandaag op te halen, gesorteerd op starttijd (meest recente eerst)

$sql = "SELECT * FROM reserveringen 
        WHERE datum = ?
        ORDER BY start_tijd DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

// Array om de reserveringen op te slaan
$vandaag_reserveringen = [];
while ($row = $result->fetch_assoc()) {
    $vandaag_reserveringen[] = $row;
}

// Sluit de statement
$stmt->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">


    <title>Klant-Kamer-Reservering</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
    <link rel="stylesheet" href="assets\css\index.css">
</head>

<body>
    <!-- Header -->
    <header class="reserve-header">
        <div class="HETBUREAU-LOGO">
            <img src="Layer 2.png" alt="HETBUREAU-LOGO-ZWART">
        </div>
        <div class="time-date-info">
            <div class="label" id="time-label">Tijd:</div>
            <div class="current" id="time-current">
                <?php
                date_default_timezone_set('Europe/Amsterdam');
                echo date("H:i");
                ?>
            </div>
            <div class="label" id="date-label">Datum:</div>
            <div class="current" id="date-current">
                <?php
                echo date("d-m-Y");
                ?>
            </div>
        </div>
        <div class="qr">
            <p>SCAN MIJ!</p>
            <div class="qr-box">
                <img src="./assets/img/qr.png" alt="qr-code">
            </div>
        </div>
    </header>
    <!-- VANDAAG KALENDER -->

    <div class="reserve-vandaag">
        <div class="reserve-vandaag-tekst">
            VANDAAG:
        </div>
        <div class="reserveringen">
            <div class="parent">
                <div class="vandaag-div1">Datum</div>
                <div class="vandaag-div7">Start Tijd</div>
                <div class="vandaag-div13">Eind Tijd</div>
                <div class="vandaag-div19">Lokaal</div>
                <div class="vandaag-div25">Gepland door</div>
                <div class="vandaag-div31">Met wie</div>

                <?php
                // Maximaal 5 reserveringen tonen (rij 2 t/m 6)
                $max_rows = 5;
                $row_count = 0;

                foreach ($vandaag_reserveringen as $index => $reservering) {
                    if ($row_count >= $max_rows)
                        break;

                    // Bereken de rij (2, 3, 4, 5, 6)
                    $row_num = $row_count + 2;

                    // Formatteer de datum naar d-m-Y
                    $formatted_date = date("d-m-Y", strtotime($reservering['datum']));

                    // Toon de gegevens in de juiste cellen
                
                    echo '<div class="vandaag-div' . $row_num . '">Vandaag</div>';
                    echo '<div class="vandaag-div' . ($row_num + 6) . '">' . date('H:i', strtotime($reservering['start_tijd'])) . '</div>';
                    echo '<div class="vandaag-div' . ($row_num + 12) . '">' . date('H:i', strtotime($reservering['eind_tijd'])) . '</div>';
                    echo '<div class="vandaag-div' . ($row_num + 18) . '">' . $reservering['lokaal'] . '</div>';
                    echo '<div class="vandaag-div' . ($row_num + 24) . '">' . $reservering['student_nummer'] . '</div>';
                    echo '<div class="vandaag-div' . ($row_num + 30) . '">' . $reservering['klant'] . '</div>';


                    $row_count++;
                }

                // Vul de rest van de rijen met lege cellen als er minder dan 5 reserveringen zijn
                for ($i = $row_count; $i < $max_rows; $i++) {
                    $row_num = $i + 2;
                    echo '<div class="vandaag-div' . $row_num . '"></div>';
                    echo '<div class="vandaag-div' . ($row_num + 6) . '"></div>';
                    echo '<div class="vandaag-div' . ($row_num + 12) . '"></div>';
                    echo '<div class="vandaag-div' . ($row_num + 18) . '"></div>';
                    echo '<div class="vandaag-div' . ($row_num + 24) . '"></div>';
                    echo '<div class="vandaag-div' . ($row_num + 30) . '"></div>';
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    // Query om toekomstige reserveringen op te halen, gesorteerd op datum en starttijd
    $sql = "SELECT * FROM reserveringen
    WHERE datum > ?
    ORDER BY datum ASC, start_tijd ASC
    LIMIT 5";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();

    // Array om de reserveringen op te slaan
    $toekomstige_reserveringen = [];
    while ($row = $result->fetch_assoc()) {
        $toekomstige_reserveringen[] = $row;
    }

    // Sluit de statement
    $stmt->close();
    $conn->close();
    ?>

    <!-- PLUS KALENDER -->
    <div class="reserve-plus">
        <div class="reserve-plus-tekst">
            RESERVERINGEN
        </div>
        <div class="reserveringen">
            <div class="parent">
                <div class="plus-div1">Datum</div>
                <div class="plus-div7">Start Tijd</div>
                <div class="plus-div13">Eind Tijd</div>
                <div class="plus-div19">Lokaal</div>
                <div class="plus-div25">Gepland door</div>
                <div class="plus-div31">Met wie</div>

                <?php
                // Maximaal 5 reserveringen tonen (rij 2 t/m 6)
                $max_rows = 5;
                $row_count = 0;

                foreach ($toekomstige_reserveringen as $index => $reservering) {
                    if ($row_count >= $max_rows)
                        break;

                    // Bereken de rij (2, 3, 4, 5, 6)
                    $row_num = $row_count + 2;

                    // Bereken het verschil in dagen tussen nu en de reserveringsdatum
                    $reservering_datum = new DateTime($reservering['datum']);
                    $vandaag = new DateTime($today);
                    $verschil = $reservering_datum->diff($vandaag)->days;

                    // Bepaal hoe de datum moet worden weergegeven
                    if ($verschil < 7) {
                        // Voor datums binnen 7 dagen, toon de dagnaam
                        $dagnaam = $reservering_datum->format('l'); // Geeft de Engelse dagnaam
                
                        // Vertaal de Engelse dagnaam naar Nederlands indien gewenst
                        $dagnamen_nl = [
                            'Monday' => 'Maandag',
                            'Tuesday' => 'Dinsdag',
                            'Wednesday' => 'Woensdag',
                            'Thursday' => 'Donderdag',
                            'Friday' => 'Vrijdag',
                            'Saturday' => 'Zaterdag',
                            'Sunday' => 'Zondag'
                        ];

                        $dagnaam_nl = $dagnamen_nl[$dagnaam];

                        // Als het morgen is, toon "Morgen" in plaats van de dagnaam
                        if ($verschil == 1) {
                            $datum_tekst = "Morgen";
                        } else {
                            $datum_tekst = $dagnaam_nl;
                        }
                    } else {
                        // Voor datums verder dan 7 dagen, toon de normale datum
                        $datum_tekst = date("d-m-Y", strtotime($reservering['datum']));
                    }

                    // Toon de gegevens in de juiste cellen
                    echo '<div class="plus-div' . ($row_num) . '">' . $datum_tekst . '</div>';
                    echo '<div class="plus-div' . ($row_num + 6) . '">' . date('H:i', strtotime($reservering['start_tijd'])) . '</div>';
                    echo '<div class="plus-div' . ($row_num + 12) . '">' . date('H:i', strtotime($reservering['eind_tijd'])) . '</div>';
                    echo '<div class="plus-div' . ($row_num + 18) . '">' . $reservering['lokaal'] . '</div>';
                    echo '<div class="plus-div' . ($row_num + 24) . '">' . $reservering['student_nummer'] . '</div>';
                    echo '<div class="plus-div' . ($row_num + 30) . '">' . $reservering['klant'] . '</div>';

                    $row_count++;
                }

                // Vul de rest van de rijen met lege cellen als er minder dan 5 reserveringen zijn
                for ($i = $row_count; $i < $max_rows; $i++) {
                    $row_num = $i + 2;
                    echo '<div class="plus-div' . ($row_num) . '"></div>';
                    echo '<div class="plus-div' . ($row_num + 6) . '"></div>';
                    echo '<div class="plus-div' . ($row_num + 12) . '"></div>';
                    echo '<div class="plus-div' . ($row_num + 18) . '"></div>';
                    echo '<div class="plus-div' . ($row_num + 24) . '"></div>';
                    echo '<div class="plus-div' . ($row_num + 30) . '"></div>';
                }
                ?>

            </div>
        </div>
    </div>


</body>
<!-- <script src="assets/js/bla.js"></script> -->

</html>