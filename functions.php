<?php
// functions.php

function getUserData($user_id) {
    include 'koneksi.php';
    
    $sql_user = "SELECT * FROM Pengguna WHERE id = $user_id";
    $result_user = $koneksi->query($sql_user);
    $user_data = $result_user->fetch_assoc();
    
    return $user_data;
}

// Mengambil jumlah pengikut
function getFollowersCount($user_id) {
    include 'koneksi.php';
    
    $sql_followers_count = "SELECT COUNT(*) AS followers_count FROM Pengikut WHERE following_id = $user_id";
    $result_followers_count = $koneksi->query($sql_followers_count);
    $row = $result_followers_count->fetch_assoc();
    
    return $row['followers_count'];
}

// Mengambil jumlah yang diikuti
function getFollowingCount($user_id) {
    include 'koneksi.php';
    
    $sql_following_count = "SELECT COUNT(*) AS following_count FROM Pengikut WHERE follower_id = $user_id";
    $result_following_count = $koneksi->query($sql_following_count);
    $row = $result_following_count->fetch_assoc();
    
    return $row['following_count'];
}
function getLikesCount($post_id) {
    global $koneksi;
    $sql = "SELECT COUNT(*) AS like_count FROM Likes WHERE post_id = $post_id";
    $result = $koneksi->query($sql);
    $row = $result->fetch_assoc();
    return $row['like_count'];
}
?>
