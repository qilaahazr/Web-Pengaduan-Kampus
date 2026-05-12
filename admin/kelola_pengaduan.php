<?php
include '../auth/session.php';
include '../config/koneksi.php';

if($_SESSION['role'] !== 'admin'){
    header("Location: ../user/dashboard.php");
    exit;
}

$query = mysqli_query($conn,
    "SELECT pengaduan.*, users.nama
     FROM pengaduan
     JOIN users
     ON pengaduan.user_id = users.id
     ORDER BY pengaduan.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengaduan - Admin</title>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">

    <style>
        .table td,
        .table th{
            vertical-align: middle;
        }
        .table thead th{
            background-color: #1e3a8a !important;
            color: white !important;
        }
        .full-width-table {
            width: 100%;
            border-collapse: collapse;
        }
        .full-width-table th,
        .full-width-table td {
            padding: 12px 15px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../assets/img/logo_new.png" alt="Logo" style="height: 35px; margin-right: 10px;">
            <span class="text-white fw-bold">Admin Panel</span>
        </a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i>
                <?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid p-4">

    <h4 class="mb-3">
        <i class="bi bi-clipboard-data"></i> Data Pengaduan
    </h4>

    <table class="table table-hover full-width-table table-bordered">

        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pelapor</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>

            <?php
            $no = 1;

            while($data = mysqli_fetch_assoc($query)){

                if($data['status'] == 'selesai'){
                    $statusClass = 'bg-primary';
                } elseif($data['status'] == 'diproses'){
                    $statusClass = 'bg-warning text-dark';
                } else {
                    $statusClass = 'bg-secondary';
                }
            ?>

            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d-m-Y', strtotime($data['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($data['nama']); ?></td>
                <td><?php echo htmlspecialchars($data['judul']); ?></td>
                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($data['kategori']); ?></span></td>
                <td><span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($data['status']); ?></span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalUpdate<?php echo $data['id']; ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#modalDeskripsi<?php echo $data['id']; ?>">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php if($data['file_bukti']): ?>
                    <a href="../uploads/<?php echo htmlspecialchars($data['file_bukti']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-paperclip"></i>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- Modal Deskripsi -->
            <div class="modal fade" id="modalDeskripsi<?php echo $data['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo htmlspecialchars($data['judul']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Pelapor:</strong> <?php echo htmlspecialchars($data['nama']); ?></p>
                            <p><strong>Kategori:</strong> <?php echo htmlspecialchars($data['kategori']); ?></p>
                            <p><strong>Tanggal:</strong> <?php echo date('d-m-Y H:i', strtotime($data['created_at'])); ?></p>
                            <hr>
                            <p><?php echo nl2br(htmlspecialchars($data['deskripsi'], ENT_QUOTES, 'UTF-8')); ?></p>
                            <?php if($data['file_bukti']): ?>
                            <hr>
                            <p><strong>File Bukti:</strong></p>
                            <img src="../uploads/<?php echo htmlspecialchars($data['file_bukti']); ?>" class="img-fluid rounded" alt="Bukti">
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Update -->
            <div class="modal fade" id="modalUpdate<?php echo $data['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="update_status.php">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                                <label class="form-label">Status:</label>
                                <select name="status" class="form-select">
                                    <option value="menunggu" <?php echo $data['status'] == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="diproses" <?php echo $data['status'] == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                    <option value="selesai" <?php echo $data['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php } ?>

        </tbody>

    </table>

</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
