<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if($_SESSION['role'] != 'super_admin'){ header("Location: ../index.php"); exit; }

// --- PROSES HAPUS (CRUD: Delete) ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman='$id'");
    header("Location: konfirmasi_pinjam.php?msg=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Peminjaman - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h3 class="fw-bold">Manajemen Transaksi (Admin)</h3>
            <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Peminjam</th>
                            <th>Alat</th>
                            <th>Jumlah</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Menggunakan LEFT JOIN agar jika ada data kosong tidak error
                        $sql = "SELECT p.*, u.nama, a.nama_alat 
                                FROM peminjaman p 
                                LEFT JOIN users u ON p.id_user = u.id_user 
                                LEFT JOIN alat a ON p.id_alat = a.id_alat 
                                ORDER BY p.id_peminjaman DESC";
                        $q = mysqli_query($conn, $sql);
                        while($r = mysqli_fetch_array($q)): 
                        ?>
                        <tr class="text-center">
                            <td class="text-start ps-3 fw-bold"><?= $r['nama'] ?? 'User Dihapus' ?></td>
                            <td><?= $r['nama_alat'] ?? 'Alat Dihapus' ?></td>
                            <td><?= $r['jumlah'] ?></td>
                            <td><?= $r['tgl_pinjam'] ?></td>
                            <td>
                                <?= (!empty($r['tgl_kembali'])) ? $r['tgl_kembali'] : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($r['status']=='pending') ? 'warning' : (($r['status']=='disetujui') ? 'primary' : 'success') ?>">
                                    <?= strtoupper($r['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="?hapus=<?= $r['id_peminjaman'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus permanen riwayat ini?')">
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
</body>
</html>