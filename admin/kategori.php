<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Proteksi: Hanya Super Admin (Role: super_admin)
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'super_admin'){ 
    echo "<script>alert('Akses khusus Super Admin!'); window.location='dashboard.php';</script>";
    exit; 
}

// --- PROSES ACTION ---
// 1. Tambah Kategori
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
    header("Location: kategori.php?msg=success");
}

// 2. Edit Kategori
if(isset($_POST['edit'])){
    $id = $_POST['id_kategori'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "UPDATE kategori SET nama_kategori='$nama' WHERE id_kategori='$id'");
    header("Location: kategori.php?msg=updated");
}

// 3. Hapus Kategori
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");
    header("Location: kategori.php?msg=deleted");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kategori - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold"><i class="bi bi-tags me-2"></i>Manajemen Kategori</h3>
                <p class="text-muted">Kelola kategori alat untuk mempermudah pencarian.</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary shadow-sm">Kembali</a>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Tambah Kategori Baru</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Kamera" required>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary w-100 fw-bold">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Nama Kategori</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $q = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
                                while($r = mysqli_fetch_array($q)): ?>
                                <tr>
                                    <td class="ps-3"><?= $no++ ?></td>
                                    <td class="fw-bold"><?= $r['nama_kategori'] ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id_kategori'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?hapus=<?= $r['id_kategori'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editModal<?= $r['id_kategori'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                                <button type="button" class="btn-close" data-bs-modal="dismiss"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_kategori" value="<?= $r['id_kategori'] ?>">
                                                <label class="form-label small fw-bold">Nama Kategori</label>
                                                <input type="text" name="nama_kategori" class="form-control" value="<?= $r['nama_kategori'] ?>" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="edit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>