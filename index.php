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
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Het Bureau - Kamer Reservering</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-logo">
                <img src="Layer 2.png" alt="Het Bureau Logo">
            </div>
            <h1 class="hero-title">Kamer Reservering</h1>
            <p class="hero-sub">Reserveer eenvoudig een vergaderruimte bij Het Bureau</p>
            <div class="hero-actions">
                <a href="reserve.php" class="hero-btn primary">Reserveer Nu</a>
                <a href="lijst.php" class="hero-btn secondary">Bekijk Lijst</a>
            </div>
            <div class="hero-info-bar">
                <div class="info-chip">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><?php date_default_timezone_set('Europe/Amsterdam'); echo date("H:i"); ?></span>
                </div>
                <div class="info-chip">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span><?php echo date("d-m-Y"); ?></span>
                </div>
                <div class="info-chip qr-chip">
                    <span>Scan QR</span>
                    <img src="./assets/img/qr.png" alt="QR Code" class="mini-qr">
                </div>
            </div>
        </div>
    </section>

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