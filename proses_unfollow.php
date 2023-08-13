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

    $sql_unfollow = "DELETE FROM Pengikut WHERE follower_id = $user_id AND following_id = $following_id";

    if ($koneksi->query($sql_unfollow) === TRUE) {
        header("Location: profil.php?user_id=$following_id");
    } else {
        echo "Error: " . $sql_unfollow . "<br>" . $koneksi->error;
    }
}

$koneksi->close();
?>
