<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
include '../auth/session.php';
include '../config/koneksi.php';

$query = mysqli_query($conn,
    "SELECT pengaduan.*, users.nama
     FROM pengaduan
     JOIN users
     ON pengaduan.user_id = users.id
     ORDER BY pengaduan.id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pengaduan</title>
</head>
<body>

<h2>Data Pengaduan</h2>

<table border="1" cellpadding="10">

<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Judul</th>
    <th>Kategori</th>
    <th>Deskripsi</th>
    <th>File</th>
    <th>Status</th>
</tr>

<?php
$no = 1;

while($data = mysqli_fetch_assoc($query)){
?>

<tr>
    <td><?= $no++; ?></td>

    <td><?= $data['nama']; ?></td>

    <td><?= $data['judul']; ?></td>

    <td><?= $data['kategori']; ?></td>

    <td><?= $data['deskripsi']; ?></td>

    <td>
        <a href="../uploads/<?= $data['file_bukti']; ?>">
            Lihat File
        </a>
    </td>

    <td><?= $data['status']; ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>
