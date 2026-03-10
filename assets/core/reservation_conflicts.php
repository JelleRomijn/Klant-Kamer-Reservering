<?php

/**
 * Geeft alle gekoppelde lokalen terug die tegelijk als bezet moeten gelden.
 */
function get_related_lokalen(string $lokaal): array
{
    switch ($lokaal) {
        case 'W002':
            return ['W002', 'W002a', 'W002b'];
        case 'W002a':
        case 'W002b':
            return [$lokaal, 'W002'];
        case 'W003':
            return ['W003', 'W003a', 'W003b'];
        case 'W003a':
        case 'W003b':
            return [$lokaal, 'W003'];
        default:
            return [$lokaal];
    }
}

/**
 * Zoekt overlappende reserveringen voor lokaal, datum en tijd.
 *
 * @throws RuntimeException
 */
function find_conflicting_reservations(
    mysqli $conn,
    string $lokaal,
    string $datum,
    string $start_tijd,
    string $eind_tijd,
    ?int $exclude_reservering_id = null
): array {
    $lokalen = get_related_lokalen($lokaal);
    $placeholders = implode(',', array_fill(0, count($lokalen), '?'));

    $sql = "SELECT reservering_id, lokaal, start_tijd, eind_tijd
            FROM reserveringen
            WHERE lokaal IN ($placeholders)
              AND datum = ?
              AND start_tijd < ?
              AND eind_tijd > ?";

    $params = array_merge($lokalen, [$datum, $eind_tijd, $start_tijd]);
    $types = str_repeat('s', count($params));

    if ($exclude_reservering_id !== null) {
        $sql .= " AND reservering_id <> ?";
        $params[] = $exclude_reservering_id;
        $types .= 'i';
    }

    $sql .= " ORDER BY start_tijd ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException("Kon conflictcontrole niet voorbereiden: " . $conn->error);
    }

    if (!$stmt->bind_param($types, ...$params)) {
        $stmt->close();
        throw new RuntimeException("Kon parameters voor conflictcontrole niet binden: " . $conn->error);
    }

    if (!$stmt->execute()) {
        $stmt->close();
        throw new RuntimeException("Conflictcontrole uitvoeren mislukt: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $overlaps = [];
    while ($row = $result->fetch_assoc()) {
        $overlaps[] = $row;
    }

    $stmt->close();
    return $overlaps;
}
