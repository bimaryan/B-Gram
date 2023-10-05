<?php
$host = "localhost"; // Ganti dengan host database Anda
$username = "root"; // Ganti dengan nama pengguna database Anda
$password = ""; // Ganti dengan kata sandi database Anda
$database = "if0_34806348_bgramm"; // Ganti dengan nama basis data Anda

// Membuat koneksi ke database
$koneksi = new mysqli($host, $username, $password, $database);

// Memeriksa apakah terjadi error dalam koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set karakter encoding
$koneksi->set_charset("utf8");
?>
