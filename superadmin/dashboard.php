<?php 
include '../config/koneksi.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Proteksi: Benar-benar CUMA boleh Super Admin
if($_SESSION['role'] != 'super_admin'){ 
    header("Location: ../index.php"); 
    exit;
} 

// Statistik
$total_alat = mysqli_num_rows(mysqli_query($conn, "SELECT id_alat FROM alat"));
$total_user = mysqli_num_rows(mysqli_query($conn, "SELECT id_user FROM users"));
$total_pinjam = mysqli_num_rows(mysqli_query($conn, "SELECT id_peminjaman FROM peminjaman WHERE status='disetujui'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Super Admin Panel - Rental Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }
        .card-menu { transition: 0.3s; border: none; border-radius: 15px; }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4 mb-4">
        <a class="navbar-brand fw-bold" href="#">SUPER ADMIN PANEL</a>
        <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </nav>

    <div class="container">
        <div class="alert alert-dark shadow-sm">
            <h4 class="fw-bold mb-1">Selamat Datang, Master <?= $_SESSION['nama'] ?>!</h4>
            <p class="mb-0">Anda memiliki akses penuh ke seluruh fitur sistem.</p>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card card-menu h-100 shadow-sm text-center p-4">
                    <i class="bi bi-tools text-primary fs-1 mb-2"></i>
                    <h5 class="fw-bold">Data Alat</h5>
                    <p class="small text-muted">Manajemen stok dan katalog barang.</p>
                    <a href="kelola_alat.php" class="btn btn-primary btn-sm mt-auto">Buka</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-menu h-100 shadow-sm text-center p-4 border-bottom border-info border-4">
                    <i class="bi bi-tags text-info fs-1 mb-2"></i>
                    <h5 class="fw-bold">Kategori</h5>
                    <p class="small text-muted">Atur kategori alat (Kamera, Tenda, dll).</p>
                    <a href="kategori.php" class="btn btn-info text-white btn-sm mt-auto">Buka</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-menu h-100 shadow-sm text-center p-4 border-bottom border-danger border-4">
                    <i class="bi bi-people text-danger fs-1 mb-2"></i>
                    <h5 class="fw-bold">Manajemen User</h5>
                    <p class="small text-muted">Kelola akun Petugas & Peminjam.</p>
                    <a href="users.php" class="btn btn-danger btn-sm mt-auto">Buka</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-row align-items-center">
                    <i class="bi bi-check2-circle text-warning fs-1 me-4"></i>
                    <div>
                        <h5 class="fw-bold">Persetujuan & Pengembalian</h5>
                        <a href="konfirmasi_pinjam.php" class="btn btn-warning btn-sm">Lihat Transaksi</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-menu h-100 shadow-sm p-4 d-flex flex-row align-items-center">
                    <i class="bi bi-file-earmark-pdf text-success fs-1 me-4"></i>
                    <div>
                        <h5 class="fw-bold">Cetak Laporan</h5>
                        <a href="laporan.php" class="btn btn-success btn-sm">Buka Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>