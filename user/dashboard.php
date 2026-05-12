<?php
include '../auth/session.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pengaduan Kampus</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../assets/img/logo_new.png" alt="Logo" style="height: 35px; margin-right: 10px;">
            <span class="text-white fw-bold">Pengaduan Kampus</span>
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </h2>
                    <p class="lead">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!</p>

                    <hr class="my-4">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <a href="tambah_pengaduan.php" style="display:flex; align-items:center; justify-content:space-between; height:120px; padding:20px; background:#1e3a8a; color:white; border-radius:8px; text-decoration:none; box-sizing:border-box;">
                                <div>
                                    <h5 style="margin:0;"><i class="bi bi-pencil-square"></i> Buat Pengaduan</h5>
                                    <p style="margin:5px 0 0 0; opacity:0.8; font-size:14px;">Ajukan keluhan atau masukan baru</p>
                                </div>
                                <i class="bi bi-arrow-right" style="font-size:24px; opacity:0.5;"></i>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="../auth/logout.php" style="display:flex; align-items:center; justify-content:space-between; height:120px; padding:20px; background:#1e3a8a; color:white; border-radius:8px; text-decoration:none; box-sizing:border-box;">
                                <div>
                                    <h5 style="margin:0;"><i class="bi bi-box-arrow-right"></i> Logout</h5>
                                    <p style="margin:5px 0 0 0; opacity:0.8; font-size:14px;">Keluar dari sistem</p>
                                </div>
                                <i class="bi bi-arrow-right" style="font-size:24px; opacity:0.5;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>