<?php
require_once "require_login.php";
require_user_login(true);
header('Content-Type: application/json; charset=utf-8');

// Database verbinding maken
include_once "connect.php";
include_once "reservation_conflicts.php";

// Ontvang de JSON-gegevens
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data) || !isset($data['id']) || !isset($data['data']) || !is_array($data['data']) || empty($data['data'])) {
    echo json_encode(['success' => false, 'message' => 'Ongeldige gegevens ontvangen']);
    $conn->close();
    exit;
}

$id = (int) $data['id'];
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Ongeldig reserverings-ID']);
    $conn->close();
    exit;
}

$allowed_fields = ['datum', 'start_tijd', 'eind_tijd', 'lokaal', 'student_nummer', 'klant', 'type'];
$updateData = [];

foreach ($data['data'] as $field => $value) {
    if (!in_array($field, $allowed_fields, true)) {
        continue;
    }
    $updateData[$field] = is_string($value) ? trim($value) : $value;
}

if (empty($updateData)) {
    echo json_encode(['success' => false, 'message' => 'Geen toegestane velden om bij te werken']);
    $conn->close();
    exit;
}

try {
    // Lees huidige reservering op zodat conflictcontrole met complete set werkt
    $current_stmt = $conn->prepare("SELECT lokaal, datum, start_tijd, eind_tijd, student_nummer, klant, type FROM reserveringen WHERE reservering_id = ?");
    if (!$current_stmt) {
        throw new RuntimeException($conn->error);
    }
    $current_stmt->bind_param("i", $id);
    $current_stmt->execute();
    $current_result = $current_stmt->get_result();
    $current = $current_result->fetch_assoc();
    $current_stmt->close();

    if (!$current) {
        echo json_encode(['success' => false, 'message' => 'Reservering niet gevonden']);
        $conn->close();
        exit;
    }

    $final = $current;
    foreach ($updateData as $field => $value) {
        $final[$field] = $value;
    }

    if (isset($final['student_nummer']) && !preg_match('/^\d{6}$/', (string) $final['student_nummer'])) {
        echo json_encode(['success' => false, 'message' => 'Studentnummer moet uit 6 cijfers bestaan']);
        $conn->close();
        exit;
    }

    if ($final['eind_tijd'] <= $final['start_tijd']) {
        echo json_encode(['success' => false, 'message' => 'Eindtijd moet na starttijd zijn']);
        $conn->close();
        exit;
    }

    $conflicts = find_conflicting_reservations(
        $conn,
        (string) $final['lokaal'],
        (string) $final['datum'],
        (string) $final['start_tijd'],
        (string) $final['eind_tijd'],
        $id
    );

    if (!empty($conflicts)) {
        $time_blocks = [];
        foreach ($conflicts as $conflict) {
            $time_blocks[] = $conflict['lokaal'] . " (" . date('H:i', strtotime($conflict['start_tijd'])) . " - " . date('H:i', strtotime($conflict['eind_tijd'])) . ")";
        }
        echo json_encode([
            'success' => false,
            'message' => 'Overlappende reservering gevonden: ' . implode(', ', $time_blocks)
        ]);
        $conn->close();
        exit;
    }

    // Bouw veilige UPDATE query met whitelist-velden
    $set_parts = [];
    $params = [];
    $types = '';

    foreach ($updateData as $field => $value) {
        $set_parts[] = "$field = ?";
        if ($field === 'student_nummer') {
            $params[] = (int) $value;
            $types .= 'i';
        } else {
            $params[] = (string) $value;
            $types .= 's';
        }
    }

    $sql = "UPDATE reserveringen SET " . implode(', ', $set_parts) . " WHERE reservering_id = ?";
    $params[] = $id;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException($conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Bijwerken is mislukt']);
    }
} catch (Throwable $exception) {
    echo json_encode(['success' => false, 'message' => $exception->getMessage()]);
}

$conn->close();
?>
