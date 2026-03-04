<?php 
include '../config/koneksi.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi: Cuma Super Admin yang bisa akses folder ini
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'super_admin'){ 
    header("Location: ../index.php"); 
    exit;
} 

// --- 1. PROSES TAMBAH DATA ---
if(isset($_POST['tambah'])){
    $nama_alat = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $id_kategori = $_POST['id_kategori'];
    $stok = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Proses Upload Gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $path = "../uploads/" . $gambar;

    if(move_uploaded_file($tmp, $path)){
        $query = "INSERT INTO alat (id_kategori, nama_alat, stok, deskripsi, gambar) 
                  VALUES ('$id_kategori', '$nama_alat', '$stok', '$deskripsi', '$gambar')";
        mysqli_query($conn, $query);
        header("Location: kelola_alat.php?msg=success");
        exit;
    }
}

// --- 2. PROSES EDIT DATA ---
if(isset($_POST['edit'])){
    $id_alat = $_POST['id_alat'];
    $nama_alat = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $id_kategori = $_POST['id_kategori'];
    $stok = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $gambar_baru = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if(!empty($gambar_baru)){
        move_uploaded_file($tmp, "../uploads/" . $gambar_baru);
        $query = "UPDATE alat SET id_kategori='$id_kategori', nama_alat='$nama_alat', stok='$stok', deskripsi='$deskripsi', gambar='$gambar_baru' WHERE id_alat='$id_alat'";
    } else {
        $query = "UPDATE alat SET id_kategori='$id_kategori', nama_alat='$nama_alat', stok='$stok', deskripsi='$deskripsi' WHERE id_alat='$id_alat'";
    }
    
    mysqli_query($conn, $query);
    header("Location: kelola_alat.php?msg=updated");
    exit;
}

// --- 3. PROSES HAPUS DATA ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    // Hapus file gambar dari folder biar hemat storage
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM alat WHERE id_alat='$id'"));
    if($data['gambar']) unlink("../uploads/" . $data['gambar']);

    mysqli_query($conn, "DELETE FROM alat WHERE id_alat='$id'");
    header("Location: kelola_alat.php?msg=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Alat - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; }
        .card { border: none; border-radius: 15px; }
    </style>
</head>
<body class="p-4">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="bi bi-box-seam me-2"></i>Katalog Alat Rental</h3>
        <a href="dashboard.php" class="btn btn-secondary shadow-sm">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tambah Alat Baru</h5>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label class="small fw-bold">Nama Alat</label>
                            <input type="text" name="nama_alat" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Kategori</label>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php 
                                $kats = mysqli_query($conn, "SELECT * FROM kategori");
                                while($k = mysqli_fetch_array($kats)) echo "<option value='".$k['id_kategori']."'>".$k['nama_kategori']."</option>";
                                ?>
                            </select>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="small fw-bold">Stok</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold">Gambar</label>
                                <input type="file" name="gambar" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" name="tambah" class="btn btn-primary w-100 fw-bold">Simpan Alat</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th>Gambar</th>
                                <th class="text-start">Nama Alat</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = mysqli_query($conn, "SELECT alat.*, kategori.nama_kategori FROM alat 
                                                      LEFT JOIN kategori ON alat.id_kategori = kategori.id_kategori 
                                                      ORDER BY id_alat DESC");
                            while($r = mysqli_fetch_array($q)): ?>
                            <tr class="text-center">
                                <td><img src="../uploads/<?= $r['gambar'] ?>" class="img-preview shadow-sm"></td>
                                <td class="text-start">
                                    <div class="fw-bold"><?= $r['nama_alat'] ?></div>
                                    <small class="text-muted"><?= substr($r['deskripsi'], 0, 40) ?>...</small>
                                </td>
                                <td><span class="badge bg-info text-dark"><?= $r['nama_kategori'] ?? 'N/A' ?></span></td>
                                <td class="fw-bold"><?= $r['stok'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id_alat'] ?>"><i class="bi bi-pencil-square"></i></button>
                                    <a href="?hapus=<?= $r['id_alat'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus alat?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal<?= $r['id_alat'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" enctype="multipart/form-data" class="modal-content text-start">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Alat</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_alat" value="<?= $r['id_alat'] ?>">
                                            <div class="mb-2">
                                                <label class="small fw-bold">Nama Alat</label>
                                                <input type="text" name="nama_alat" class="form-control" value="<?= $r['nama_alat'] ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="small fw-bold">Kategori</label>
                                                <select name="id_kategori" class="form-select" required>
                                                    <?php 
                                                    $kats_edit = mysqli_query($conn, "SELECT * FROM kategori");
                                                    while($ke = mysqli_fetch_array($kats_edit)): ?>
                                                        <option value="<?= $ke['id_kategori'] ?>" <?= ($ke['id_kategori'] == $r['id_kategori']) ? 'selected' : '' ?>>
                                                            <?= $ke['nama_kategori'] ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <label class="small fw-bold">Stok</label>
                                                    <input type="number" name="stok" class="form-control" value="<?= $r['stok'] ?>" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="small fw-bold">Gambar (Kosongkan jika tidak ganti)</label>
                                                    <input type="file" name="gambar" class="form-control">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="3"><?= $r['deskripsi'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="edit" class="btn btn-warning fw-bold">Update Data</button>
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