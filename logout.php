<?php
session_start();

// Menghapus semua variabel sesi
session_unset();

// Menghancurkan sesi
session_destroy();

// Mengarahkan pengguna kembali ke halaman masuk atau halaman lain yang sesuai
header("Location: login.php");
exit();
?>
