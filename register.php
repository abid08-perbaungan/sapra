<?php
include 'config/koneksi.php';

if (isset($_POST['daftar'])) {
    // Ambil data dan amankan dari SQL Injection
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $role = 'peminjam'; // Default user baru pasti peminjam

    // 1. Cek apakah username sudah ada
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $error_msg = "Username sudah terpakai, cari yang lain!";
    } else {
        // 2. Masukkan data ke database (Plain Text sesuai instruksi kamu)
        $query = "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')";
        
        if (mysqli_query($conn, $query)) {
            $success_reg = true;
        } else {
            $error_msg = "Gagal mendaftar ke server!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - Peminjaman Alat</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        /* Hiasan background konsisten dengan index.php */
        .bg-circle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .register-card {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            z-index: 1;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); /* Warna hijau sukses */
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 35px;
            box-shadow: 0 10px 20px rgba(56, 249, 215, 0.3);
        }

        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            color: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
        }
    </style>
</head>
<body>

    <div class="bg-circle" style="width: 250px; height: 250px; top: -50px; right: -50px;"></div>
    <div class="bg-circle" style="width: 150px; height: 150px; bottom: 20px; left: 20px;"></div>

    <div class="register-card">
        <div class="text-center mb-4">
            <div class="logo-icon"><i class="bi bi-person-plus-fill"></i></div>
            <h3 class="fw-bold text-dark">Daftar Akun</h3>
            <p class="text-muted small">Lengkapi data untuk meminjam alat</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-card-text"></i></span>
                    <input type="text" name="nama" class="form-control border-start-0 ps-0" placeholder="Contoh: Budi Santoso" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Buat username unik" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" name="daftar" class="btn btn-register w-100 mb-3">
                <i class="bi bi-check-circle me-1"></i> Daftar Sekarang
            </button>
            
            <a href="index.php" class="btn btn-light w-100 py-2 border" style="border-radius: 12px; font-size: 0.9rem;">
                Kembali ke Login
            </a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if (isset($error_msg)): ?>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Pendaftaran Gagal',
            text: '<?= $error_msg ?>',
            confirmButtonColor: '#764ba2'
        });
    </script>
    <?php endif; ?>

    <?php if (isset($success_reg)): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Registrasi Berhasil!',
            text: 'Akun Anda sudah terdaftar. Silakan login.',
            confirmButtonColor: '#43e97b'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'index.php';
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>