<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: index.php");
    exit();
}

include 'koneksi.php';

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];
$action = $_POST['action'];

if ($action === 'like') {
    $sql_insert_like = "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)";
    $koneksi->query($sql_insert_like);
} elseif ($action === 'unlike') {
    $sql_delete_like = "DELETE FROM likes WHERE user_id = $user_id AND post_id = $post_id";
    $koneksi->query($sql_delete_like);
}

// Get updated like count
$sql_likes_count = "SELECT COUNT(*) AS count FROM likes WHERE post_id = $post_id";
$result_likes_count = $koneksi->query($sql_likes_count);
$likes_count = $result_likes_count->fetch_assoc()['count'];

$response = array(
    'likes' => $likes_count,
    'button' => ($action === '<i class="bi bi-hand-thumbs-up-fill"></i>') ? '<button onclick="unlikePost(' . $post_id . ')" class="text-red-500"><i class="bi bi-hand-thumbs-up"></i></button>' : '<button onclick="likePost(' . $post_id . ')" class="text-blue-500"><i class="bi bi-hand-thumbs-up-fill"></i></button>'
);

echo json_encode($response);
