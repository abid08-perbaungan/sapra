<?php 
include '../config/koneksi.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if($_SESSION['role'] != 'super_admin'){ 
    header("Location: ../index.php"); 
    exit;
} 

// Ambil data statistik untuk Super Admin
$count_admin = mysqli_num_rows(mysqli_query($conn, "SELECT id_user FROM users WHERE role='admin'"));
$count_peminjam = mysqli_num_rows(mysqli_query($conn, "SELECT id_user FROM users WHERE role='peminjam'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Panel - Rental Pro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a; /* Deep Navy Dark */
            color: #f8fafc;
        }

        .navbar-super {
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 1rem 2rem;
        }

        .hero-section {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid #334155;
            border-radius: 24px;
            padding: 40px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 25px;
            transition: 0.3s ease;
        }

        .stat-card:hover {
            border-color: #fbbf24; /* Amber Gold */
            transform: translateY(-5px);
        }

        .menu-action-card {
            background: #1e293b;
            border: none;
            border-radius: 24px;
            overflow: hidden;
            transition: 0.4s;
        }

        .menu-action-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            background: #334155;
        }

        .btn-gold {
            background-color: #fbbf24;
            color: #000;
            font-weight: 800;
            border-radius: 12px;
            padding: 12px 30px;
            border: none;
        }

        .btn-gold:hover {
            background-color: #f59e0b;
            color: #000;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: rgba(251, 191, 36, 0.1);
            color: #fbbf24;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-super sticky-top">
        <div class="container">
            <a class="navbar-brand fw-extrabold d-flex align-items-center" href="#">
                <i class="bi bi-shield-shaded text-warning me-2"></i> SUPER ADMIN
            </a>
            <div class="ms-auto">
                <a href="../logout.php" class="btn btn-outline-danger btn-sm px-4 fw-bold" style="border-radius: 10px;">
                    <i class="bi bi-power me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        
        <div class="hero-section shadow-lg">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="text-warning fw-bold mb-2">SISTEM KENDALI UTAMA</h5>
                    <h1 class="display-5 fw-bold mb-3">Selamat Datang, Bos <?= $_SESSION['nama'] ?></h1>
                    <p class="text-secondary fs-5">Anda memiliki otoritas penuh untuk mengatur akses administrator dan memantau seluruh aktivitas pengguna di sistem Rental Pro.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <i class="bi bi-rocket-takeoff text-warning opacity-50" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6 mb-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box me-4">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0"><?= $count_admin ?></h2>
                        <span class="text-secondary fw-semibold">Total Staff Admin</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="icon-box me-4" style="color: #60a5fa; background: rgba(96, 165, 250, 0.1);">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0"><?= $count_peminjam ?></h2>
                        <span class="text-secondary fw-semibold">Total Member (Peminjam)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card menu-action-card shadow">
                    <div class="card-body p-5 text-center">
                        <div class="icon-box mx-auto mb-4" style="width: 80px; height: 80px; font-size: 40px;">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <h3 class="fw-bold mb-3">Kelola Akun Admin</h3>
                        <p class="text-secondary mb-4 px-lg-5">
                            Tambahkan admin baru, nonaktifkan akses, atau ubah kredensial staff operasional Anda.
                        </p>
                        <a href="kelola_admin.php" class="btn btn-gold w-100 shadow-lg">
                            BUKA MANAJEMEN ADMIN <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>