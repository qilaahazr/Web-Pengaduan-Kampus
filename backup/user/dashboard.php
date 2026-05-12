<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     </a>
    </li>
</ul>

</bo<?php
include '../auth/session.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Dashboard User</h2>

<p>Selamat datang, <?php echo $_SESSION['nama']; ?></p>

<ul>
    <li>
        <a href="tambah_pengaduan.php">
            Buat Pengaduan
        </a>
    </li>

    <li>
        <a href="../auth/logout.php">
            Logout
   dy>
</html>
