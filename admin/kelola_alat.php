<?php
include '../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek sesi admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../index.php");
    exit;
}

// PROSES TAMBAH ALAT
if(isset($_POST['simpan'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok = $_POST['stok'];
    $kondisi = $_POST['kondisi'];
    
    $foto = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $fotobaru = date('dmYHis').$foto;
    $path = "../uploads/".$fotobaru;

    if(!empty($foto)){
        if(move_uploaded_file($tmp, $path)){
            $sql = "INSERT INTO alat (nama_alat, deskripsi, stok, kondisi, gambar) 
                    VALUES ('$nama', '$deskripsi', '$stok', '$kondisi', '$fotobaru')";
            if(mysqli_query($conn, $sql)){
                $status = "success_add";
            }
        } else {
            $status = "err_upload";
        }
    }
}

// PROSES HAPUS ALAT
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    $cek = mysqli_query($conn, "SELECT gambar FROM alat WHERE id_alat='$id'");
    $data = mysqli_fetch_array($cek);
    if($data){
        $file_gambar = "../uploads/" . $data['gambar'];
        if(file_exists($file_gambar)) unlink($file_gambar); 
        mysqli_query($conn, "DELETE FROM alat WHERE id_alat='$id'");
        $status = "success_delete";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Alat - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-label { font-size: 0.85rem; text-uppercase: uppercase; letter-spacing: 1px; color: #666; }
        .badge-kondisi { font-size: 0.75rem; padding: 5px 10px; border-radius: 50px; }
        .btn-action { border-radius: 8px; transition: 0.2s; }
        .sticky-form { position: sticky; top: 90px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark px-4 py-3 mb-4 sticky-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-arrow-left-circle me-2"></i> KELOLA INVENTARIS</a>
            <span class="text-white-50 small">Admin: <?= $_SESSION['nama'] ?></span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card main-card sticky-form">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-plus-square me-2"></i>Tambah Alat</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Alat</label>
                                <input type="text" name="nama_alat" class="form-control" placeholder="Nama barang..." required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">Stok</label>
                                    <input type="number" name="stok" class="form-control" value="1" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">Kondisi</label>
                                    <select name="kondisi" class="form-select">
                                        <option value="Baik">Baru / Baik</option>
                                        <option value="Bekas">Bekas</option>
                                        <option value="Rusak">Perlu Servis</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Spek singkat..."></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Foto Barang</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*" required>
                            </div>
                            <button type="submit" name="simpan" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>Simpan ke Gudang
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card main-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-table me-2"></i>Daftar Inventaris</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="small text-muted">
                                        <th>Alat</th>
                                        <th class="text-center">Stok</th>
                                        <th>Kondisi</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $q = mysqli_query($conn, "SELECT * FROM alat ORDER BY id_alat DESC");
                                    while($row = mysqli_fetch_array($q)): 
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../uploads/<?= $row['gambar'] ?>" class="me-3 shadow-sm">
                                                <div>
                                                    <div class="fw-bold text-dark"><?= $row['nama_alat'] ?></div>
                                                    <small class="text-muted"><?= substr($row['deskripsi'], 0, 40) ?>...</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-primary border border-primary px-3"><?= $row['stok'] ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $c = "bg-success";
                                                if($row['kondisi'] != 'Baik') $c = "bg-warning text-dark";
                                            ?>
                                            <span class="badge badge-kondisi <?= $c ?>"><?= $row['kondisi'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-danger btn-sm btn-action px-3" onclick="confirmDelete(<?= $row['id_alat'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Alat?',
                text: "Data dan foto akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?hapus=" + id;
                }
            })
        }

        <?php if(isset($status) && $status == 'success_add'): ?>
        Swal.fire('Berhasil!', 'Alat baru telah ditambahkan.', 'success');
        <?php endif; ?>

        <?php if(isset($status) && $status == 'success_delete'): ?>
        Swal.fire('Terhapus!', 'Alat telah dihapus dari sistem.', 'success');
        <?php endif; ?>
    </script>
</body>
</html>