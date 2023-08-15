<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['recipient_id'])) {
    $recipient_id = $_GET['recipient_id'];

    // Memastikan pengguna penerima ada dalam basis data
    $sql_recipient = "SELECT * FROM pengguna WHERE id = $recipient_id";
    $result_recipient = $koneksi->query($sql_recipient);

    if ($result_recipient->num_rows > 0) {
        $recipient_data = $result_recipient->fetch_assoc();
    } else {
        echo "Pengguna penerima tidak ditemukan.";
        exit();
    }

    // Jika ada pengiriman pesan
    if (isset($_POST['send_message'])) {
        $message_text = $_POST['message_text'];

        $sql_insert_message = "INSERT INTO messages (sender_id, recipient_id, message_text) VALUES ($user_id, $recipient_id, '$message_text')";

        if ($koneksi->query($sql_insert_message) === TRUE) {
            $_SESSION['message_sent'] = true;
            header("Location: message.php?recipient_id=$recipient_id");
            exit();
        } else {
            echo "Error: " . $sql_insert_message . "<br>" . $koneksi->error;
        }
    }
}

if (isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];

    $sql_delete_message = "DELETE FROM messages WHERE id = $message_id";

    if ($koneksi->query($sql_delete_message) === TRUE) {
        // Pesan berhasil dihapus
        header("Location: message.php?recipient_id=$recipient_id");
        exit();
        // Anda bisa mengambil tindakan tambahan jika diperlukan
    } else {
        echo "Error: " . $sql_delete_message . "<br>" . $koneksi->error;
    }
}

// Clear session after message sent
if (isset($_SESSION['message_sent']) && $_SESSION['message_sent'] === true) {
    unset($_SESSION['message_sent']);
}

$sql_shared_posts = "
    SELECT posting.id, posting.image_path, posting.caption, shares.created_at, shares.recipient_id, shares.user_id
    FROM posting
    INNER JOIN shares ON posting.id = shares.post_id
    WHERE (shares.recipient_id = $user_id OR shares.user_id = $user_id)
    ORDER BY shares.created_at DESC";
$result_shared_posts = $koneksi->query($sql_shared_posts);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kirim Pesan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .message-container {
            max-width: 600px;
            margin: auto;
        }

        .message-sent {
            background-color: #DCF8C6;
            color: #333;
            margin-left: auto;
        }

        .message-received {
            background-color: #F3F4F6;
            color: #333;
            margin-right: auto;
        }

        .message-box {
            padding: 10px;
            margin: 5px;
            border-radius: 10px;
        }

        .sticky-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: white;
            padding: 15px;
            box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="container mx-auto mt-6">
        <div class="navbar navbar-expand-lg fixed-top bg-white shadow">
            <div class="container-fluid flex items-center">
                <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-full mr-4">
                    <img src="<?php echo $recipient_data['profile_picture']; ?>" alt="Profil Pengguna" class="w-16 h-16 rounded-full object-cover">
                </div>
                <div class="flex-grow">
                    <a href="profil.php?user_id=<?php echo $recipient_data['id']; ?>" class="navbar-brand text-xl font-semibold"><?php echo $recipient_data['username']; ?></a>
                </div>
                <div>
                    <a href="beranda_chat.php" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
        <br />
        <div class="message-container mt-5">
            <?php
            $sql_messages = "SELECT * FROM Messages WHERE (sender_id = $user_id AND recipient_id = $recipient_id) OR (sender_id = $recipient_id AND recipient_id = $user_id) ORDER BY created_at ASC";
            $result_messages = $koneksi->query($sql_messages);

            while ($message = $result_messages->fetch_assoc()) {
                $isSender = $message['sender_id'] == $user_id;
                $messageClass = $isSender ? 'message-sent' : 'message-received';
            ?>
                <div class="shadow <?php echo $messageClass; ?> message-box">
                    <p class="text-sm"><?php echo $message['message_text']; ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?php echo $message['created_at']; ?></p>
                    <?php if ($isSender) : ?>
                        <form method="post">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            <button type="submit" name="delete_message" class="text-gray-500 hover:text-red-500 focus:outline-none">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php } ?>
            <?php
            while ($shared_post = $result_shared_posts->fetch_assoc()) {
                if ($shared_post['recipient_id'] == $recipient_id || $shared_post['user_id'] == $recipient_id) {
            ?>
                    <div class="relative bg-white rounded p-4 shadow-md mb-1">
                        <?php if (strpos($shared_post['image_path'], '.mp4') !== false) { ?>
                            <video class="w-full h-64 object-cover" controls>
                                <source src="<?php echo $shared_post['image_path']; ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php } else { ?>
                            <img src="<?php echo $shared_post['image_path']; ?>" alt="Shared Post" class="w-full h-64 object-cover">
                        <?php } ?>
                        <p class="mt-2"><?php echo $shared_post['caption']; ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $shared_post['created_at']; ?></p>
                    </div>
            <?php
                }
            }
            ?>

        </div>
        <br />
        <br />
        <br />
        <form method="post" class="fixed-bottom mt-5">
            <div class="flex items-center bg-white p-2">
                <textarea name="message_text" rows="1" class="form-control" style="resize: none;" placeholder="Ketik pesan..." required></textarea>
                <button type="submit" name="send_message" class="btn btn-success">
                    <i class="bi bi-send"></i>
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.js"></script>
    <script>
        function toggleVideoPlayback(postId) {
            const video = document.getElementById(`video-${postId}`);
            const videoOverlay = document.getElementById(`video-overlay-${postId}`);
            const playButton = document.getElementById(`play-btn-${postId}`);
            const pauseButton = document.getElementById(`pause-btn-${postId}`);

            if (video.paused) {
                video.play();
                videoOverlay.style.display = 'none';
                playButton.style.display = 'none';
                pauseButton.style.display = 'block';
            } else {
                video.pause();
                videoOverlay.style.display = 'block';
                playButton.style.display = 'block';
                pauseButton.style.display = 'none';
            }
        }
    </script>
</body>

</html>