<?php
include '../auth/session.php';
include '../config/koneksi.php';

$pesan = '';

if(isset($_POST['kirim'])){

    $user_id = $_SESSION['id'];
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    $namaFile = $_FILES['file']['name'];
    $tmpFile = $_FILES['file']['tmp_name'];

    // Security: Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $tmpFile);
    finfo_close($fileInfo);

    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $safeExt = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

    if (!in_array($mimeType, $allowedTypes) || !in_array($ext, $safeExt)) {
        $pesan = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> File type not allowed!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    } elseif ($_FILES['file']['size'] > 5 * 1024 * 1024) {
        $pesan = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle"></i> File too large (max 5MB)!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    } else {
        $safeName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $namaFile);
        move_uploaded_file($tmpFile, "../uploads/" . $safeName);
        $namaFile = $safeName;
    }

    if(isset($namaFile) && !isset($pesan)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO pengaduan (user_id, judul, kategori, deskripsi, file_bukti) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issss", $user_id, $judul, $kategori, $deskripsi, $namaFile);
        $query = mysqli_stmt_execute($stmt);

        if($query){
            $pesan = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> Pengaduan berhasil dikirim!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        } else {
            $pesan = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle"></i> Pengaduan gagal dikirim.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengaduan - Sistem Pengaduan Kampus</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="../assets/img/logo_new.png" alt="Logo" style="height: 35px; margin-right: 10px;">
            <span class="text-white fw-bold">Pengaduan Kampus</span>
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i> <?php echo $_SESSION['nama']; ?>
            </span>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4">
                        <i class="bi bi-pencil-square"></i> Ajukan Pengaduan
                    </h2>

                    <?php echo $pesan; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-card-heading"></i> Judul Pengaduan
                            </label>
                            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul pengaduan" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-tags"></i> Kategori
                            </label>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Fasilitas">Fasilitas</option>
                                <option value="Internet">Internet</option>
                                <option value="Akademik">Akademik</option>
                                <option value="Kebersihan">Kebersihan</option>
                                <option value="Keamanan">Keamanan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-chat-text"></i> Deskripsi
                            </label>
                            <textarea name="deskripsi" class="form-control" rows="5" placeholder="Jelaskan detail pengaduan Anda..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-paperclip"></i> File Bukti (Gambar/PDF)
                            </label>
                            <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                            <div class="form-text">Format: JPG, PNG, GIF, PDF (max 5MB)</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="kirim" class="btn btn-custom btn-lg">
                                <i class="bi bi-send"></i> Kirim Pengaduan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>