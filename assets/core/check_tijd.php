<?php
header('Content-Type: application/json; charset=utf-8');

// Database verbinding maken
include_once "connect.php";
include_once "reservation_conflicts.php";

// Ontvang de gegevens van het AJAX-verzoek
$lokaal = trim($_POST['lokaal'] ?? '');
$datum = trim($_POST['datum'] ?? '');
$start_tijd = trim($_POST['start_tijd'] ?? '');
$eind_tijd = trim($_POST['eind_tijd'] ?? '');

if ($lokaal === '' || $datum === '' || $start_tijd === '' || $eind_tijd === '') {
    echo json_encode([
        'available' => false,
        'message' => 'Vul lokaal, datum, starttijd en eindtijd in.',
        'overlaps' => []
    ]);
    $conn->close();
    exit;
}

if ($eind_tijd <= $start_tijd) {
    echo json_encode([
        'available' => false,
        'message' => 'Eindtijd moet na starttijd zijn.',
        'overlaps' => []
    ]);
    $conn->close();
    exit;
}

try {
    $conflicts = find_conflicting_reservations($conn, $lokaal, $datum, $start_tijd, $eind_tijd);

    if (!empty($conflicts)) {
        $overlaps = [];
        foreach ($conflicts as $row) {
            $overlaps[] = [
                'lokaal' => $row['lokaal'],
                'start_tijd' => date('H:i', strtotime($row['start_tijd'])),
                'eind_tijd' => date('H:i', strtotime($row['eind_tijd']))
            ];
        }

        echo json_encode([
            'available' => false,
            'message' => 'Er is al een reservering op dit moment.',
            'overlaps' => $overlaps
        ]);
    } else {
        echo json_encode(['available' => true]);
    }
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'available' => false,
        'message' => 'Beschikbaarheid kon niet gecontroleerd worden.',
        'overlaps' => []
    ]);
}

$conn->close();
?>
