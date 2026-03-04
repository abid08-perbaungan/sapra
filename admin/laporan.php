<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Proteksi Petugas & Super Admin
if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')){ 
    header("Location: ../index.php"); exit;
} 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { background-color: white; }
        }
    </style>
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-5 shadow-sm">
        <div class="text-center mb-4">
            <h2 class="fw-bold">LAPORAN PEMINJAMAN ALAT</h2>
            <p class="text-muted">Sistem Informasi Rental Pro - Periode: <?= date('F Y') ?></p>
            <hr>
        </div>

        <div class="card mb-4 no-print border-0 bg-light">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold">Dari Tanggal</label>
                        <input type="date" name="tgl_awal" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">Sampai Tanggal</label>
                        <input type="date" name="tgl_akhir" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <button type="button" onclick="window.print()" class="btn btn-success">Cetak PDF</button>
                        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>Nama Alat</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $where = "";
                if(isset($_GET['tgl_awal']) && isset($_GET['tgl_akhir'])){
                    $tgl_a = $_GET['tgl_awal'];
                    $tgl_b = $_GET['tgl_akhir'];
                    $where = " WHERE tgl_pinjam BETWEEN '$tgl_a' AND '$tgl_b'";
                }

                $q = mysqli_query($conn, "SELECT p.*, u.nama, a.nama_alat FROM peminjaman p 
                                          JOIN users u ON p.id_user = u.id_user 
                                          JOIN alat a ON p.id_alat = a.id_alat $where");
                while($r = mysqli_fetch_array($q)): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $r['nama'] ?></td>
                    <td><?= $r['nama_alat'] ?></td>
                    <td class="text-center"><?= $r['tgl_pinjam'] ?></td>
                    <td class="text-center"><?= $r['tgl_kembali'] ?? '-' ?></td>
                    <td class="text-center"><?= strtoupper($r['status']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Dicetak pada: <?= date('d/m/Y') ?></p>
                <br><br><br>
                <p class="fw-bold">( <?= $_SESSION['nama'] ?> )</p>
                <p class="small text-muted"><?= strtoupper($_SESSION['role']) ?></p>
            </div>
        </div>
    </div>
</body>
</html>