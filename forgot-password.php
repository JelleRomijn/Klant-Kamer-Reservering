<?php
require_once 'assets/core/connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = isset($_POST['login_input']) ? trim($_POST['login_input']) : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (empty($login_input) || empty($new_password) || empty($confirm_password)) {
        $error = 'Vul alle velden in.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Wachtwoorden komen niet overeen.';
    } elseif (strlen($new_password) < 6) {
        $error = 'Wachtwoord moet minimaal 6 tekens lang zijn.';
    } else {
        $is_email = filter_var($login_input, FILTER_VALIDATE_EMAIL);

        if (!$is_email && !ctype_digit($login_input)) {
            $error = 'Gebruik een geldig e-mailadres of studentnummer.';
        } else {
            if ($is_email) {
                $query = "SELECT id FROM student WHERE email = ?";
            } else {
                $query = "SELECT id FROM student WHERE nummer = ?";
            }

            $stmt = $conn->prepare($query);

            if ($is_email) {
                $stmt->bind_param("s", $login_input);
            } else {
                $student_nummer = (int) $login_input;
                $stmt->bind_param("i", $student_nummer);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                $update_stmt = $conn->prepare("UPDATE student SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user['id']);

                if ($update_stmt->execute()) {
                    $success = 'Wachtwoord succesvol gewijzigd. U kunt nu opnieuw inloggen.';
                } else {
                    $error = 'Er ging iets mis bij het wijzigen van uw wachtwoord.';
                }

                $update_stmt->close();
            } else {
                $error = 'Geen gebruiker gevonden met deze gegevens.';
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/register.css">
    <title>Wachtwoord Vergeten - Klant-Kamer-Reservering</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
</head>
<body>
    <div class="logo">
        <img src="Layer 2.png" alt="HETBUREAU-LOGO-ZWART">
    </div>

    <div class="register-container">
        <h2>Wachtwoord vergeten</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
                <p><a href="login.php">Ga naar inlogpagina</a></p>
            </div>
        <?php else: ?>
            <form method="POST" action="forgot-password.php" class="register-form">
                <div class="form-group">
                    <label for="login_input">E-mailadres of studentnummer:</label>
                    <input type="text" id="login_input" name="login_input" required value="<?php echo htmlspecialchars($login_input ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="new_password">Nieuw wachtwoord:</label>
                    <input type="password" id="new_password" name="new_password" minlength="6" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Bevestig nieuw wachtwoord:</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
                </div>

                <button type="submit" class="btn-register">Wachtwoord wijzigen</button>
            </form>
        <?php endif; ?>

        <p class="login-link"><a href="login.php">Terug naar inloggen</a></p>
    </div>
</body>
</html>
