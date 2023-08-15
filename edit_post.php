<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $sql_post = "SELECT * FROM posting WHERE id = $post_id";
    $result_post = $koneksi->query($sql_post);
    $post_data = $result_post->fetch_assoc();
}

if (isset($_POST['edit'])) {
    $post_id = $_POST['post_id'];
    $new_caption = $_POST['new_caption'];

    $sql_edit_caption = "UPDATE posting SET caption = '$new_caption' WHERE id = $post_id";

    if ($koneksi->query($sql_edit_caption) === TRUE) {
        echo "Caption berhasil diedit!";
    } else {
        echo "Error: " . $sql_edit_caption . "<br>" . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Caption</title>
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

    <div class="container mx-auto mt-8">
        <div class="bg-white p-4 shadow-md">
            <h2 class="text-2xl font-semibold">Edit Caption</h2>
            <form method="post" action="" class="space-y-4">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <label class="block">Caption Baru:</label>
                <textarea name="new_caption" rows="3" class="border p-2 w-full"><?php echo $post_data['caption']; ?></textarea>
                <button type="submit" name="edit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan Perubahan</button>
            </form>
            <a href="profil.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="text-blue-500 hover:underline mt-2 block">Kembali ke Profil</a>
        </div>
    </div>

    <nav class="fixed bottom-0 left-0 w-full bg-white shadow">
        <div class="container mx-auto flex justify-between py-2 px-4">
            <a href="./" class="text-blue-500 nav-link"><i class="bi bi-house-door"></i></a>
            <a href="search.php" class="text-blue-500 nav-link"><i class="bi bi-search"></i></a>
            <a href="profil.php?user_id=<?php echo $user_id; ?>" class="text-blue-500 nav-link"><i class="bi bi-person"></i></a>
        </div>
    </nav>
</body>

</html>