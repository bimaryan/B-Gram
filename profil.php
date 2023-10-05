<?php
session_start();
include 'koneksi.php';
include 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mengambil informasi pengguna yang sedang dilihat profilnya
if (isset($_GET['user_id'])) {
    $viewed_user_id = $_GET['user_id'];
    $sql_viewed_user = "SELECT * FROM pengguna WHERE id = $viewed_user_id";
    $result_viewed_user = $koneksi->query($sql_viewed_user);
    $viewed_user_data = $result_viewed_user->fetch_assoc();

    // Mengambil posting pengguna yang sedang dilihat profilnya
    $sql_viewed_post = "SELECT * FROM posting WHERE user_id = $viewed_user_id ORDER BY created_at DESC";
    $result_viewed_post = $koneksi->query($sql_viewed_post);
}


// Mengecek apakah pengguna yang sedang login sudah mengikuti pengguna yang sedang dilihat profilnya
$sql_check_follow = "SELECT * FROM pengikut WHERE follower_id = $user_id AND following_id = $viewed_user_id";
$result_check_follow = $koneksi->query($sql_check_follow);
$is_following = $result_check_follow->num_rows > 0;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .popup-width {
            width: 90%;
            /* Adjust this value as needed */
            max-width: 400px;
            /* Set a maximum width to ensure readability on larger screens */
        }
    </style>
</head>

<body class="bg-gray-100">
    <nav class="bg-white py-4">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="./" class="text-2xl font-semibold" style="text-decoration: none;">B-Gram</a>
            <div class="flex space-x-4">
                <a href="uploads.php" class="text-blue-500 nav-link active"><i class="bi bi-upload"></i></a>
                <a href="pesan.php" class="text-blue-500 nav-link"><i class="bi bi-chat-text"></i></a>
                <a href="logout.php" class="text-blue-500 nav-link"><i class="bi bi-box-arrow-left"></i></a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-4 bg-white p-4 shadow-md">
                <div class="flex items-center">
                    <img src="<?php echo $viewed_user_data['profile_picture']; ?>" alt="Profil Pengguna" class="w-16 h-16 rounded-full">
                    <div class="ml-4">
                        <div class="flex items-center">
                            <div class="text-lg font-semibold mr-3"><?php echo $viewed_user_data['username']; ?></div>
                        </div>
                        <div class="flex">
                            <p class="mr-3">
                                <span class="font-semibold"><?php echo getPostsCount($viewed_user_id); ?></span> Posts
                            </p>
                            <p class="mr-3">
                                <a href="#" onclick="showFollowersPopup(); return false;" class="nav-link font-semibold"><?php echo getFollowersCount($viewed_user_id); ?> Followers</a>
                            </p>
                            <p>
                                <a href="#" onclick="showFollowingPopup(); return false;" class="nav-link font-semibold"><?php echo getFollowingCount($viewed_user_id); ?> Following</a>
                            </p>
                        </div>
                        <?php if ($user_id != $viewed_user_id) : ?>
                            <div class="mt-3">
                                <?php if ($is_following) : ?>
                                    <a href="proses_unfollow.php?user_id=<?php echo $viewed_user_id; ?>" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Unfollow</a>
                                <?php else : ?>
                                    <a href="proses_follow.php?user_id=<?php echo $viewed_user_id; ?>" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Follow</a>
                                <?php endif; ?>
                                <a href="message.php?recipient_id=<?php echo $viewed_user_id; ?>" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 ml-2">Message</a>
                            </div>
                        <?php endif; ?>
                        <div class="mt-3">
                            <span><?php echo $viewed_user_data['email']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <?php while ($post = $result_viewed_post->fetch_assoc()) : ?>
                <div class="bg-white p-4 shadow-md">
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
                    <p class="mt-2 text-gray-500"><?php echo $post['created_at']; ?></p>
                    <?php if ($user_id == $viewed_user_id) : ?>
                        <a href="edit_post.php?post_id=<?php echo $post['id']; ?>" class="text-blue-500 hover:underline">Edit Caption</a>
                        <a href="hapus_post.php?post_id=<?php echo $post['id']; ?>" class="text-red-500 hover:underline ml-2">Hapus Foto</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <div id="followers-popup" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white p-4 rounded-lg popup-width">
                <h2 class="text-lg font-semibold mb-3">Followers</h2>
                <ul class="list-group">
                    <?php
                    $sql_followers = "SELECT follower_id FROM pengikut WHERE following_id = $viewed_user_id";
                    $result_followers = $koneksi->query($sql_followers);

                    while ($follower = $result_followers->fetch_assoc()) {
                        $follower_id = $follower['follower_id'];
                        $follower_data = getUserData($follower_id);

                        echo '<li class="list-group-item flex items-center justify-between">';
                        echo '<div class="flex items-center">';

                        // Display profile picture
                        echo '<img src="' . $follower_data['profile_picture'] . '" alt="' . $follower_data['username'] . '" class="w-10 h-10 rounded-full mr-2">';

                        // Display username with link to profile
                        echo '<a href="profil.php?user_id=' . $follower_id . '" class="nav-link mr-2">' . $follower_data['username'] . '</a>';

                        echo '</div>';
                        echo '</li>';
                    }
                    ?>
                </ul>
                <button type="button" onclick="hideFollowersPopup()" class="btn btn-secondary mt-3">Close</button>
            </div>
        </div>

        <!-- Popup for Following -->
        <div id="following-popup" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white p-4 rounded-lg popup-width">
                <h2 class="text-lg font-semibold mb-3">Following</h2>
                <ul class="list-group">
                    <?php
                    $sql_following = "SELECT following_id FROM pengikut WHERE follower_id = $viewed_user_id";
                    $result_following = $koneksi->query($sql_following);

                    while ($following = $result_following->fetch_assoc()) {
                        $following_id = $following['following_id'];
                        $following_data = getUserData($following_id);

                        echo '<li class="list-group-item flex items-center justify-between">';
                        echo '<div class="flex items-center">';

                        // Display profile picture
                        echo '<img src="' . $following_data['profile_picture'] . '" alt="' . $following_data['username'] . '" class="w-10 h-10 rounded-full mr-2">';

                        // Display username with link to profile
                        echo '<a href="profil.php?user_id=' . $following_id . '" class="nav-link mr-2">' . $following_data['username'] . '</a>';

                        echo '</div>';
                        echo '</li>';
                    }
                    ?>
                </ul>
                <button type="button" onclick="hideFollowingPopup()" class="btn btn-secondary mt-3">Close</button>
            </div>
        </div>
    </div>
    <br />
    <br />
    <nav class="fixed bottom-0 left-0 w-full bg-white shadow">
        <div class="container mx-auto flex justify-between py-2 px-4">
            <a href="./" class="text-blue-500 nav-link"><i class="bi bi-house-door"></i></a>
            <a href="search.php" class="text-blue-500 nav-link"><i class="bi bi-search"></i></a>
            <a href="profil.php?user_id=<?php echo $user_id; ?>" class="text-blue-500 nav-link"><i class="bi bi-person"></i></a>
        </div>
    </nav>

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
    <script>
        function showFollowersPopup() {
            document.getElementById('followers-popup').classList.remove('hidden');
        }

        function hideFollowersPopup() {
            document.getElementById('followers-popup').classList.add('hidden');
        }

        function showFollowingPopup() {
            document.getElementById('following-popup').classList.remove('hidden');
        }

        function hideFollowingPopup() {
            document.getElementById('following-popup').classList.add('hidden');
        }
    </script>
</body>

</html>