<?php
include '../auth/session.php';
include '../config/koneksi.php';

// CSRF Protection
if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']){
    die("CSRF validation failed!");
}

if(isset($_POST['id']) && isset($_POST['status'])){
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare($conn, "UPDATE pengaduan SET status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    $query = mysqli_stmt_execute($stmt);

    if($query){
        header("Location: kelola_pengaduan.php");
        exit;
    } else {
        echo "Gagal update status";
    }
} else {
    header("Location: kelola_pengaduan.php");
    exit;
}
?>