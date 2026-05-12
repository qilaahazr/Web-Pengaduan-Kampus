<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
session_start();

if(!isset($_SESSION['id'])){
    header("Location: ../auth/login.php");
    exit;
}
?>
