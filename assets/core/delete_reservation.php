<?php
require_once "require_login.php";
require_user_login(true);

// Database verbinding maken
include_once "connect.php";

// Ontvang de JSON-gegevens
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Ongeldige gegevens ontvangen']);
    exit;
}

$id = $data['id'];

// Bereid de SQL-query voor
$sql = "DELETE FROM reserveringen WHERE reservering_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

// Voer de query uit
$result = $stmt->execute();

// Controleer of het verwijderen is gelukt
if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
