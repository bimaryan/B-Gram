<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Pengguna (username, email, password_hash) VALUES ('$username', '$email', '$password')";
    
    if ($koneksi->query($sql) === TRUE) {
        header ('location: login.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }
}

$koneksi->close();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-96">
            <h2 class="text-2xl font-semibold mb-4">Pendaftaran Pengguna</h2>
            <form method="post" action="" class="space-y-4">
                <label class="block">Username:</label>
                <input type="text" name="username" class="border p-2 w-full" required>
                
                <label class="block">Email:</label>
                <input type="email" name="email" class="border p-2 w-full" required>
                
                <label class="block">Password:</label>
                <input type="password" name="password" class="border p-2 w-full" required>
                
                <button type="submit"  name="register" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">Daftar</button>
            </form>
        </div>
    </div>
</body>
</html>

