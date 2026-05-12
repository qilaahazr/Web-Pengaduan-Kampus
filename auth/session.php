<?php
session_start();

// Generate CSRF token if not exists
if(!isset($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(!isset($_SESSION['id'])){
    header("Location: ../auth/login.php");
    exit;
}
?>