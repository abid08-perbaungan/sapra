<?php
include '../config/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id = $_POST['id_peminjaman'];
    $kondisi = $_POST['kondisi_kembali'];
    
    // Upload Foto Bukti
    $foto = $_FILES['foto_bukti']['name'];
    $tmp = $_FILES['foto_bukti']['tmp_name'];
    $new_name = time() . "_" . $foto; // Rename biar unik
    $path = "../uploads/" . $new_name;
    
    if(move_uploaded_file($tmp, $path)){
        $sql = "UPDATE peminjaman SET status='diajukan_kembali', foto_bukti='$new_name', kondisi_kembali='$kondisi' WHERE id_peminjaman='$id'";
        if(mysqli_query($conn, $sql)){
            echo "<script>alert('Bukti terkirim! Menunggu pengecekan Admin.'); window.location='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Gagal upload foto!'); window.location='dashboard.php';</script>";
    }
}
?>