<?php
$conn = mysqli_connect("localhost", "root", "", "peminjaman_alat");
if (!$conn) { die("Gagal Konek: " . mysqli_connect_error()); }
session_start();
?>