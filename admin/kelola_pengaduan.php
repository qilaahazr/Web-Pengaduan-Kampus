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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .card { max-width: 100% !important; margin: 0 !important; }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-clipboard-data"></i> Data Pengaduan
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0 p-3">
                        <?php
                        $no = 1;
                        while($data = mysqli_fetch_assoc($query)){
                            if($data['status'] == 'selesai'){
                                $statusClass = 'bg-primary';
                                $statusIcon = 'bi-check-circle';
                            } elseif($data['status'] == 'diproses'){
                                $statusClass = 'bg-warning text-dark';
                                $statusIcon = 'bi-arrow-repeat';
                            } else {
                                $statusClass = 'bg-secondary';
                                $statusIcon = 'bi-clock';
                            }
                        ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge <?php echo $statusClass; ?> mb-2">
                                                <i class="bi <?php echo $statusIcon; ?>"></i> <?php echo htmlspecialchars($data['status']); ?>
                                            </span>
                                            <span class="badge bg-info text-dark"><?php echo htmlspecialchars($data['kategori']); ?></span>
                                        </div>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalUpdate<?php echo $data['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                    <h5 class="card-title"><?php echo htmlspecialchars($data['judul']); ?></h5>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-person"></i> <?php echo htmlspecialchars($data['nama']); ?>
                                        <span class="ms-3"><i class="bi bi-calendar"></i> <?php echo date('d-m-Y', strtotime($data['created_at'])); ?></span>
                                    </p>
                                    <p class="card-text text-truncate"><?php echo htmlspecialchars($data['deskripsi']); ?></p>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDeskripsi<?php echo $data['id']; ?>">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </button>
                                        <?php if($data['file_bukti']): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($data['file_bukti']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-paperclip"></i> File
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

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

                                <!-- Modal Update Status -->
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>