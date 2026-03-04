<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if($_SESSION['role'] != 'super_admin'){ exit; }

// --- LOGIKA TAMBAH ---
if(isset($_POST['simpan'])){
    $nama = $_POST['nama'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    mysqli_query($conn, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$user', '$pass', '$role')");
    header("Location: users.php");
}

// --- LOGIKA HAPUS ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    if($id != $_SESSION['id_user']){
        mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");
    }
    header("Location: users.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Rental Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">Manajemen User</h3>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <h5 class="fw-bold mb-3">Tambah Akun</h5>
                    <form method="POST">
                        <div class="mb-2"><label class="small fw-bold">Nama</label><input type="text" name="nama" class="form-control" required></div>
                        <div class="mb-2"><label class="small fw-bold">Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="mb-2"><label class="small fw-bold">Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="small fw-bold">Role</label>
                            <select name="role" class="form-select">
                                <option value="peminjam">Peminjam</option>
                                <option value="admin">Petugas (Admin)</option>
                                <option value="super_admin">Super Admin (Bos)</option>
                            </select>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-danger w-100 fw-bold">Simpan User</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <table class="table align-middle mb-0">
                        <thead class="table-dark">
                            <tr><th>Nama</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = mysqli_query($conn, "SELECT * FROM users ORDER BY role DESC");
                            while($r = mysqli_fetch_array($q)): ?>
                            <tr>
                                <td><?= $r['nama'] ?></td>
                                <td><?= $r['username'] ?></td>
                                <td><span class="badge bg-secondary"><?= strtoupper($r['role']) ?></span></td>
                                <td>
                                    <?php if($r['id_user'] != $_SESSION['id_user']): ?>
                                    <a href="?hapus=<?= $r['id_user'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user ini?')"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>