<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Proteksi Admin/Petugas & Super Admin
if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')){ 
    header("Location: ../index.php"); 
    exit; 
}

// --- PROSES TAMBAH ALAT ---
if(isset($_POST['tambah'])){
    $nama = $_POST['nama_alat'];
    $kategori = $_POST['id_kategori']; // Ambil ID Kategori
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    
    // Upload Gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $path = "../uploads/".$gambar;

    if(move_uploaded_file($tmp, $path)){
        $query = "INSERT INTO alat (id_kategori, nama_alat, stok, deskripsi, gambar) 
                  VALUES ('$kategori', '$nama', '$stok', '$deskripsi', '$gambar')";
        mysqli_query($conn, $query);
        header("Location: kelola_alat.php?msg=success");
    }
}

// --- PROSES HAPUS ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM alat WHERE id_alat='$id'");
    header("Location: kelola_alat.php?msg=deleted");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Alat - Rental Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .img-table { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body class="p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-tools me-2"></i>Manajemen Alat</h3>
            <a href="dashboard.php" class="btn btn-secondary shadow-sm">Dashboard</a>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Tambah Alat Baru</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Alat</label>
                                <input type="text" name="nama_alat" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Kategori</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php 
                                    $kat = mysqli_query($conn, "SELECT * FROM kategori");
                                    while($k = mysqli_fetch_array($kat)): ?>
                                        <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Foto Alat</label>
                                <input type="file" name="gambar" class="form-control" required>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary w-100 fw-bold">Simpan Alat</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0 text-center">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Alat</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Query JOIN untuk mengambil nama kategori
                                $sql = "SELECT alat.*, kategori.nama_kategori 
                                        FROM alat 
                                        LEFT JOIN kategori ON alat.id_kategori = kategori.id_kategori 
                                        ORDER BY id_alat DESC";
                                $q = mysqli_query($conn, $sql);
                                while($r = mysqli_fetch_array($q)): ?>
                                <tr>
                                    <td><img src="../uploads/<?= $r['gambar'] ?>" class="img-table shadow-sm"></td>
                                    <td class="text-start">
                                        <div class="fw-bold"><?= $r['nama_alat'] ?></div>
                                        <small class="text-muted"><?= substr($r['deskripsi'], 0, 50) ?>...</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= $r['nama_kategori'] ?? 'Tanpa Kategori' ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold"><?= $r['stok'] ?></td>
                                    <td>
                                        <a href="?hapus=<?= $r['id_alat'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus alat ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
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