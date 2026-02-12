<?php
include 'config/koneksi.php';

// Cek apakah session sudah jalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROSES LOGIN
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    // Query cek user & password (Sesuai database kamu yang TANPA HASH)
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Set Session
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        // Redirect sesuai Role (Pastikan tulisan role di DB sama)
        if ($data['role'] == 'super_admin') {
            header("Location: superadmin/dashboard.php");
        } elseif ($data['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } elseif ($data['role'] == 'peminjam') {
            header("Location: customer/dashboard.php");
        }
        exit;
    } else {
        $error_login = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Alat</title>
    
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        /* Animasi bulatan di background */
        .bg-circle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 40px;
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.3);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }
    </style>
</head>
<body>

    <div class="bg-circle" style="width: 300px; height: 300px; top: -100px; left: -100px;"></div>
    <div class="bg-circle" style="width: 200px; height: 200px; bottom: -50px; right: -50px;"></div>

    <div class="login-card">
        <div class="text-center">
            <div class="logo-icon"><i class="bi bi-person-lock"></i></div>
            <h3 class="fw-bold text-dark">Login System</h3>
            <p class="text-muted mb-4">Aplikasi Peminjaman Alat</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="******" required>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
            </button>
        </form>

        <div class="divider mt-4 mb-4 text-center">
            <hr>
            <span class="bg-white px-2 text-muted small">atau</span>
        </div>

        <div class="text-center">
            <p class="mb-2 small">Belum punya akun?</p>
            <a href="register.php" class="btn btn-outline-primary btn-sm w-100 fw-bold" style="border-radius: 10px;">
                Daftar Akun Baru
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if (isset($error_login)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Masuk',
            text: 'Username atau password salah!',
            confirmButtonColor: '#764ba2'
        });
    </script>
    <?php endif; ?>

</body>
</html>