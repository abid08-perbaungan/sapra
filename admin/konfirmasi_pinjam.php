<?php 
include '../config/koneksi.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if($_SESSION['role'] != 'admin'){ header("Location: ../index.php"); exit; }

// PROSES TERIMA BARANG (DENGAN DENDA)
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
        $days = (new DateTime($tgl_sekarang))->diff(new DateTime($tgl_deadline))->days;
        $denda_telat = $days * 10000;
    }

    mysqli_query($conn, "UPDATE peminjaman SET status='kembali' WHERE id_peminjaman='$id'");
    mysqli_query($conn, "UPDATE alat SET stok = stok + $jumlah_kembali WHERE id_alat='$id_alat'");
    mysqli_query($conn, "INSERT INTO pengembalian (id_peminjaman, tgl_kembali_real, denda, denda_rusak) 
                         VALUES ('$id', '$tgl_sekarang', '$denda_telat', '$denda_rusak')");
    
    header("Location: konfirmasi_pinjam.php?msg=success_return");
}

// PROSES SETUJUI PEMINJAMAN
if(isset($_GET['aksi']) && $_GET['aksi'] == 'setujui'){
    $id = $_GET['id'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman='$id'"));
    $jml = $cek['jumlah'];
    $alat = $cek['id_alat'];
    
    mysqli_query($conn, "UPDATE peminjaman SET status='disetujui' WHERE id_alat='$id'");
    mysqli_query($conn, "UPDATE alat SET stok = stok - $jml WHERE id_alat='$alat'");
    header("Location: konfirmasi_pinjam.php?msg=success_approve");
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
        .table thead { background-color: #f1f3f5; }
        .badge-status { font-size: 11px; text-transform: uppercase; padding: 6px 12px; border-radius: 50px; }
        .btn-action { border-radius: 8px; font-weight: 600; }
        .img-preview { width: 40px; height: 40px; object-fit: cover; border-radius: 8px; cursor: pointer; transition: 0.2s; }
        .img-preview:hover { transform: scale(1.1); }
    </style>
</head>
<body class="p-lg-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Verifikasi Transaksi</h3>
                <p class="text-muted">Kelola persetujuan pinjam dan validasi pengembalian alat.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-action shadow-sm">
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
                                <th>VALIDASI USER</th>
                                <th class="text-center">AKSI ADMIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = mysqli_query($conn, "SELECT p.*, u.nama, a.nama_alat, a.gambar as gbr_alat FROM peminjaman p 
                                                    JOIN users u ON p.id_user=u.id_user 
                                                    JOIN alat a ON p.id_alat=a.id_alat 
                                                    ORDER BY p.id_peminjaman DESC");
                            while($r = mysqli_fetch_array($q)): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= $r['nama'] ?></div>
                                    <small class="text-muted">ID: #PR-<?= $r['id_peminjaman'] ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../uploads/<?= $r['gbr_alat'] ?>" class="img-preview me-2 border">
                                        <span class="small fw-semibold"><?= $r['nama_alat'] ?></span>
                                    </div>
                                </td>
                                <td class="text-center fw-bold"><?= $r['jumlah'] ?></td>
                                <td>
                                    <?php 
                                        $badge = "bg-secondary";
                                        if($r['status'] == 'pending') $badge = "bg-warning text-dark";
                                        if($r['status'] == 'disetujui') $badge = "bg-primary";
                                        if($r['status'] == 'diajukan_kembali') $badge = "bg-info text-dark";
                                        if($r['status'] == 'kembali') $badge = "bg-success";
                                    ?>
                                    <span class="badge badge-status <?= $badge ?>"><?= str_replace('_', ' ', $r['status']) ?></span>
                                </td>
                                <td>
                                    <?php if($r['status'] == 'diajukan_kembali'): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="../uploads/<?= $r['foto_bukti'] ?>" target="_blank">
                                                <img src="../uploads/<?= $r['foto_bukti'] ?>" class="img-preview shadow-sm" title="Klik untuk perbesar">
                                            </a>
                                            <span class="badge <?= ($r['kondisi_kembali'] == 'aman') ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> border">
                                                <?= strtoupper($r['kondisi_kembali']) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <?php if($r['status'] == 'pending'): ?>
                                        <a href="?aksi=setujui&id=<?= $r['id_peminjaman'] ?>" class="btn btn-success btn-sm btn-action w-100 py-2">
                                            <i class="bi bi-check-circle me-1"></i> Setujui Pinjam
                                        </a>
                                    
                                    <?php elseif($r['status'] == 'diajukan_kembali'): ?>
                                        <form method="POST" class="d-flex gap-2 justify-content-center">
                                            <input type="hidden" name="id_peminjaman" value="<?= $r['id_peminjaman'] ?>">
                                            <div class="input-group input-group-sm" style="width: 180px;">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="denda_rusak" class="form-control" placeholder="Denda Rusak" value="0">
                                            </div>
                                            <button type="submit" name="konfirmasi_terima" class="btn btn-primary btn-sm btn-action">
                                                Konfirmasi
                                            </button>
                                        </form>
                                    
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="bi bi-check-all text-success"></i> Selesai</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>