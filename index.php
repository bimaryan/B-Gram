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

if (isset($_POST['share_post'])) {
    $post_id = $_POST['post_id'];
    $selected_recipients = $_POST['recipient_id'];

    // Ensure you sanitize and validate the input here

    foreach ($selected_recipients as $recipient_id) {
        // Construct and execute your SQL query here for each recipient
        $sql_insert_share = "INSERT INTO shares (user_id, post_id, recipient_id) VALUES ($user_id, $post_id, $recipient_id)";
        $koneksi->query($sql_insert_share);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        #share-popup {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            align-items: flex-end;
            justify-content: center;
            z-index: 1000;
        }

        #share-popup-content .btn {
            flex: 1;
        }

        #share-popup-content {
            transition: transform 0.3s, opacity 0.3s;
            transform: translateY(100%);
            width: 100%;
            max-width: 500px;
            /* Adjust as needed */
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>

<body class="bg-gray-100">
    <nav class="navbar navbar-expand-lg bg-white py-4 fixed-top shadow">
        <div class="container mx-auto flex justify-between items-center px-4">
            <a href="./" class="text-2xl font-semibold" style="text-decoration: none;">B-Gram</a>
            <div class="flex space-x-4">
                <a href="uploads.php" class="text-blue-500 nav-link active"><i class="bi bi-upload"></i></a>
                <a href="pesan.php" class="text-blue-500 nav-link"><i class="bi bi-chat-text"></i></a>
                <a href="logout.php" class="text-blue-500 nav-link"><i class="bi bi-box-arrow-left"></i></a>
            </div>
        </div>
    </nav>

    <br />
    <br />
    <br />

    <div class="container mx-auto mt-4">
        <div class="grid grid-cols-1 gap-4">
            <?php while ($post = $result_post->fetch_assoc()) : ?>
                <?php
                $sql_check_like = "SELECT * FROM likes WHERE user_id = $user_id AND post_id = " . $post['id'];
                $result_check_like = $koneksi->query($sql_check_like);
                $is_liked = $result_check_like->num_rows > 0;

                $posted_user_id = $post['user_id'];
                $posted_user_data = getUserData($posted_user_id);
                ?>
                <div class="rounded bg-white p-4 shadow-md">
                    <div class="flex items-center mb-2">
                        <img src="<?php echo $posted_user_data['profile_picture']; ?>" alt="Profil Pengguna" class="w-10 h-10 rounded-full">
                        <?php
                        $posted_user_id = $post['user_id'];
                        $posted_user_data = getUserData($posted_user_id);
                        ?>
                        <a href="profil.php?user_id=<?php echo $post['user_id']; ?>" class="ml-2 font-semibold" style="text-decoration: none;"><?php echo $posted_user_data['username']; ?></a>
                    </div>
                    <div class="relative">
                        <?php if (pathinfo($post['image_path'], PATHINFO_EXTENSION) === 'mp4' || pathinfo($post['image_path'], PATHINFO_EXTENSION) === 'MOV') : ?>
                            <video id="video-<?php echo $post['id']; ?>" data-post-id="<?php echo $post['id']; ?>" src="<?php echo $post['image_path']; ?>" alt="Postingan" class="w-full object-cover" loop></video>
                            <div class="absolute inset-0 flex items-center justify-center" id="video-overlay-<?php echo $post['id']; ?>">
                                <button class="text-white bg-black bg-opacity-50 p-2 rounded-full" id="play-btn-<?php echo $post['id']; ?>" onclick="toggleVideoPlayback(<?php echo $post['id']; ?>)">
                                    <i class="bi bi-play"></i>
                                </button>
                                <button class="text-white bg-black bg-opacity-50 p-2 rounded-full hidden" id="pause-btn-<?php echo $post['id']; ?>" onclick="toggleVideoPlayback(<?php echo $post['id']; ?>)">
                                    <i class="bi bi-pause"></i>
                                </button>
                                <div class="text-white absolute bottom-2 right-2">
                                    <span id="video-duration-<?php echo $post['id']; ?>">0:00</span>
                                </div>
                            </div>
                        <?php else : ?>
                            <img src="<?php echo $post['image_path']; ?>" alt="Postingan" class="w-full h-64 object-cover">
                        <?php endif; ?>
                    </div>
                    <p class="mt-2"><?php echo $post['caption']; ?></p>

                    <div class="flex justify-between items-center mt-4">
                        <div class="flex items-center">
                            <?php if ($is_liked) : ?>
                                <button onclick="unlikePost(<?php echo $post['id']; ?>)" class="btn btn-primary"><i class="bi bi-hand-thumbs-up-fill"></i></button>
                            <?php else : ?>
                                <button onclick="likePost(<?php echo $post['id']; ?>)" class="btn btn-danger"><i class="bi bi-hand-thumbs-up"></i></button>
                            <?php endif; ?>
                            <div class="ml-2 text-gray-500" id="like-count-<?php echo $post['id']; ?>"><?php echo getLikesCount($post['id']); ?> suka</div>
                        </div>
                        <button class="text-blue-500" onclick="showSharePopup(<?php echo $post['id']; ?>)">Berbagi</button>
                    </div>
                </div>
            <?php endwhile; ?>

        </div>
        <div id="share-popup" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white p-4 rounded-lg overflow-y-auto" id="share-popup-content" style="height: 50vh;">
                <nav class="navbar navbar-expand-lg fixed-top">
                    <div class="container-fluid">
                        <div class="navbar-brand">Bagikan Postingan</div>
                    </div>
                </nav>
                <br />
                <form method="post" class="container mt-2">
                    <input type="hidden" name="post_id" value="">
                    <ul class="list-group">
                        <?php
                        $sql_users = "SELECT id, username FROM pengguna WHERE id != $user_id";
                        $result_users = $koneksi->query($sql_users);

                        while ($user = $result_users->fetch_assoc()) {
                            // Display each user as an option to share
                            echo '<li class="list-group-item flex items-center justify-between">';
                            echo '<label>';
                            echo '<span class="mr-2">' . $user['username'] . '</span>';
                            echo '<input type="checkbox" class="form-check-input" name="recipient_id[]" value="' . $user['id'] . '">';
                            echo '</label>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                    <div class="mt-3">
                        <button type="submit" name="share_post" class="btn btn-primary">Bagikan</button>
                        <button type="button" onclick="hideSharePopup()" class="btn btn-secondary">Batal</button>
                    </div>
                </form>
                <!-- <hr class="my-3">
                <p class="font-semibold">Bagikan melalui:</p>
                <div class="flex gap-3">
                    <a href="#" class="btn btn-outline-primary" onclick="shareOnWhatsApp()">WhatsApp</a>
                    <a href="#" class="btn btn-outline-info" onclick="copyPostLink()">Salin Tautan</a>
                </div> -->
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
        let currentVideoId = null;

        function toggleVideoPlayback(postId) {
            const video = document.getElementById(`video-${postId}`);
            const playButton = document.getElementById(`play-btn-${postId}`);
            const pauseButton = document.getElementById(`pause-btn-${postId}`);
            const durationElement = document.getElementById(`video-duration-${postId}`);

            if (currentVideoId !== null && currentVideoId !== postId) {
                const prevVideo = document.getElementById(`video-${currentVideoId}`);
                prevVideo.pause();
            }

            if (video.paused) {
                video.play();
                playButton.style.display = 'none';
                pauseButton.style.display = 'block';
                currentVideoId = postId;

                video.addEventListener('timeupdate', function() {
                    const minutes = Math.floor(video.currentTime / 60);
                    const seconds = Math.floor(video.currentTime % 60);
                    durationElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                });
            } else {
                video.pause();
                playButton.style.display = 'block';
                pauseButton.style.display = 'none';
                currentVideoId = null;
            }
        }

        // Use Intersection Observer to handle video playback
        const videoElements = document.querySelectorAll('video');

        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5 // Trigger when at least 50% of the video is visible
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const postId = entry.target.getAttribute('data-post-id');
                    toggleVideoPlayback(postId);
                } else {
                    const postId = entry.target.getAttribute('data-post-id');
                    const video = document.getElementById(`video-${postId}`);
                    video.pause();
                }
            });
        }, options);

        videoElements.forEach(video => {
            const postId = video.getAttribute('data-post-id');
            observer.observe(video);
        });
    </script>
    <script>
        function showSharePopup(postId) {
            const popup = document.getElementById('share-popup');
            const popupContent = document.getElementById('share-popup-content');

            // Set the post_id value in the form
            const form = popupContent.querySelector('form');
            const postInput = form.querySelector('input[name="post_id"]');
            postInput.value = postId;

            // Show the popup with a fade-in and slide-up effect
            popup.style.display = 'flex';
            setTimeout(() => {
                popup.style.opacity = '1';
                popupContent.style.transform = 'translateY(0)';
            }, 10);
        }

        function hideSharePopup() {
            const popup = document.getElementById('share-popup');
            const popupContent = document.getElementById('share-popup-content');

            // Hide the popup with a fade-out and slide-down effect
            popup.style.opacity = '0';
            popupContent.style.transform = 'translateY(100%)';
            setTimeout(() => {
                popup.style.display = 'none';
            }, 300);
        }

        function shareOnWhatsApp() {
            // Replace this URL with the actual link to the post
            const postLink = 'https://localhost/post/123';

            // Format the message for WhatsApp sharing
            const message = `Lihat postingan menarik: ${postLink}`;

            // Create the WhatsApp sharing link
            const whatsappLink = `https://api.whatsapp.com/send?text=${encodeURIComponent(message)}`;

            // Open the WhatsApp sharing link
            window.open(whatsappLink, '_blank');
        }

        function copyPostLink() {
            const postLink = 'https://localhost/post/123'; // Replace with the actual link
            const tempInput = document.createElement('input');
            tempInput.value = postLink;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert('Tautan berhasil disalin!');
        }
    </script>
</body>

</html>