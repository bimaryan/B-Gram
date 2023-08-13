<?php
session_start();
include 'koneksi.php';
include 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['unggah'])) {
    $caption = $_POST['caption'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowedFormats = array("jpg", "png", "mp4", "MOV");
    if (in_array($imageFileType, $allowedFormats)) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $image_path = $target_file;

            // Save posting data to the database
            $sql_insert_post = "INSERT INTO Posting (user_id, image_path, caption) VALUES ($user_id, '$image_path', '$caption')";
            if ($koneksi->query($sql_insert_post) === TRUE) {
                // Return a success response
                http_response_code(200);
                echo json_encode(array("message" => "Foto berhasil diunggah!"));
            } else {
                // Return an error response
                http_response_code(500);
                echo json_encode(array("message" => "Gagal mengunggah foto."));
            }
        } else {
            // Return an error response
            http_response_code(500);
            echo json_encode(array("message" => "Gagal mengunggah foto."));
        }
    } else {
        // Return an error response
        http_response_code(400);
        echo json_encode(array("message" => "Hanya format JPG, PNG, MP4, dan MOV yang diizinkan."));
    }
} else {
    // Return an error response
    http_response_code(400);
    echo json_encode(array("message" => "Permintaan tidak valid."));
}
?>
