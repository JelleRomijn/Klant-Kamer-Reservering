<?php
function require_admin_login($json_response = false) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['admin_id'])) {
        return;
    }

    if ($json_response) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Niet ingelogd als docent.']);
    } else {
        header('Location: login-admin.php');
    }

    exit();
}
