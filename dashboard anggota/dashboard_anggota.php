<?php
session_start();
include_once("../config/koneksi.php");

// Cek apakah anggota sudah login
if (!isset($_SESSION["id_anggota"])) {
    header("Location: ../login/login-anggota.php");
    exit();
}

$id_anggota = $_SESSION["id_anggota"];
$nama_anggota = $_SESSION["nama_anggota"];

// Ambil data simpanan
$query_simpanan = "SELECT * FROM simpanan WHERE id_anggota='$id_anggota'";
$result_simpanan = mysqli_query($conn, $query_simpanan);
$data_simpanan = mysqli_fetch_assoc($result_simpanan);

// Ambil data pinjaman
$query_pinjaman = "SELECT * FROM pinjaman WHERE id_anggota='$id_anggota'";
$result_pinjaman = mysqli_query($conn, $query_pinjaman);
$data_pinjaman = mysqli_fetch_assoc($result_pinjaman);

// Pastikan data tidak null, jika null, set ke 0
$s_pokok = $data_simpanan["simpanan_pokok"] ?? 0;
$s_wajib = $data_simpanan["simpanan_wajib"] ?? 0;
$s_sukarela = $data_simpanan["simpanan_sukarela"] ?? 0;
$sisa_pinjaman = $data_pinjaman["sisa_pinjaman"] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiJam Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <link rel="stylesheet" href="../css/style_anggota.css">
    <script>
        function openModal() {
            document.getElementById("loanModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("loanModal").style.display = "none";
        }
    </script>
</head>

<body>

    <div class="header">
        <div class="logo-container">
            <strong>SiJam</strong>
            <img src="../picture/logo.png" alt="Logo SiJam">
        </div>
        <a href="../login/logout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="container">
        <h2>👋 Selamat datang, <?= htmlspecialchars($nama_anggota); ?></h2>
        <div class="info-s">Info Simpanan</div>
        <div class="savings-container">
            <div class="savings-box"><i class="fas fa-piggy-bank"></i> Simpanan Pokok <br> Rp. <?= number_format($s_pokok, 0, ',', '.'); ?></div>
            <div class="savings-box"><i class="fas fa-coins"></i> Simpanan Wajib <br> Rp. <?= number_format($s_wajib, 0, ',', '.'); ?></div>
            <div class="savings-box"><i class="fas fa-hand-holding-usd"></i> Simpanan Sukarela <br> Rp. <?= number_format($s_sukarela, 0, ',', '.'); ?></div>
        </div>
        <div class="sisa-p"><i class="fas fa-file-invoice-dollar"></i> Sisa Pinjaman <br> Rp. <?= number_format($sisa_pinjaman, 0, ',', '.'); ?></div>
        <button onclick="openModal()" class="loan-button">Layanan Pinjaman</button>
    </div>
    <div id="loanModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div class="modal-content" style="background:#fff; padding:20px; margin:15% auto; width:50%;">
            <span onclick="closeModal()" style="float:right; cursor:pointer; font-size:20px;">&times;</span>
            <h2>Ajukan Pinjaman</h2>
            <form action="proses_pinjaman.php" method="POST">
                <label for="nominal">Nominal Pinjaman:</label>
                <input type="number" name="nominal" id="nominal" required>
                
                <label for="angsuran">Pilih Angsuran:</label>
                <select name="angsuran" id="angsuran" required>
                    <option value="6">6 Bulan</option>
                    <option value="12">12 Bulan</option>
                    <option value="24">24 Bulan</option>
                </select>
                
                <button type="submit">Ajukan</button>
            </form>
        </div>
    </div>
</body>
</html>
