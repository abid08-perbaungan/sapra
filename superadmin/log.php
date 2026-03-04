<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if($_SESSION['role'] != 'super_admin'){ exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Log Aktivitas Sistem</h3>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </div>
        
        <div class="card border-0 shadow-sm">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $q = mysqli_query($conn, "SELECT l.*, u.nama, u.role FROM log_aktivitas l 
                                              JOIN users u ON l.id_user = u.id_user 
                                              ORDER BY l.id_log DESC");
                    while($r = mysqli_fetch_array($q)): ?>
                    <tr>
                        <td><?= $r['waktu'] ?></td>
                        <td class="fw-bold"><?= $r['nama'] ?></td>
                        <td><span class="badge bg-secondary"><?= strtoupper($r['role']) ?></span></td>
                        <td><?= $r['aksi'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>