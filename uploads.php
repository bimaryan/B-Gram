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
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .bottom-navigation {
            background-color: white;
            box-shadow: 0px -2px 6px rgba(0, 0, 0, 0.1);
        }

        .bottom-nav-link {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            font-size: 1.5rem;
        }
    </style>
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
                <input type="file" name="media" id="media" accept="image/*,video/*" class="form-control" onchange="previewMedia(event)" required>
            </div>
            <div class="rounded bg-white p-4 shadow-md mb-3">
                <div id="media-preview" class="mb-3"></div>
            </div>
            <div class="mb-3">
                <label for="caption" class="form-label">Caption:</label>
                <textarea name="caption" id="caption" class="form-control" placeholder="Caption..." style="resize: none;"></textarea>
                <!-- <input type="text" name="caption" id="caption" class="form-control" placeholder="Caption"> -->
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <br />
    <br />
    <div class="bottom-navigation fixed bottom-0 left-0 w-full flex bg-white shadow">
        <a href="./" class="bottom-nav-link text-gray-500"><i class="bi bi-house-door"></i></a>
        <a href="search.php" class="bottom-nav-link text-gray-500"><i class="bi bi-search"></i></a>
        <a href="profil.php?user_id=<?php echo $user_id; ?>" class="bottom-nav-link text-gray-500"><i class="bi bi-person"></i></a>
    </div>

    <script>
        function previewMedia(event) {
            const mediaInput = event.target;
            const previewContainer = document.getElementById('media-preview');
            const captionInput = document.getElementById('caption');

            if (mediaInput.files && mediaInput.files[0]) {
                const mediaFile = mediaInput.files[0];
                const mediaType = mediaFile.type;

                if (mediaType.startsWith('uploads/')) {
                    previewContainer.innerHTML = `<img src="${URL.createObjectURL(mediaFile)}" class="max-w-full object-cover" alt="Media Preview">`;
                } else if (mediaType.startsWith('video/')) {
                    previewContainer.innerHTML = `<video src="${URL.createObjectURL(mediaFile)}" class="w-full object-cover" controlsList="nodownload" controls></video>`;
                } else {
                    previewContainer.innerHTML = '';
                }

                captionInput.disabled = false;
            } else {
                previewContainer.innerHTML = '';
                captionInput.disabled = true;
            }
        }
    </script>
</body>

</html>