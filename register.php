<?php
require_once 'assets/core/connect.php';
require_once 'assets/core/config.php';
/** @var mysqli $conn */

$error = '';
$success = '';

// Determine client IP (supporting proxied requests)
$client_ip = '';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $client_ip = trim($ips[0]);
} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
    $client_ip = $_SERVER['REMOTE_ADDR'];
}

$allowed_to_register = is_ip_allowed($client_ip);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$allowed_to_register) {
        $error = 'Registratie is alleen mogelijk wanneer u verbonden bent met het gebouw Wi‑Fi.';
    } else {
        $student_nummer = isset($_POST['student_nummer']) ? trim($_POST['student_nummer']) : '';
        $voornaam = isset($_POST['voornaam']) ? trim($_POST['voornaam']) : '';
        $achternaam = isset($_POST['achternaam']) ? trim($_POST['achternaam']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

        // Validatie
        if (empty($student_nummer) || empty($voornaam) || empty($achternaam) || empty($email) || empty($password) || empty($password_confirm)) {
            $error = 'Alle velden zijn verplicht.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Ongeldig e-mailadres.';
        } elseif ($password !== $password_confirm) {
            $error = 'Wachtwoorden komen niet overeen.';
        } elseif (strlen($password) < 6) {
            $error = 'Wachtwoord moet minimaal 6 tekens lang zijn.';
        } else {
            try {
                // Check if student_nummer or email already exists
                $check_query = "SELECT id FROM users WHERE student_nummer = ? OR email = ?";
                $check_stmt = $conn->prepare($check_query);
                if ($check_stmt === false) {
                    $error = 'Er is een databasefout opgetreden. Probeer het later opnieuw.';
                } else {
                    $check_stmt->bind_param("is", $student_nummer, $email);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();

                    if ($check_result->num_rows > 0) {
                        $error = 'Student nummer of e-mailadres bestaat al.';
                    } else {
                        // Schrijf account naar users en student in 1 transaction.
                        $conn->begin_transaction();

                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                        $insert_user_query = "INSERT INTO users (student_nummer, voornaam, achternaam, email, password) VALUES (?, ?, ?, ?, ?)";
                        $insert_user_stmt = $conn->prepare($insert_user_query);

                        if ($insert_user_stmt === false) {
                            $conn->rollback();
                            $error = 'Er is een databasefout opgetreden. Probeer het later opnieuw.';
                        } else {
                            $insert_user_stmt->bind_param("issss", $student_nummer, $voornaam, $achternaam, $email, $hashed_password);

                            if (!$insert_user_stmt->execute()) {
                                $conn->rollback();
                                $error = 'Er is een fout opgetreden bij het registreren. Probeer het later opnieuw.';
                            } else {
                                $student_exists_query = "SELECT id FROM student WHERE nummer = ? LIMIT 1";
                                $student_exists_stmt = $conn->prepare($student_exists_query);

                                if ($student_exists_stmt === false) {
                                    $conn->rollback();
                                    $error = 'Er is een databasefout opgetreden. Probeer het later opnieuw.';
                                } else {
                                    $student_exists_stmt->bind_param("i", $student_nummer);
                                    $student_exists_stmt->execute();
                                    $student_exists_result = $student_exists_stmt->get_result();

                                    if ($student_exists_result->num_rows === 0) {
                                        $insert_student_query = "INSERT INTO student (nummer, voornaam, achternaam) VALUES (?, ?, ?)";
                                        $insert_student_stmt = $conn->prepare($insert_student_query);

                                        if ($insert_student_stmt === false) {
                                            $conn->rollback();
                                            $error = 'Er is een databasefout opgetreden. Probeer het later opnieuw.';
                                        } else {
                                            $insert_student_stmt->bind_param("iss", $student_nummer, $voornaam, $achternaam);

                                            if (!$insert_student_stmt->execute()) {
                                                $conn->rollback();
                                                $error = 'Er is een fout opgetreden bij het opslaan van studentgegevens.';
                                            } else {
                                                $conn->commit();
                                                $success = 'Registratie succesvol! U kunt nu inloggen.';
                                                // Clear form fields
                                                $student_nummer = '';
                                                $voornaam = '';
                                                $achternaam = '';
                                                $email = '';
                                                $password = '';
                                                $password_confirm = '';
                                            }

                                            $insert_student_stmt->close();
                                        }
                                    } else {
                                        $conn->commit();
                                        $success = 'Registratie succesvol! U kunt nu inloggen.';
                                        // Clear form fields
                                        $student_nummer = '';
                                        $voornaam = '';
                                        $achternaam = '';
                                        $email = '';
                                        $password = '';
                                        $password_confirm = '';
                                    }

                                    $student_exists_stmt->close();
                                }
                            }

                            $insert_user_stmt->close();
                        }
                    }
                    $check_stmt->close();
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
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/register.css">

    <title>Registratie - Klant-Kamer-Reservering</title>
    <link rel="icon" type="image/x-icon" href="BUREAU-LOGO.ico">
</head>

<body>
    <div class="logo">
        <img src="Layer 2.png" alt="HETBUREAU-LOGO-ZWART">
    </div>

    <div class="register-container">
        <h2>Registratie</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
                <p><a href="login.php">Ga naar inlogpagina</a></p>
            </div>
        <?php else: ?>
            <?php if (!$allowed_to_register): ?>
                <div class="error-message">Registratie is alleen mogelijk wanneer u verbonden bent met het gebouw Wi‑Fi. (IP: <?php echo htmlspecialchars($client_ip); ?>)</div>
            <?php else: ?>
            <form method="POST" action="register.php" class="register-form">
                <div class="form-group">
                    <label for="student_nummer">Student Nummer:</label>
                    <input type="number" id="student_nummer" name="student_nummer" pattern="[0-9]{6}" maxlength="6" required value="<?php echo htmlspecialchars($student_nummer ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="voornaam">Voornaam:</label>
                    <input type="text" id="voornaam" name="voornaam" required value="<?php echo htmlspecialchars($voornaam ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="achternaam">Achternaam:</label>
                    <input type="text" id="achternaam" name="achternaam" required value="<?php echo htmlspecialchars($achternaam ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-mailadres:</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Wachtwoord:</label>
                    <input type="password" id="password" name="password" minlength="6" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Wachtwoord bevestigen:</label>
                    <input type="password" id="password_confirm" name="password_confirm" minlength="6" required>
                </div>

                <button type="submit" class="btn-register">Registreren</button>
            </form>

            <p class="login-link">Al een account? <a href="login.php">Log in</a></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>

</html>