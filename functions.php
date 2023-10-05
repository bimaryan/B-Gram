<?php
// functions.php
function getUserData($user_id) {
    include 'koneksi.php';
    
    $sql_user = "SELECT * FROM pengguna WHERE id = $user_id";
    $result_user = $koneksi->query($sql_user);
    $user_data = $result_user->fetch_assoc();
    
    return $user_data;
}

// Mengambil jumlah pengikut
function getFollowersCount($user_id) {
    include 'koneksi.php';
    
    $sql_followers_count = "SELECT COUNT(*) AS followers_count FROM pengikut WHERE following_id = $user_id";
    $result_followers_count = $koneksi->query($sql_followers_count);
    $row = $result_followers_count->fetch_assoc();
    
    return $row['followers_count'];
}

// Mengambil jumlah yang diikuti
function getFollowingCount($user_id) {
    include 'koneksi.php';
    
    $sql_following_count = "SELECT COUNT(*) AS following_count FROM pengikut WHERE follower_id = $user_id";
    $result_following_count = $koneksi->query($sql_following_count);
    $row = $result_following_count->fetch_assoc();
    
    return $row['following_count'];
}
function getLikesCount($post_id) {
    include 'koneksi.php';
    global $koneksi;
    $sql = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = $post_id";
    $result = $koneksi->query($sql);
    $row = $result->fetch_assoc();
    return $row['like_count'];
}

function getPostsCount($user_id) {
    include 'koneksi.php'; // Include your database connection code

    $sql = "SELECT COUNT(*) AS count FROM posting WHERE user_id = $user_id";
    $result = $koneksi->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    return 0;
}

function getFollowersList($user_id) {
    global $koneksi;
    $followers = array();

    $sql = "SELECT pengguna.id, pengguna.username, pengguna.profile_picture FROM pengguna
            INNER JOIN pengikut ON pengikut.follower_id = pengguna.id
            WHERE pengikut.following_id = $user_id";
            
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $followers[] = $row;
        }
    }

    return $followers;
}

function getFollowingList($user_id) {
    global $koneksi;
    $following = array();

    $sql = "SELECT pengguna.id, pengguna.username, pengguna.profile_picture FROM pengguna
            INNER JOIN pengikut ON pengikut.following_id = pengguna.id
            WHERE pengikut.follower_id = $user_id";
            
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $following[] = $row;
        }
    }

    return $following;
}

?>
