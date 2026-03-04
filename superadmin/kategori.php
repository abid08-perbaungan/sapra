<?php 
include '../config/koneksi.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi: Benar-benar CUMA boleh Super Admin (Role: super_admin)
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'super_admin'){ 
    header("Location: ../index.php"); 
    exit; 
}

// --- PROSES ACTION ---

// 1. Tambah Kategori
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
    header("Location: kategori.php?msg=success");
    exit;
}

// 2. Edit Kategori
if(isset($_POST['edit'])){
    $id = $_POST['id_kategori'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    mysqli_query($conn, "UPDATE kategori SET nama_kategori='$nama' WHERE id_kategori='$id'");
    header("Location: kategori.php?msg=updated");
    exit;
}

// 3. Hapus Kategori
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    // Opsional: Cek apakah kategori masih dipakai di tabel alat sebelum dihapus
    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");
    header("Location: kategori.php?msg=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; }
        .table thead { background-color: #2d3436; color: white; }
    </style>
</head>
<body class="p-4">

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-tags-fill me-2 text-info"></i>Manajemen Kategori</h3>
                <p class="text-muted mb-0">Kelola kategori barang untuk pengelompokan alat rental.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary shadow-sm fw-bold">
                <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Operasi data berhasil dilakukan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Tambah Kategori</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nama Kategori</label>
                                <input type="text" name="nama_kategori" class="form-control form-control-lg" placeholder="Contoh: Kamera" required>
                                <div class="form-text">Misal: Kamera, Tripod, Lighting, Tenda, dll.</div>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-info w-100 fw-bold text-white py-2 shadow-sm">
                                <i class="bi bi-plus-circle me-1"></i> Simpan Kategori
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th width="10%">No</th>
                                    <th class="text-start">Nama Kategori Alat</th>
                                    <th width="25%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $q = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
                                if(mysqli_num_rows($q) == 0):
                                ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada kategori. Silakan tambah data.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php while($r = mysqli_fetch_array($q)): ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                    <td class="fw-semibold"><?= $r['nama_kategori'] ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id_kategori'] ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        
                                        <a href="?hapus=<?= $r['id_kategori'] ?>" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Hapus kategori ini? Pastikan kategori ini tidak sedang digunakan oleh alat.')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editModal<?= $r['id_kategori'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Kategori</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_kategori" value="<?= $r['id_kategori'] ?>">
                                                <div class="mb-3 text-start">
                                                    <label class="form-label small fw-bold">Ubah Nama Kategori</label>
                                                    <input type="text" name="nama_kategori" class="form-control" value="<?= $r['nama_kategori'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="edit" class="btn btn-warning fw-bold text-white px-4">Update</button>
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