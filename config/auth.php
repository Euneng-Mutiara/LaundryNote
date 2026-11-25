<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_user'])) {

    $halamanLogin = strpos($_SERVER['SCRIPT_NAME'], '/views/') !== false
        ? 'login.php'
        : 'views/login.php';

    header("Location: " . $halamanLogin);
    exit;
}

?>