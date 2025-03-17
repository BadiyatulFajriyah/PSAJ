<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../css/login_anggota.css">
</head>
<body>
    <div class="container">
        <div class="form-section">
            <form method="POST" action="proses_login.php">
                <h2>LOGIN ANGGOTA</h2>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn">LOGIN NOW</button>
            </form>
        </div>
    </div>
</body>
</html>
