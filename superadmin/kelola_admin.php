<?php
include '../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

/**
 * PROTEKSI & DETEKSI ROLE
 */
$session_role = isset($_SESSION['role']) ? $_SESSION['role'] : (isset($_SESSION['level']) ? $_SESSION['level'] : "");

if (!isset($_SESSION['id_user']) || strtolower($session_role) != 'super_admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../index.php';</script>";
    exit;
}

// Cek nama kolom secara dinamis (role atau level)
$cek_kolom = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role'");
$kolom = (mysqli_num_rows($cek_kolom) > 0) ? "role" : "level";

// LOGIKA CRUD
if(isset($_POST['tambah'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    mysqli_query($conn, "INSERT INTO users (nama, username, password, $kolom) VALUES ('$nama', '$user', '$pass', 'admin')");
    header("Location: kelola_admin.php?msg=add_success");
}

if(isset($_POST['edit'])){
    $id = $_POST['id_user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    
    if(!empty($_POST['password'])){
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $q_upd = "UPDATE users SET nama='$nama', username='$user', password='$pass' WHERE id_user='$id'";
    } else {
        $q_upd = "UPDATE users SET nama='$nama', username='$user' WHERE id_user='$id'";
    }
    mysqli_query($conn, $q_upd);
    header("Location: kelola_admin.php?msg=edit_success");
}

if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");
    header("Location: kelola_admin.php?msg=del_success");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Staff Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .card-table { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .modal-content { border: none; border-radius: 20px; }
        .table thead { background-color: #f8fafc; }
        .badge-admin { background-color: #e0f2fe; color: #0369a1; border-radius: 6px; font-size: 11px; font-weight: 700; padding: 4px 8px; }
    </style>
</head>
<body class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Daftar Akun Admin</h3>
                <p class="text-secondary small">Total staff yang memiliki akses ke dashboard operasional.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="dashboard.php" class="btn btn-light shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
                <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Admin
                </button>
            </div>
        </div>

        <div class="card card-table">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-secondary small">
                                <th class="ps-4">NAMA LENGKAP</th>
                                <th>USERNAME</th>
                                <th>ROLE</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = mysqli_query($conn, "SELECT * FROM users WHERE $kolom='admin' ORDER BY id_user DESC");
                            while($row = mysqli_fetch_array($q)): 
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 14px;">
                                            <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-semibold"><?= $row['nama'] ?></span>
                                    </div>
                                </td>
                                <td><span class="text-muted small">@<?= $row['username'] ?></span></td>
                                <td><span class="badge-admin">ADMIN OPERASIONAL</span></td>
                                <td class="text-center">
                                    <button class="btn btn-light btn-sm text-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_user'] ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm text-danger" onclick="confirmDelete(<?= $row['id_user'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEdit<?= $row['id_user'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold">Edit Akun Staff</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Nama Lengkap</label>
                                                    <input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Username</label>
                                                    <input type="text" name="username" class="form-control" value="<?= $row['username'] ?>" required>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold">Ganti Password</label>
                                                    <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin ganti">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="submit" name="edit" class="btn btn-primary w-100 py-2 fw-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0 text-center d-block">
                    <h5 class="fw-bold mt-2">Daftarkan Admin Baru</h5>
                    <p class="text-muted small">Berikan akses pengelolaan inventaris.</p>
                </div>
                <form method="POST">
                    <div class="modal-body px-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control bg-light" placeholder="Masukkan nama staff" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control bg-light" placeholder="Contoh: admin_kamera" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control bg-light" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="submit" name="tambah" class="btn btn-primary w-100 py-2 fw-bold">Daftarkan Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Admin?',
                text: "Akses login orang ini akan dicabut sepenuhnya!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?hapus=" + id;
                }
            })
        }
    </script>
</body>
</html>