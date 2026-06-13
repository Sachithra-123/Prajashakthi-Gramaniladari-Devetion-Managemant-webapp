<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$CURRENT_ROLE = $_SESSION["role"] ?? "";

function allow_roles($roles = []) {
    global $CURRENT_ROLE;
    if (!in_array($CURRENT_ROLE, $roles, true)) {
        die("Access denied for your role.");
    }
}
