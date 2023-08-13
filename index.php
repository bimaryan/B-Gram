<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';
include 'functions.php';

$user_id = $_SESSION['user_id'];

if (isset($_POST['like']) || isset($_POST['unlike'])) {
    $post_id = $_POST['post_id'];

    if (isset($_POST['like'])) {
        $sql_insert_like = "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)";
        $koneksi->query($sql_insert_like);
    } elseif (isset($_POST['unlike'])) {
        $sql_delete_like = "DELETE FROM likes WHERE user_id = $user_id AND post_id = $post_id";
        $koneksi->query($sql_delete_like);
    }
}

if (isset($_POST['update_profile_picture'])) {
    $target_dir = "profil/"; // Direktori untuk menyimpan foto profil
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Izinkan hanya format gambar tertentu (misalnya jpg, png)
    $allowedFormats = array("jpg", "png");
    if (in_array($imageFileType, $allowedFormats)) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $new_profile_picture = $target_file;

            // Update data profil pengguna dengan foto baru
            $sql_update_profile_picture = "UPDATE pengguna SET profile_picture = '$new_profile_picture' WHERE id = $user_id";
            if ($koneksi->query($sql_update_profile_picture) === TRUE) {
                echo "Foto profil berhasil diubah!";
            } else {
                echo "Error: " . $sql_update_profile_picture . "<br>" . $koneksi->error;
            }
        } else {
            echo "Gagal mengunggah foto profil.";
        }
    } else {
        echo "Hanya format JPG dan PNG yang diizinkan.";
    }
}

// Mengambil informasi pengguna
$sql_user = "SELECT * FROM pengguna WHERE id = $user_id";
$result_user = $koneksi->query($sql_user);
$user_data = $result_user->fetch_assoc();

// Mengambil posting pengguna dan pengguna lain
$sql_post = "SELECT * FROM posting ORDER BY created_at DESC";
$result_post = $koneksi->query($sql_post);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <nav class="bg-white py-4">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="./" class="text-2xl font-semibold" style="text-decoration: none;">B-Gram</a>
            <div class="flex space-x-4">
                <a href="profil.php?user_id=<?php echo $user_id; ?>" class="text-blue-500 nav-link">Profil</a>
                <a href="search.php" class="text-blue-500 nav-link">Pencarian Akun</a>
                <a href="logout.php" class="text-blue-500 nav-link">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-3">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($post = $result_post->fetch_assoc()) : ?>
                <div class="bg-white p-4 shadow-md">
                    <div class="flex items-center mb-2">
                        <img src="<?php echo $post['image_path']; ?>" alt="Postingan" class="w-10 h-10 rounded-full">
                        <?php
                        $posted_user_id = $post['user_id'];
                        $posted_user_data = getUserData($posted_user_id);
                        ?>
                        <a href="profil.php?user_id=<?php echo $post['user_id']; ?>" class="ml-2 font-semibold" style="text-decoration: none;"><?php echo $posted_user_data['username']; ?></a>
                    </div>
                    <div class="relative">
                        <?php if (pathinfo($post['image_path'], PATHINFO_EXTENSION) === 'mp4' || pathinfo($post['image_path'], PATHINFO_EXTENSION) === 'MOV') : ?>
                            <video id="video-<?php echo $post['id']; ?>" src="<?php echo $post['image_path']; ?>" alt="Postingan" class="w-full h-64 object-cover"></video>
                            <div class="absolute inset-0 flex items-center justify-center" id="video-overlay-<?php echo $post['id']; ?>">
                                <button class="text-white bg-black bg-opacity-50 p-2 rounded-full" id="play-btn-<?php echo $post['id']; ?>" onclick="toggleVideoPlayback(<?php echo $post['id']; ?>)">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"></path>
                                    </svg>
                                </button>
                                <button class="text-white bg-black bg-opacity-50 p-2 rounded-full hidden" id="pause-btn-<?php echo $post['id']; ?>" onclick="toggleVideoPlayback(<?php echo $post['id']; ?>)">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"></path>
                                    </svg>
                                </button>
                            </div>
                        <?php else : ?>
                            <img src="<?php echo $post['image_path']; ?>" alt="Postingan" class="w-full h-64 object-cover">
                        <?php endif; ?>
                    </div>
                    <!-- <img src="<?php echo $post['image_path']; ?>" alt="Postingan" class="w-full h-64 object-cover"> -->
                    <p class="mt-2"><?php echo $post['caption']; ?></p>

                    <!-- Tombol Like -->
                    <form method="post" action="">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <?php
                        $sql_check_like = "SELECT * FROM Likes WHERE user_id = $user_id AND post_id = " . $post['id'];
                        $result_check_like = $koneksi->query($sql_check_like);
                        $is_liked = $result_check_like->num_rows > 0;
                        ?>
                        <div id="like-container-<?php echo $post['id']; ?>">
                            <?php if ($is_liked) : ?>
                                <button onclick="unlikePost(<?php echo $post['id']; ?>)" class="text-red-500">Unlike</button>
                            <?php else : ?>
                                <button onclick="likePost(<?php echo $post['id']; ?>)" class="text-blue-500">Like</button>
                            <?php endif; ?>
                            <p id="like-count-<?php echo $post['id']; ?>"><?php echo getLikesCount($post['id']); ?> suka</p>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function likePost(postId) {
            sendLikeUnlike(postId, 'like');
        }

        function unlikePost(postId) {
            sendLikeUnlike(postId, 'unlike');
        }

        function sendLikeUnlike(postId, action) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'like_unlike_handler.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        const likeContainer = document.getElementById(`like-container-${postId}`);
                        const likeCount = document.getElementById(`like-count-${postId}`);
                        likeCount.innerText = `${response.likes} suka`;
                        likeContainer.innerHTML = response.button;
                    }
                }
            };

            const data = `post_id=${postId}&action=${action}`;
            xhr.send(data);
        }
    </script>
    <script>
        function toggleVideoPlayback(postId) {
            const video = document.getElementById(`video-${postId}`);
            const playButton = document.getElementById(`play-btn-${postId}`);
            const pauseButton = document.getElementById(`pause-btn-${postId}`);

            if (video.paused) {
                video.play();
                playButton.style.display = 'none';
                pauseButton.style.display = 'block';
            } else {
                video.pause();
                playButton.style.display = 'block';
                pauseButton.style.display = 'none';
            }
        }
    </script>


</body>

</html>