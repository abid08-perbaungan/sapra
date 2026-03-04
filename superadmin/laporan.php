<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Proteksi: Cuma Super Admin (Admin Dosen) yang bisa akses
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'super_admin'){ 
    header("Location: ../index.php"); 
    exit;
} 

// --- FITUR CRUD: HAPUS DATA PEMINJAMAN ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman='$id'");
    header("Location: laporan.php?msg=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Laporan & Transaksi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        @media print {
            .no-print, .btn-action { display: none !important; }
            body { background-color: white; padding: 0; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 shadow-sm rounded">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h3 class="fw-bold text-dark"><i class="bi bi-file-earmark-bar-graph me-2"></i>CRUD Laporan Peminjaman</h3>
            <div>
                <button onclick="window.print()" class="btn btn-success me-2"><i class="bi bi-printer me-1"></i> Cetak PDF</button>
                <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
            </div>
        </div>

        <div class="text-center mb-4 d-none d-print-block">
            <h2 class="fw-bold">LAPORAN PEMINJAMAN ALAT</h2>
            <p>Sistem Informasi Rental Pro - Admin Report</p>
            <hr>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Nama Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th class="btn-action">Aksi (CRUD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $q = mysqli_query($conn, "SELECT p.*, u.nama, a.nama_alat FROM peminjaman p 
                                              LEFT JOIN users u ON p.id_user = u.id_user 
                                              LEFT JOIN alat a ON p.id_alat = a.id_alat 
                                              ORDER BY p.id_peminjaman DESC");
                    while($r = mysqli_fetch_array($q)): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="fw-bold"><?= $r['nama'] ?? 'User Dihapus' ?></td>
                        <td><?= $r['nama_alat'] ?? 'Alat Dihapus' ?></td>
                        <td class="text-center"><?= $r['tgl_pinjam'] ?></td>
                        <td class="text-center"><?= $r['tgl_kembali'] ?? '-' ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?= ($r['status']=='pending') ? 'warning' : (($r['status']=='disetujui') ? 'primary' : 'success') ?> text-dark">
                                <?= strtoupper($r['status']) ?>
                            </span>
                        </td>
                        <td class="text-center btn-action">
                            <a href="?hapus=<?= $r['id_peminjaman'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data transaksi ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-none d-print-block">
            <div class="row text-center">
                <div class="col-8"></div>
                <div class="col-4">
                    <p>Dicetak pada: <?= date('d/m/Y') ?></p>
                    <br><br><br>
                    <p class="fw-bold">( <?= $_SESSION['nama'] ?> )</p>
                    <p>Super Admin</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>