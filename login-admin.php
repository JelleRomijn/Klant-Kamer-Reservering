<?php
session_start();

// Als al ingelogd als docent, direct door
if (isset($_SESSION['admin_id'])) {
    header('Location: lijst.php');
    exit();
}

require_once 'assets/core/connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $password       = $_POST['password'] ?? '';

    if (empty($gebruikersnaam) || empty($password)) {
        $error = 'Vul gebruikersnaam en wachtwoord in.';
    } else {
        $stmt = $conn->prepare("SELECT id, naam, password FROM docenten WHERE gebruikersnaam = ?");
        $stmt->bind_param("s", $gebruikersnaam);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $docent = $result->fetch_assoc();
            if (password_verify($password, $docent['password'])) {
                $_SESSION['admin_id']           = $docent['id'];
                $_SESSION['admin_naam']         = $docent['naam'];
                $_SESSION['admin_gebruikersnaam'] = $gebruikersnaam;
                $stmt->close();
                $conn->close();
                header('Location: lijst.php');
                exit();
            } else {
                $error = 'Onjuist wachtwoord.';
            }
        } else {
            $error = 'Gebruiker niet gevonden.';
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>Docent Login - Klant-Kamer-Reservering</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
</head>
<body>
    <div class="logo">
        <img src="Layer 2.png" alt="Het Bureau Logo">
    </div>

    <div class="login-container">
        <h2>Docent Inloggen</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login-admin.php" class="login-form">
            <div class="form-group">
                <label for="gebruikersnaam">Gebruikersnaam:</label>
                <input
                    type="text"
                    id="gebruikersnaam"
                    name="gebruikersnaam"
                    placeholder="Voer je gebruikersnaam in"
                    class="input-field"
                    required
                    value="<?= htmlspecialchars($_POST['gebruikersnaam'] ?? '') ?>"
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password">Wachtwoord:</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Voer je wachtwoord in"
                    class="input-field"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn-login">Inloggen</button>
        </form>
    </div>
</body>
</html>
