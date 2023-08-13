<!-- forgot_password.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-96">
            <h2 class="text-2xl font-semibold mb-4">Lupa Password</h2>
            <form method="post" action="send_reset_link.php" class="space-y-4">
                <label class="block">Email:</label>
                <input type="email" name="email" class="border p-2 w-full" required>
                
                <button type="submit" name="send_reset_link" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">Kirim Tautan Reset Password</button>
            </form>
            <p class="mt-4 text-center">
                Kembali ke <a href="login.php" class="text-blue-500">Halaman Masuk</a>
            </p>
        </div>
    </div>
</body>
</html>