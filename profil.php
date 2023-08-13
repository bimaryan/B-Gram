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
    $sql_viewed_user = "SELECT * FROM Pengguna WHERE id = $viewed_user_id";
    $result_viewed_user = $koneksi->query($sql_viewed_user);
    $viewed_user_data = $result_viewed_user->fetch_assoc();

    // Mengambil posting pengguna yang sedang dilihat profilnya
    $sql_viewed_post = "SELECT * FROM Posting WHERE user_id = $viewed_user_id ORDER BY created_at DESC";
    $result_viewed_post = $koneksi->query($sql_viewed_post);
}


if (isset($_POST['unggah'])) {
    $caption = $_POST['caption'];

    $target_dir = "uploads/"; // Direktori untuk menyimpan gambar yang diunggah
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Izinkan hanya format gambar tertentu (misalnya jpg, png)
    $allowedFormats = array("jpg", "png", "mp4", "MOV");
    if (in_array($imageFileType, $allowedFormats)) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $image_path = $target_file;

            // Simpan data posting ke basis data
            $sql_insert_post = "INSERT INTO Posting (user_id, image_path, caption) VALUES ($user_id, '$image_path', '$caption')";
            if ($koneksi->query($sql_insert_post) === TRUE) {
                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Foto berhasil diunggah!',
                            showCon1500
                        });firmButton: false,
                            timer: 
                    </script>";
            } else {
                echo "Error: " . $sql_insert_post . "<br>" . $koneksi->error;
            }
        } else {
            echo "Gagal mengunggah foto.";
        }
    } else {
        echo "Hanya format JPG dan PNG yang diizinkan.";
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
            $sql_update_profile_picture = "UPDATE Pengguna SET profile_picture = '$new_profile_picture' WHERE id = $user_id";
            if ($koneksi->query($sql_update_profile_picture) === TRUE) {
                echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Foto berhasil diunggah!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>";
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


// Mengecek apakah pengguna yang sedang login sudah mengikuti pengguna yang sedang dilihat profilnya
$sql_check_follow = "SELECT * FROM Pengikut WHERE follower_id = $user_id AND following_id = $viewed_user_id";
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-4 bg-white p-4 shadow-md items-center justify-between">
                <div class="flex items-center">
                    <img src="<?php echo $viewed_user_data['profile_picture']; ?>" alt="Profil Pengguna" class="w-16 h-16 rounded-full">
                    <div class="flex ml-4">
                        <div class="flex">
                            <p class="m-3">
                                <center>
                                    <?php echo getFollowersCount($viewed_user_id); ?>
                                    <br />
                                    Follow
                                </center>
                            </p>
                            <p class="m-3">
                                <center>
                                    <?php echo getFollowingCount($viewed_user_id); ?>
                                    <br />
                                    Following
                                </center>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <h2 class="text-xl font-semibold"><?php echo $viewed_user_data['username']; ?></h2>
                    <p class="text-gray-500"><?php echo $viewed_user_data['email']; ?></p>
                </div>
                <?php if ($user_id != $viewed_user_id) : ?>
                    <?php if ($is_following) : ?>
                        <a href="proses_unfollow.php?user_id=<?php echo $viewed_user_id; ?>" class="px-3 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Unfollow</a>
                    <?php else : ?>
                        <a href="proses_follow.php?user_id=<?php echo $viewed_user_id; ?>" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Follow</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php if ($user_id == $viewed_user_id) : ?>
                <div class="md:col-span-1 bg-white p-4 shadow-md">
                    <h3 class="text-xl font-semibold mb-4">Foto Profil:</h3>
                    <form method="post" action="" enctype="multipart/form-data" class="space-y-4">
                        <label class="block">Pilih Foto:</label>
                        <input type="file" name="profile_picture" class="border p-2 w-full" required>
                        <button type="submit" name="update_profile_picture" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan Foto Profil</button>
                    </form>
                </div>
                <div class="md:col-span-1 bg-white p-4 shadow-md">
                    <h3 class="text-xl font-semibold mb-4">Unggah Foto:</h3>
                    <form method="post" action="" enctype="multipart/form-data" class="space-y-4">
                        <label class="block">Foto:</label>
                        <input type="file" name="foto" accept="image/*,video/*" class="border p-2 w-full" required>
                        <label class="block">Caption:</label>
                        <textarea name="caption" rows="3" class="border p-2 w-full"></textarea>
                        <button type="submit" name="unggah" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Unggah</button>
                    </form>
                </div>
            <?php endif; ?>
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
    </div>
    <br/>

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