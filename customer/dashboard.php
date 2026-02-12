<?php 
include '../config/koneksi.php'; 

// Cek session start jika belum ada di koneksi.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SESSION['role'] != 'peminjam'){ 
    header("Location: ../index.php"); 
    exit; 
} 

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Rental Pro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 2rem;
        }

        .card-alat {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-alat:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.5em 0.8em;
            border-radius: 50px;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .btn-action {
            border-radius: 8px;
            font-weight: 600;
        }

        .img-katalog {
            height: 180px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }

        .welcome-banner {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <i class="bi bi-tools me-2"></i> RENTAL PRO
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-inline small">Halo, <b><?= $nama_user ?></b></span>
                <a href="../logout.php" class="btn btn-light btn-sm fw-bold text-danger px-3 shadow-sm" style="border-radius: 8px;">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        
        <div class="welcome-banner shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Katalog Alat</h4>
                <p class="text-muted mb-0">Pilih alat yang ingin Anda sewa hari ini.</p>
            </div>
            <i class="bi bi-grid-3x3-gap text-secondary fs-2 opacity-25"></i>
        </div>

        <div class="row">
            <?php 
            $q = mysqli_query($conn, "SELECT * FROM alat");
            while($alat = mysqli_fetch_array($q)): 
                $is_ready = $alat['stok'] > 0;
            ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 card-alat shadow-sm">
                    <img src="../uploads/<?= $alat['gambar'] ?>" class="img-katalog" alt="Foto Alat">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-1"><?= $alat['nama_alat'] ?></h6>
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border small"><?= $alat['kondisi'] ?></span>
                            <span class="badge <?= $is_ready ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> small">
                                Stok: <?= $alat['stok'] ?>
                            </span>
                        </div>
                        
                        <?php if($is_ready): ?>
                            <button type="button" class="btn btn-primary btn-action mt-auto w-100" data-bs-toggle="modal" data-bs-target="#modalPinjam<?= $alat['id_alat'] ?>">
                                <i class="bi bi-plus-circle me-1"></i> Pinjam
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-action mt-auto w-100 disabled">Habis</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalPinjam<?= $alat['id_alat'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                        <div class="modal-header border-0 bg-warning" style="border-radius: 20px 20px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Syarat Peminjaman</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="pinjam_alat.php" method="POST">
                            <div class="modal-body p-4">
                                <div class="bg-light p-3 rounded-3 mb-3 border-start border-4 border-danger">
                                    <ul class="mb-0 small text-dark">
                                        <li>Jaga barang dengan penuh tanggung jawab.</li>
                                        <li>Kerusakan/Kehilangan = <b>Denda Kerusakan</b>.</li>
                                        <li>Terlambat = <b>Rp 10.000/hari</b>.</li>
                                    </ul>
                                </div>
                                <input type="hidden" name="id_alat" value="<?= $alat['id_alat'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Jumlah Pinjam (Maks: <?= $alat['stok'] ?>)</label>
                                    <input type="number" name="jumlah" class="form-control" min="1" max="<?= $alat['stok'] ?>" value="1" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small">Tanggal Pinjam</label>
                                        <input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-danger">Batas Kembali</label>
                                        <input type="date" name="tgl_kembali_rencana" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="proses_pinjam" class="btn btn-primary px-4 shadow">Setuju & Pinjam</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <h4 class="mt-5 mb-4 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i>Riwayat & Status</h4>
        <div class="table-container shadow-sm border mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr class="small text-uppercase fw-bold text-secondary">
                            <th class="border-0">Nama Alat</th>
                            <th class="border-0 text-center">Jml</th>
                            <th class="border-0">Deadline</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Denda</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT p.*, a.nama_alat, a.gambar, peng.denda_rusak, peng.denda 
                                FROM peminjaman p JOIN alat a ON p.id_alat = a.id_alat 
                                LEFT JOIN pengembalian peng ON p.id_peminjaman = peng.id_peminjaman
                                WHERE p.id_user='$id_user' ORDER BY p.id_peminjaman DESC";
                        $his = mysqli_query($conn, $sql);
                        while($h = mysqli_fetch_array($his)): 
                            $denda_live = 0;
                            if($h['status'] != 'kembali' && date('Y-m-d') > $h['tgl_kembali_rencana']){
                                $denda_live = (new DateTime(date('Y-m-d')))->diff(new DateTime($h['tgl_kembali_rencana']))->days * 10000;
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="../uploads/<?= $h['gambar'] ?>" width="45" height="45" class="rounded-3 shadow-sm me-3 object-fit-cover">
                                    <span class="fw-bold text-dark"><?= $h['nama_alat'] ?></span>
                                </div>
                            </td>
                            <td class="text-center fw-semibold"><?= $h['jumlah'] ?></td>
                            <td>
                                <span class="text-danger fw-bold small"><i class="bi bi-calendar-x me-1"></i><?= $h['tgl_kembali_rencana'] ?></span>
                            </td>
                            <td>
                                <?php 
                                    $badge = "bg-info text-dark";
                                    if($h['status'] == 'pending') $badge = "bg-warning text-dark";
                                    if($h['status'] == 'disetujui') $badge = "bg-success text-white";
                                    if($h['status'] == 'kembali') $badge = "bg-secondary text-white";
                                ?>
                                <span class="badge badge-status <?= $badge ?>"><?= strtoupper($h['status']) ?></span>
                            </td>
                            <td>
                                <span class="fw-bold text-danger">
                                    <?php if($h['status']=='kembali'): ?>
                                        Rp <?= number_format($h['denda'] + $h['denda_rusak']) ?>
                                    <?php else: ?>
                                        <?= ($denda_live > 0) ? "Rp ".number_format($denda_live) : "-" ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if($h['status'] == 'disetujui'): ?>
                                    <button class="btn btn-warning btn-sm btn-action px-3" data-bs-toggle="modal" data-bs-target="#modalKembali<?= $h['id_peminjaman'] ?>">
                                        <i class="bi bi-arrow-return-left me-1"></i> Kembali
                                    </button>

                                    <div class="modal fade" id="modalKembali<?= $h['id_peminjaman'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Form Pengembalian</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="proses_kembali.php" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-body p-4 text-start">
                                                        <input type="hidden" name="id_peminjaman" value="<?= $h['id_peminjaman'] ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Upload Bukti Foto Kondisi Alat:</label>
                                                            <input type="file" name="foto_bukti" class="form-control" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Pilih Kondisi Alat:</label>
                                                            <select name="kondisi_kembali" class="form-select border-2" required>
                                                                <option value="aman">Aman / Normal</option>
                                                                <option value="rusak">Terdapat Kerusakan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                        <button type="submit" class="btn btn-success btn-action w-100 py-2 shadow-sm">Kirim & Selesaikan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small italic">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>     