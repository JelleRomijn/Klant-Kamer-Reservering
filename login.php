<?php
// Start the session
session_start();
require_once 'assets/core/connect.php';
/** @var mysqli $conn */

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = isset($_POST['login_input']) ? trim($_POST['login_input']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validatie
    if (empty($login_input) || empty($password)) {
        $error = 'Voer alstublieft e-mailadres/student nummer en wachtwoord in.';
    } else {
        // Check if input is email or student number
        $is_email = filter_var($login_input, FILTER_VALIDATE_EMAIL);

        if ($is_email) {
            $query = "SELECT id, student_nummer, voornaam, achternaam, password FROM users WHERE email = ?";
        } else {
            $query = "SELECT id, student_nummer, voornaam, achternaam, password FROM users WHERE student_nummer = ?";
        }

        try {
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                $error = 'Er is een databasefout opgetreden. Probeer het later opnieuw.';
            } else {
                if ($is_email) {
                    $stmt->bind_param("s", $login_input);
                } else {
                    $stmt->bind_param("i", $login_input);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['student_nummer'] = $user['student_nummer'];
                        $_SESSION['voornaam'] = $user['voornaam'];
                        $_SESSION['achternaam'] = $user['achternaam'];

                        // Redirect to reservation page
                        header("Location: reserve.php");
                        exit();
                    } else {
                        $error = 'Onjuist wachtwoord.';
                    }
                } else {
                    $error = 'Gebruiker niet gevonden.';
                }

                $stmt->close();
            }
        } catch (mysqli_sql_exception $e) {
            if (stripos($e->getMessage(), "doesn't exist") !== false) {
                $error = 'Database tabel mist. Importeer eerst bureau_kamer.sql in phpMyAdmin.';
            } else {
                $error = 'Er is een databasefout opgetreden. Probeer het later opnieuw.';
            }
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
    <link rel="stylesheet" href="assets/css/login.css">

    <title>Login - Klant-Kamer-Reservering</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">

</head>

<body>
    <div class="logo">
        <img src="Layer 2.png" alt="HETBUREAU-LOGO-ZWART">
    </div>

    <div class="login-container">
        <h2>Inloggen</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="login_input">E-mailadres of Student Nummer:</label>
                <input
                    type="text"
                    id="login_input"
                    name="login_input"
                    placeholder="bijv. student@school.nl of 123456"
                    class="input-field"
                    required
                    value="<?php echo htmlspecialchars($login_input ?? ''); ?>"
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" placeholder="Voer je wachtwoord in" class="input-field" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Inloggen</button>
        </form>

        <p class="forgot-link"><a href="forgot-password.php">Wachtwoord vergeten?</a></p>
        <p class="register-link">Nog geen account? <a href="register.php">Registreren</a></p>
    </div>
</body>

</html>
