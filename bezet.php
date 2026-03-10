<?php
include_once "assets/core/connect.php";

date_default_timezone_set('Europe/Amsterdam');

$today = date("Y-m-d");
$now = date("H:i:s");

$active_sql = "SELECT lokaal, klant, type, start_tijd, eind_tijd, student_nummer
               FROM reserveringen
               WHERE datum = ?
                 AND start_tijd <= ?
                 AND eind_tijd > ?
               ORDER BY eind_tijd ASC";

$active_stmt = $conn->prepare($active_sql);
$bezette_lokalen = [];

if ($active_stmt) {
    $active_stmt->bind_param("sss", $today, $now, $now);
    $active_stmt->execute();
    $active_result = $active_stmt->get_result();

    while ($row = $active_result->fetch_assoc()) {
        $bezette_lokalen[] = $row;
    }

    $active_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Nu Bezet - Het Bureau</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bezet.css">
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
</head>
<body>
    <main class="bezet-page">
        <section class="bezet-header">
            <img src="Layer 2.png" alt="Het Bureau Logo" class="bezet-logo">
            <div>
                <h1>Nu Bezet</h1>
                <p><?php echo date("d-m-Y"); ?> | <?php echo date("H:i"); ?> | Vernieuwt elke 30s</p>
            </div>
        </section>

        <section class="bezet-grid" aria-label="Live bezette lokalen">
            <?php if (!empty($bezette_lokalen)): ?>
                <?php foreach ($bezette_lokalen as $bezet): ?>
                    <article class="bezet-card">
                        <h2><?php echo htmlspecialchars($bezet['lokaal']); ?></h2>
                        <p class="bezet-type"><?php echo htmlspecialchars($bezet['type']); ?></p>
                        <p>Bezet van <?php echo date('H:i', strtotime($bezet['start_tijd'])); ?> tot <?php echo date('H:i', strtotime($bezet['eind_tijd'])); ?></p>
                        <p>Student: <?php echo htmlspecialchars($bezet['student_nummer']); ?></p>
                        <p>Klant: <?php echo htmlspecialchars($bezet['klant']); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bezet-empty">Er zijn op dit moment geen bezette lokalen.</div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
