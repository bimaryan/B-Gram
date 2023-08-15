<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Pastikan postingan yang akan dihapus milik pengguna yang sedang login
    $sql_check_owner = "SELECT * FROM posting WHERE id = $post_id AND user_id = $user_id";
    $result_check_owner = $koneksi->query($sql_check_owner);

    $sql_delete_shares = "DELETE FROM shares WHERE post_id = $post_id";
    $koneksi->query($sql_delete_shares);

    if ($result_check_owner->num_rows > 0) {
        // Hapus likes yang terkait dengan postingan
        $sql_delete_likes = "DELETE FROM likes WHERE post_id = $post_id";
        if ($koneksi->query($sql_delete_likes) === TRUE) {
            // Setelah likes dihapus, baru hapus postingan
            $sql_delete_post = "DELETE FROM posting WHERE id = $post_id";
            if ($koneksi->query($sql_delete_post) === TRUE) {
                header("Location: profil.php?user_id=$user_id");
                exit();
            } else {
                echo "Error: " . $sql_delete_post . "<br>" . $koneksi->error;
            }
        } else {
            echo "Error: " . $sql_delete_likes . "<br>" . $koneksi->error;
        }
    } else {
        echo "Anda tidak memiliki izin untuk menghapus postingan ini.";
    }
}
?>
