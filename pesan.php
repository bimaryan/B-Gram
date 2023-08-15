<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mengambil daftar pengguna untuk chat
$sql_users = "SELECT id, username FROM pengguna WHERE id != $user_id";
$result_users = $koneksi->query($sql_users);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Direct Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 font-sans">
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

    <div class="container mx-auto mt-6">
        <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php while ($user = $result_users->fetch_assoc()) : ?>
                <li class="bg-white p-4 shadow-md rounded-lg">
                    <a href="message.php?recipient_id=<?php echo $user['id']; ?>" class="nav-link block hover:bg-gray-100 focus:outline-none focus:ring focus:border-blue-300">
                        <div class="flex items-center">
                            <div class="w-10 h-10 mr-3 bg-gray-200 rounded-full flex-shrink-0">
                                <?php
                                // Mengambil data profil pengguna
                                $sql_user_profile = "SELECT profile_picture FROM pengguna WHERE id = {$user['id']}";
                                $result_user_profile = $koneksi->query($sql_user_profile);
                                $user_profile_data = $result_user_profile->fetch_assoc();

                                if ($user_profile_data['profile_picture']) {
                                    echo '<img src="' . $user_profile_data['profile_picture'] . '" alt="Profil Pengguna" class="w-10 h-10 rounded-full object-cover">';
                                } else {
                                    echo '<div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">';
                                    echo '<span class="text-gray-600">No Photo</span>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-lg font-semibold"><?php echo $user['username']; ?></h2>
                                <?php
                                $sql_last_message = "SELECT * FROM messages WHERE (sender_id = $user_id AND recipient_id = {$user['id']}) OR (sender_id = {$user['id']} AND recipient_id = $user_id) ORDER BY created_at DESC LIMIT 1";
                                $result_last_message = $koneksi->query($sql_last_message);
                                $last_message = $result_last_message->fetch_assoc();

                                if ($last_message) {
                                    echo '<p class="text-gray-500 truncate">' . $last_message['message_text'] . '</p>';
                                } else {
                                    echo '<p class="text-gray-500">Belum ada pesan.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <br/>
    <nav class="fixed bottom-0 left-0 w-full bg-white shadow">
        <div class="container mx-auto flex justify-between py-2 px-4">
            <a href="./" class="text-blue-500 nav-link"><i class="bi bi-house-door"></i></a>
            <a href="search.php" class="text-blue-500 nav-link"><i class="bi bi-search"></i></a>
            <a href="profil.php?user_id=<?php echo $user_id; ?>" class="text-blue-500 nav-link"><i class="bi bi-person"></i></a>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.js"></script>
</body>

</html>