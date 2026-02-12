<?php
include '../config/koneksi.php';

if(isset($_POST['simpan'])){
    $nama = $_POST['nama_alat'];
    $kategori = $_POST['id_kategori'];
    $stok = $_POST['stok'];
    $kondisi = $_POST['kondisi']; // Baru, Baik, Rusak
    
    // Upload Gambar
    $foto = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $path = "../uploads/" . $foto;
    
    if(move_uploaded_file($tmp, $path)){
        $q = "INSERT INTO alat (nama_alat, id_kategori, stok, kondisi, gambar) VALUES ('$nama', '$kategori', '$stok', '$kondisi', '$foto')";
        mysqli_query($conn, $q);
        echo "<script>alert('Alat Berhasil Ditambah!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal Upload Gambar');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Tambah Alat</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></head>
<body class="p-5">
    <h3>Tambah Data Alat</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nama_alat" class="form-control mb-2" placeholder="Nama Alat" required>
        <select name="id_kategori" class="form-control mb-2">
            <option value="1">Elektronik</option>
            <option value="2">Berat</option>
        </select>
        <input type="number" name="stok" class="form-control mb-2" placeholder="Stok Awal" required>
        <input type="text" name="kondisi" class="form-control mb-2" placeholder="Kondisi (Baik/Baru)" required>
        <label>Foto Alat:</label>
        <input type="file" name="gambar" class="form-control mb-2" required>
        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
    </form>
</body>
</html>