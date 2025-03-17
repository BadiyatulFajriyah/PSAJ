<?php
session_start();
include_once("../config/koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    // Cek apakah username ada di database
    $query = "SELECT * FROM anggota WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // Login sukses, simpan sesi
        $_SESSION["id_anggota"] = $row["id_anggota"];
        $_SESSION["nama_anggota"] = $row["nama_anggota"];
        header("Location: ../dashboard anggota/dashboard_anggota.php");
        exit();
    } else {
        echo "<script>alert('Username atau password salah!'); window.location.href='login-anggota.php';</script>";
    }
}
?>
