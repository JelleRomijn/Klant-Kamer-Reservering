<?php
require_once "require_login.php";
require_user_login(true);

// Database verbinding maken
include_once "connect.php";

// Ontvang de JSON-gegevens
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id']) || !isset($data['data']) || empty($data['data'])) {
    echo json_encode(['success' => false, 'message' => 'Ongeldige gegevens ontvangen']);
    exit;
}

$id = $data['id'];
$updateData = $data['data'];

// Bouw de SQL-query
$sql = "UPDATE reserveringen SET ";
$params = [];
$types = "";

// Voeg elke veld toe aan de query
foreach ($updateData as $field => $value) {
    $sql .= "$field = ?, ";
    $params[] = $value;
    $types .= "s"; // Alle waarden worden als strings behandeld
}

// Verwijder de laatste komma en spatie
$sql = rtrim($sql, ", ");

// Voeg de WHERE-clausule toe
$sql .= " WHERE reservering_id = ?";
$params[] = $id;
$types .= "i"; // ID is een integer

// Bereid de statement voor
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

// Voer de query uit
$result = $stmt->execute();

// Controleer of de update is gelukt
if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
