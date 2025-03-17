<?php 
include_once("../config/koneksi.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</head>
<style>
    .logout {
        font-size: 16px;
        color: white;
        text-decoration: none;
        font-weight: bold;
        padding: 10px 15px;
        background-color: #375248; /* Warna sesuai tema dashboard */
        border-radius: 5px;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-right: 20px; /* Beri jarak agar tidak terlalu mepet */
    }
    
    .logout:hover {
        background-color: #2A3E38; /* Warna lebih gelap saat hover */
    }
    
    .navbar {
    background: #618B7D;
    color: white;
    padding: 8px 15px; /* Kurangi padding agar tidak terlalu tinggi */
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0; /* Tetap di bagian atas */
    left: 0;
    width: 100%;
    z-index: 1000;
    height: 50px; /* Atur tinggi navbar */
}

body {
    padding-top: 55px; /* Sesuaikan agar konten tidak tertutup navbar */
}

    
</style>
<body>
<nav class="navbar">
    <div class="logo-container">
        <div class="logo-text">SiJam</div>
        <img src="../picture/logo.png" alt="Logo">
    </div>
    <ul class="menu">
        <li><a href="dashboard_admin.php" class="<?= ($current_page == 'dashboard_admin.php') ? 'active' : '' ?>">Anggota</a></li>
        <li><a href="dashboard_simpanan.php" class="<?= ($current_page == 'dashboard_simpanan.php') ? 'active' : '' ?>">Simpanan</a></li>
        <li><a href="dashboard_pinjaman.php" class="<?= ($current_page == 'dashboard_pinjaman.php') ? 'active' : '' ?>">Pinjaman</a></li>
    </ul>
    <a href="../login/logout.php" class="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>
</body>
</html>
