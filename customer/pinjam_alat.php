<?php
include '../config/koneksi.php';

if(isset($_POST['proses_pinjam'])){
    $id_alat = $_POST['id_alat'];
    $id_user = $_SESSION['id_user'];
    $jumlah = $_POST['jumlah'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali_rencana = $_POST['tgl_kembali_rencana'];

    // Cek Stok Valid
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM alat WHERE id_alat='$id_alat'"));
    if($jumlah > $cek['stok']){
        echo "<script>alert('Stok tidak cukup!'); window.location='dashboard.php';</script>";
        exit;
    }

    if($tgl_kembali_rencana < $tgl_pinjam){
        echo "<script>alert('Tanggal salah!'); window.location='dashboard.php';</script>";
        exit;
    }

    $q = "INSERT INTO peminjaman (id_user, id_alat, tgl_pinjam, tgl_kembali_rencana, status, jumlah) 
          VALUES ('$id_user', '$id_alat', '$tgl_pinjam', '$tgl_kembali_rencana', 'menunggu', '$jumlah')";
    
    if(mysqli_query($conn, $q)){
        echo "<script>alert('Peminjaman diajukan!'); window.location='dashboard.php';</script>";
    }
}
?>