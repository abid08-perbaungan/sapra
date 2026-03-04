<?php 
include '../config/koneksi.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi: Hanya Admin (Petugas) dan Super Admin yang bisa masuk
if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')){ 
    header("Location: ../index.php"); 
    exit;
} 

// Ambil data statistik (Gunakan TRIM dan UPPER untuk menghindari bug typo status di DB)
$total_alat = mysqli_num_rows(mysqli_query($conn, "SELECT id_alat FROM alat"));
$total_pending = mysqli_num_rows(mysqli_query($conn, "SELECT id_peminjaman FROM peminjaman WHERE TRIM(UPPER(status))='PENDING' OR TRIM(UPPER(status))='MENUNGGU'"));
$total_pinjam = mysqli_num_rows(mysqli_query($conn, "SELECT id_peminjaman FROM peminjaman WHERE TRIM(UPPER(status))='DISETUJUI'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rental Pro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }

        .navbar-admin {
            background: #2d3436;
            border-bottom: 4px solid #764ba2;
        }

        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.2);
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            padding: 20px;
            transition: 0.3s;
            background: white;
            display: flex;
            align-items: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }

        .menu-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: white;
            transition: 0.3s;
            height: 100%;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .btn-menu {
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 20px;
        }

        .badge-role {
            font-size: 0.7rem;
            vertical-align: middle;
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin px-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-shield-lock me-2"></i>ADMIN PANEL</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active fw-bold" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm px-3 mt-1">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        
        <div class="welcome-section d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Halo, <?= $_SESSION['nama'] ?>! 👋 <span class="badge-role"><?= $_SESSION['role'] ?></span></h2>
                <p class="mb-0 opacity-75">Sistem Manajemen Rental - Pantau data dan transaksi dengan mudah.</p>
            </div>
            <i class="bi bi-person-badge fs-1 opacity-25"></i>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card shadow-sm border-start border-primary border-4">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-box"></i></div>
                    <div>
                        <small class="text-muted d-block">Total Alat</small>
                        <h4 class="fw-bold mb-0"><?= $total_alat ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card shadow-sm border-start border-warning border-4">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <small class="text-muted d-block">Request Pending</small>
                        <h4 class="fw-bold mb-0"><?= $total_pending ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card shadow-sm border-start border-success border-4">
                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check2-all"></i></div>
                    <div>
                        <small class="text-muted d-block">Sedang Dipinjam</small>
                        <h4 class="fw-bold mb-0"><?= $total_pinjam ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card menu-card shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 text-primary">
                            <i class="bi bi-tools" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Manajemen Alat</h4>
                        <p class="text-muted px-3">Atur katalog alat, update stok, tambah alat baru, atau hapus data alat.</p>
                        <a href="kelola_alat.php" class="btn btn-primary btn-menu w-75 shadow-sm">
                            <i class="bi bi-gear-fill me-2"></i>Buka Manajemen
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card menu-card shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 text-warning">
                            <i class="bi bi-clipboard-check" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Persetujuan & Kembali</h4>
                        <p class="text-muted px-3">Verifikasi permohonan pinjam dan konfirmasi pengembalian barang user.</p>
                        <a href="konfirmasi_pinjam.php" class="btn btn-warning btn-menu w-75 shadow-sm">
                            <i class="bi bi-check-lg me-2"></i>Buka Konfirmasi
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card menu-card shadow-sm border-bottom border-success border-4">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 text-success">
                            <i class="bi bi-printer" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Laporan Transaksi</h4>
                        <p class="text-muted px-3">Cetak rekapitulasi data peminjaman bulanan ke format PDF untuk dosen.</p>
                        <a href="laporan.php" class="btn btn-success btn-menu w-75 shadow-sm">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Cetak Laporan
                        </a>
                    </div>
                </div>
            </div>

            <?php if($_SESSION['role'] == 'super_admin'): ?>
            <div class="col-md-6">
                <div class="card menu-card shadow-sm border-bottom border-info border-4">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 text-info">
                            <i class="bi bi-tags" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Kategori Alat</h4>
                        <p class="text-muted px-3">Fitur Super Admin: Kelola kategori barang (Kamera, Tenda, Lighting, dll).</p>
                        <a href="kategori.php" class="btn btn-info text-white btn-menu w-75 shadow-sm">
                            <i class="bi bi-plus-circle me-2"></i>Kelola Kategori
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>