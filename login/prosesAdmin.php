<?php
session_start();
include_once("../config/koneksi.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    // Cek apakah username dan password cocok dengan database
    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION["id_admin"] = $row["id_admin"];
        $_SESSION["username"] = $row["username"];

        // Redirect ke dashboard admin
        header("Location: ../dashboard_admin/dashboard_admin.php");
        exit();
    } else {
        echo "<script>alert('Username atau Password salah!'); window.location.href='../login/login-admin.php';</script>";
    }
} else {
    header("Location: ../login/login-admin.php");
    exit();
}
