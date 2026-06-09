<?php
include_once "assets/core/connect.php";
date_default_timezone_set('Europe/Amsterdam');

$today    = date("Y-m-d");
$min_rows = 5;

$stmt = $conn->prepare("SELECT * FROM reserveringen WHERE datum = ? ORDER BY start_tijd ASC");
$stmt->bind_param("s", $today);
$stmt->execute();
$vandaag_reserveringen = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM reserveringen WHERE datum > ? ORDER BY datum ASC, start_tijd ASC");
$stmt->bind_param("s", $today);
$stmt->execute();
$toekomstige_reserveringen = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$dagnamen_nl = [
    'Monday'    => 'Maandag',   'Tuesday'  => 'Dinsdag',
    'Wednesday' => 'Woensdag',  'Thursday' => 'Donderdag',
    'Friday'    => 'Vrijdag',   'Saturday' => 'Zaterdag',
    'Sunday'    => 'Zondag',
];

$vandaag_count     = count($vandaag_reserveringen);
$toekomst_count    = count($toekomstige_reserveringen);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Het Bureau – Kamer Reservering</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
</head>
<body>

<!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
<section class="hero">
    <div class="hero-pattern"></div>
    <div class="hero-content">

        <div class="hero-main">
            <!-- Left: branding + CTA -->
            <div class="hero-left">
                <div class="hero-logo">
                    <img src="Layer 2.png" alt="Het Bureau Logo">
                </div>
                <div class="hero-text">
                    <h1 class="hero-title">Kamer<br>Reservering</h1>
                    <p class="hero-sub">Reserveer eenvoudig een vergaderruimte bij Het Bureau</p>
                </div>
                <div class="hero-actions">
                    <a href="login.php" class="hero-btn primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 7l4-4 4 4"/><path d="M12 3v13"/><path d="M20 21H4"/></svg>
                        Reserveer Nu
                    </a>
                </div>
            </div>

            <!-- Right: QR panel -->
            <a href="login.php" class="hero-qr-panel" aria-label="Scan de QR-code om te reserveren">
                <span class="hero-qr-label">Scan om te reserveren</span>
                <div class="hero-qr-frame">
                    <img src="./assets/img/qr.png" alt="QR Code" class="hero-qr-img">
                </div>
                <span class="hero-qr-sub">Open direct de reserveringspagina</span>
            </a>
        </div>

        <!-- Bottom info bar -->
        <div class="hero-info-bar">
            <div class="info-chip">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span id="live-time"><?php echo date("H:i"); ?></span>
            </div>
            <div class="info-chip">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span><?php echo date("d-m-Y"); ?></span>
            </div>
            <div class="info-chip accent">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span><?php echo $vandaag_count; ?> vandaag &nbsp;·&nbsp; <?php echo $toekomst_count; ?> aankomend</span>
            </div>
        </div>

    </div>
</section>

<!-- ═══════════════════════════════ VANDAAG ═══════════════════════════════ -->
<div class="section-wrap">
    <div class="section-head">
        <div class="section-head-left">
            <div class="section-icon orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h2 class="section-title">Vandaag</h2>
        </div>
        <span class="section-badge"><?php echo $vandaag_count; ?> reservering<?php echo $vandaag_count !== 1 ? 'en' : ''; ?></span>
    </div>

    <div class="grid-card">
        <div class="grid-header">
            <div class="cell header">Datum</div>
            <div class="cell header">Start</div>
            <div class="cell header">Eind</div>
            <div class="cell header">Lokaal</div>
            <div class="cell header">Gepland door</div>
            <div class="cell header">Met wie</div>
        </div>
        <div class="grid-body-wrap">
            <div class="grid-body" data-min-rows="<?= $min_rows ?>">
                <?php foreach ($vandaag_reserveringen as $r): ?>
                    <div class="cell filled">Vandaag</div>
                    <div class="cell filled"><?= date('H:i', strtotime($r['start_tijd'])) ?></div>
                    <div class="cell filled"><?= date('H:i', strtotime($r['eind_tijd'])) ?></div>
                    <div class="cell filled lokaal"><?= htmlspecialchars($r['lokaal']) ?></div>
                    <div class="cell filled"><?= htmlspecialchars($r['student_nummer']) ?></div>
                    <div class="cell filled"><?= htmlspecialchars($r['klant']) ?></div>
                <?php endforeach; ?>
                <?php for ($i = $vandaag_count; $i < $min_rows; $i++): ?>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════ AANKOMEND ═══════════════════════════════ -->
<div class="section-wrap">
    <div class="section-head">
        <div class="section-head-left">
            <div class="section-icon dark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <h2 class="section-title">Aankomende Reserveringen</h2>
        </div>
        <span class="section-badge"><?php echo $toekomst_count; ?> reservering<?php echo $toekomst_count !== 1 ? 'en' : ''; ?></span>
    </div>

    <div class="grid-card">
        <div class="grid-header">
            <div class="cell header">Datum</div>
            <div class="cell header">Start</div>
            <div class="cell header">Eind</div>
            <div class="cell header">Lokaal</div>
            <div class="cell header">Gepland door</div>
            <div class="cell header">Met wie</div>
        </div>
        <div class="grid-body-wrap">
            <div class="grid-body" data-min-rows="<?= $min_rows ?>">
                <?php foreach ($toekomstige_reserveringen as $r):
                    $res_datum  = new DateTime($r['datum']);
                    $vandaag_dt = new DateTime($today);
                    $diff       = (int) $res_datum->diff($vandaag_dt)->days;

                    if ($diff < 7) {
                        $dag = $dagnamen_nl[$res_datum->format('l')] ?? $res_datum->format('l');
                        $datum_tekst = ($diff === 1) ? 'Morgen' : $dag;
                    } else {
                        $datum_tekst = date("d-m-Y", strtotime($r['datum']));
                    }
                ?>
                    <div class="cell filled"><?= htmlspecialchars($datum_tekst) ?></div>
                    <div class="cell filled"><?= date('H:i', strtotime($r['start_tijd'])) ?></div>
                    <div class="cell filled"><?= date('H:i', strtotime($r['eind_tijd'])) ?></div>
                    <div class="cell filled lokaal"><?= htmlspecialchars($r['lokaal']) ?></div>
                    <div class="cell filled"><?= htmlspecialchars($r['student_nummer']) ?></div>
                    <div class="cell filled"><?= htmlspecialchars($r['klant']) ?></div>
                <?php endforeach; ?>
                <?php for ($i = $toekomst_count; $i < $min_rows; $i++): ?>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                    <div class="cell empty"></div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/index.js"></script>
</body>
</html>
