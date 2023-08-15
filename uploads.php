<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $media = $_FILES['media'];
    $caption = $_POST['caption'];

    $uploadDirectory = 'uploads/';
    $allowedImageTypes = array('image/jpeg', 'image/png', 'image/gif');
    $allowedVideoTypes = array('video/mp4');

    $filename = $media['name'];
    $filetype = $media['type'];
    $filesize = $media['size'];
    $filetmp = $media['tmp_name'];

    if (in_array($filetype, $allowedImageTypes)) {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDirectory . $newFilename;

        if (move_uploaded_file($filetmp, $uploadPath)) {
            $sql = "INSERT INTO posting (user_id, image_path, caption) VALUES ('$user_id', '$uploadPath', '$caption')";
            if ($koneksi->query($sql)) {
                header('location: index.php');
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $koneksi->error;
            }
        } else {
            echo "Image upload failed!";
        }
    } elseif (in_array($filetype, $allowedVideoTypes)) {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDirectory . $newFilename;

        if (move_uploaded_file($filetmp, $uploadPath)) {
            $sql = "INSERT INTO posting (user_id, image_path, caption) VALUES ('$user_id', '$uploadPath', '$caption')";
            if ($koneksi->query($sql)) {
                header('location: index.php');
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $koneksi->error;
            }
        } else {
            echo "Video upload failed!";
        }
    } else {
        echo "Unsupported file type!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Media</title>
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
                <a href="beranda_chat.php" class="text-blue-500 nav-link"><i class="bi bi-chat-text"></i></a>
                <a href="logout.php" class="text-blue-500 nav-link"><i class="bi bi-box-arrow-left"></i></a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Upload Media</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="media" class="form-label">Choose File:</label>
                <input type="file" name="media" id="media" accept="image/*,video/*" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="caption" class="form-label">Caption:</label>
                <input type="text" name="caption" id="caption" class="form-control" placeholder="Caption" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
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
</body>

</html>