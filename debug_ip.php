<?php
// Quick debug page to show your IP address
require_once 'assets/core/config.php';

$client_ip = '';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $client_ip = trim($ips[0]);
} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
    $client_ip = $_SERVER['REMOTE_ADDR'];
}

$allowed = is_ip_allowed($client_ip);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Debug IP</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .info { background: #e8f4f8; padding: 15px; border-left: 4px solid #0099cc; margin: 10px 0; }
        .allowed { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 10px 0; }
        .denied { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0; }
        code { background: #f0f0f0; padding: 8px; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug IP</h1>
        
        <div class="info">
            <strong>Jouw IP-adres:</strong><br>
            <code><?php echo htmlspecialchars($client_ip); ?></code>
        </div>

        <?php if ($allowed): ?>
            <div class="allowed">
                <strong>✅ Status:</strong> Je bent verbonden met het toegestane netwerk
            </div>
        <?php else: ?>
            <div class="denied">
                <strong>❌ Status:</strong> Je bent NIET verbonden met het toegestane netwerk
            </div>
        <?php endif; ?>

        <div class="info">
            <strong>Toegestane subnetten:</strong><br>
            <?php foreach ($ALLOWED_SUBNETS as $subnet): ?>
                <code><?php echo htmlspecialchars($subnet); ?></code><br>
            <?php endforeach; ?>
        </div>

        <p style="margin-top: 30px; color: #666;">
            Stuur de IP-adres hierboven door naar de beheerder zodat deze in de config.php kan worden toegevoegd als je nu niet bent toegestaan.
        </p>
    </div>
</body>
</html>
