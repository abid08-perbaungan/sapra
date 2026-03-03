<?php 
include '../config/koneksi.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){ 
    header("Location: ../index.php"); 
    exit; 
}

// 1. PROSES SETUJUI (Dari MENUNGGU ke DIPINJAM)
if(isset($_GET['aksi']) && $_GET['aksi'] == 'setujui'){
    $id = $_GET['id'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman='$id'"));
    $jml = $cek['jumlah'];
    $alat = $cek['id_alat'];
    
    // Kita set ke 'disetujui' (atau sesuaikan dengan string di DB kamu)
    mysqli_query($conn, "UPDATE peminjaman SET status='disetujui' WHERE id_peminjaman='$id'");
    mysqli_query($conn, "UPDATE alat SET stok = stok - $jml WHERE id_alat='$alat'");
    
    header("Location: konfirmasi_pinjam.php?msg=approved");
    exit;
}

// 2. PROSES TERIMA (Dari DIAJUKAN_KEMBALI ke KEMBALI)
if(isset($_POST['konfirmasi_terima'])){
    $id = $_POST['id_peminjaman'];
    $denda_rusak = $_POST['denda_rusak']; 
    
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman='$id'"));
    $id_alat = $data['id_alat'];
    $jumlah_kembali = $data['jumlah'];
    
    $tgl_deadline = $data['tgl_kembali_rencana'];
    $tgl_sekarang = date('Y-m-d');
    $denda_telat = 0;
    
    if($tgl_sekarang > $tgl_deadline){
        $tgl1 = new DateTime($tgl_deadline);
        $tgl2 = new DateTime($tgl_sekarang);
        $jarak = $tgl2->diff($tgl1);
        $denda_telat = $jarak->days * 10000;
    }

    mysqli_query($conn, "UPDATE peminjaman SET status='kembali' WHERE id_peminjaman='$id'");
    mysqli_query($conn, "UPDATE alat SET stok = stok + $jumlah_kembali WHERE id_alat='$id_alat'");
    mysqli_query($conn, "INSERT INTO pengembalian (id_peminjaman, tgl_kembali_real, denda, denda_rusak) 
                         VALUES ('$id', '$tgl_sekarang', '$denda_telat', '$denda_rusak')");
    
    header("Location: konfirmasi_pinjam.php?msg=returned");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Transaksi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .badge-status { font-size: 11px; text-transform: uppercase; padding: 6px 12px; border-radius: 50px; }
        .img-preview { width: 45px; height: 45px; object-fit: cover; border-radius: 8px; border: 1px solid #dee2e6; }
    </style>
</head>
<body class="p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Verifikasi Transaksi</h3>
                <p class="text-muted">Kelola persetujuan pinjam dan konfirmasi barang kembali.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
        </div>

        <div class="card main-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted fw-bold">
                                <th class="ps-4">PEMINJAM</th>
                                <th>ALAT</th>
                                <th class="text-center">QTY</th>
                                <th>STATUS</th>
                                <th>FOTO BUKTI</th>
                                <th class="text-center">AKSI ADMIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = mysqli_query($conn, "SELECT p.*, u.nama, a.nama_alat, a.gambar as gbr_alat FROM peminjaman p 
                                                    JOIN users u ON p.id_user=u.id_user 
                                                    JOIN alat a ON p.id_alat=a.id_alat 
                                                    ORDER BY p.id_peminjaman DESC");
                            while($r = mysqli_fetch_array($q)): 
                                // NORMALISASI STATUS (Ubah ke huruf kecil semua untuk pengecekan)
                                $status_cek = strtolower($r['status']);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= $r['nama'] ?></div>
                                    <small class="text-muted">ID: #INV-<?= $r['id_peminjaman'] ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../uploads/<?= $r['gbr_alat'] ?>" class="img-preview me-2">
                                        <span class="small fw-semibold"><?= $r['nama_alat'] ?></span>
                                    </div>
                                </td>
                                <td class="text-center fw-bold"><?= $r['jumlah'] ?></td>
                                <td>
                                    <?php 
                                        $badge = "bg-secondary";
                                        if($status_cek == 'menunggu' || $status_cek == 'pending') $badge = "bg-warning text-dark";
                                        if($status_cek == 'disetujui') $badge = "bg-primary";
                                        if($status_cek == 'diajukan_kembali') $badge = "bg-info text-dark";
                                        if($status_cek == 'kembali' || $status_cek == 'selesai') $badge = "bg-success";
                                    ?>
                                    <span class="badge badge-status <?= $badge ?>"><?= $r['status'] ?></span>
                                </td>
                                <td>
                                    <?php if($r['foto_bukti']): ?>
                                        <a href="../uploads/<?= $r['foto_bukti'] ?>" target="_blank">
                                            <img src="../uploads/<?= $r['foto_bukti'] ?>" class="img-preview shadow-sm">
                                        </a>
                                        <span class="badge bg-light text-dark border ms-1"><?= strtoupper($r['kondisi_kembali']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <?php if($status_cek == 'menunggu' || $status_cek == 'pending'): ?>
                                        <a href="?aksi=setujui&id=<?= $r['id_peminjaman'] ?>" class="btn btn-success btn-sm px-3 fw-bold shadow-sm">
                                            <i class="bi bi-check2-circle me-1"></i> Setujui
                                        </a>
                                    
                                    <?php elseif($status_cek == 'disetujui'): ?>
                                        <small class="text-muted italic"><i class="bi bi-truck me-1"></i> Sedang Dipinjam</small>

                                    <?php elseif($status_cek == 'diajukan_kembali'): ?>
                                        <form method="POST" class="d-flex gap-1 justify-content-center">
                                            <input type="hidden" name="id_peminjaman" value="<?= $r['id_peminjaman'] ?>">
                                            <input type="number" name="denda_rusak" class="form-control form-control-sm" placeholder="Denda" value="0" style="width: 80px;">
                                            <button type="submit" name="konfirmasi_terima" class="btn btn-primary btn-sm fw-bold">Terima</button>
                                        </form>
                                    
                                    <?php else: ?>
                                        <i class="bi bi-check-all text-success fs-5"></i>
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