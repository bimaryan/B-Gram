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
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pencarian Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
        <h2 class="text-2xl font-semibold mb-4">Pencarian Akun</h2>
        <form method="get" action="" class="flex space-x-2">
            <input type="text" name="query" placeholder="Cari akun..." class="border p-2 w-full">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Cari</button>
        </form>

        <?php
        if (isset($_GET['query'])) {
            $search_query = $_GET['query'];
            $sql_search = "SELECT * FROM pengguna WHERE username LIKE '%$search_query%' OR email LIKE '%$search_query%'";
            $result_search = $koneksi->query($sql_search);

            if ($result_search->num_rows > 0) {
                echo '<div class="mt-4">';
                while ($user = $result_search->fetch_assoc()) {
                    echo '<div class="bg-white p-4 shadow-md mb-4">';
                    echo '<a href="profil.php?user_id=' . $user['id'] . '" class="text-blue-500 font-semibold">' . $user['username'] . '</a>';
                    echo '<p>Email: ' . $user['email'] . '</p>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p class="mt-4">Tidak ditemukan akun dengan kata kunci tersebut.</p>';
            }
        }
        ?>
    </div>

    <br />

    <nav class="fixed bottom-0 left-0 w-full bg-white shadow">
        <div class="container mx-auto flex justify-between py-2 px-4">
            <a href="./" class="text-blue-500 nav-link"><i class="bi bi-house-door"></i></a>
            <a href="search.php" class="text-blue-500 nav-link"><i class="bi bi-search"></i></a>
            <a href="profil.php?user_id=<?php echo $user_id; ?>" class="text-blue-500 nav-link"><i class="bi bi-person"></i></a>
        </div>
    </nav>
</body>

</html>