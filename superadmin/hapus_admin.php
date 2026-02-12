<?php
include '../config/koneksi.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");
header("Location: kelola_admin.php");
?>