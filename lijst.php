<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/lijst.css">

    <title>Klant-Kamer-Reservering</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-logo">
            <img src="Layer 2.png" alt="HETBUREAU-LOGO-ZWART">
        </div>
        <div class="header-text-wrapper">
            <h1 class="header-text">Beheren</h1>
            <p>als <i>docent naam</i></p>
        </div>
    </header>
    <div class="content">
        <!-- Filter en zoekfunctie -->
        <div class="filter-bar">
            <select id="lokaal-filter">
                <option value="all">Alle</option>
                <option value="W002">W002</option>
                <option value="W002a">W002a</option>
                <option value="W002b">W002b</option>
                <option value="W003">W003</option>
                <option value="W003a">W003a</option>
                <option value="W003b">W003b</option>
            </select>
            <input type="date" id="datum-filter">
            <input type="search" id="zoek-filter" placeholder="Zoeken...">
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Datum</th>
                        <th>Start tijd</th>
                        <th>Eind tijd</th>
                        <th>Lokaal</th>
                        <th>Gepland door</th>
                        <th>Klant</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody id="reserveringen-tabel">
                    <?php
                    // Database verbinding maken
                    include_once "assets/core/connect.php";

                    // Query om alle reserveringen op te halen
                    $sql = "SELECT * FROM reserveringen ORDER BY datum ASC, start_tijd ASC";
                    $result = $conn->query($sql);

                    // Controleer of er resultaten zijn
                    if ($result->num_rows > 0) {
                        // Loop door alle resultaten
                        while ($row = $result->fetch_assoc()) {
                            // Formatteer de datum naar d-m-Y
                            $formatted_date = date("d-m-Y", strtotime($row['datum']));
                            ?>
                            <tr class="table-item" data-id="<?= $row['reservering_id'] ?>">
                                <td class="item-edit">EDIT</td>
                                <td class="item-delete">DELETE</td>
                                <td class="editable" data-field="datum"><?= $formatted_date ?></td>
                                <td class="editable" data-field="start_tijd"><?= date('H:i', strtotime($row['start_tijd'])) ?></td>
                                <td class="editable" data-field="eind_tijd"><?= date('H:i', strtotime($row['eind_tijd'])) ?></td>
                                <td class="editable" data-field="lokaal"><?= $row['lokaal'] ?></td>
                                <td class="editable" data-field="student_nummer"><?= $row['student_nummer'] ?></td> <!-- Het moet in de db opzoeken naar student naam mbv nummer en die ook laten zien -->
                                <td class="editable" data-field="klant"><?= $row['klant'] ?></td>
                                <td class="editable" data-field="type"><?= $row['type'] ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='8'>Geen reserveringen gevonden</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bevestigingsmodal voor verwijderen -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <h2>Weet je het zeker?</h2>
            <p>Wil je deze reservering echt verwijderen?</p>
            <div class="modal-buttons">
                <button id="confirm-delete">Ja, verwijderen</button>
                <button id="cancel-delete">Annuleren</button>
            </div>
        </div>
    </div>

    <script src="assets/js/lijst.js"></script>
</body>

</html>