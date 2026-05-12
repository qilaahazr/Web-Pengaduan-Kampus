<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
include '../auth/session.php';
include '../config/koneksi.php';

if(isset($_POST['kirim'])){

    $user_id = $_SESSION['id'];
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    $namaFile = $_FILES['file']['name'];
    $tmpFile = $_FILES['file']['tmp_name'];

    move_uploaded_file(
        $tmpFile,
        "../uploads/" . $namaFile
    );

    $query = mysqli_query($conn,
        "INSERT INTO pengaduan(
            user_id,
            judul,
            kategori,
            deskripsi,
            file_bukti
        ) VALUES(
            '$user_id',
            '$judul',
            '$kategori',
            '$deskripsi',
            '$namaFile'
        )"
    );

    if($query){
        echo "Pengaduan berhasil dikirim";
    } else {
        echo "Pengaduan gagal";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pengaduan</title>
</head>
<body>

<h2>Form Pengaduan</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="judul" placeholder="Judul" required>
    <br><br>

    <select name="kategori">
        <option>Fasilitas</option>
        <option>Internet</option>
        <option>Akademik</option>
        <option>Kebersihan</option>
    </select>

    <br><br>

    <textarea name="deskripsi" placeholder="Deskripsi"></textarea>

    <br><br>

    <input type="file" name="file" required>

    <br><br>

    <button type="submit" name="kirim">
        Kirim Pengaduan
    </button>

</form>

<br>

<a href="dashboard.php">Kembali</a>

</body>
</html>

