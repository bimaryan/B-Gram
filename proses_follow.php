<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['user_id'])) {
    $following_id = $_GET['user_id'];

    $sql_follow = "INSERT INTO Pengikut (follower_id, following_id) VALUES ($user_id, $following_id)";

    if ($koneksi->query($sql_follow) === TRUE) {
        header("Location: profil.php?user_id=$following_id");
    } else {
        echo "Error: " . $sql_follow . "<br>" . $koneksi->error;
    }
}

$koneksi->close();
?>
