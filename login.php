<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Pengguna WHERE email='$email'";
    $result = $koneksi->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: index.php"); // Ganti dengan halaman setelah login berhasil
        } else {
            echo "Password salah.";
        }
    } else {
        echo "Email tidak ditemukan.";
    }
}

$koneksi->close();
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-96">
            <h2 class="text-2xl font-semibold mb-4">Masuk Pengguna</h2>
            <form method="post" action="" class="space-y-4">
                <label class="block">Email:</label>
                <input type="email" name="email" class="border p-2 w-full" required>

                <label class="block">Password:</label>
                <input type="password" name="password" class="border p-2 w-full" required>

                <button type="submit" name="login" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full" value="Masuk">Masuk</button>
            </form>
            <p class="mt-4 text-center">
                Belum punya akun? <a href="register.php" class="text-blue-500">Daftar sekarang</a>
            </p>
        </div>
    </div>
</body>

</html>