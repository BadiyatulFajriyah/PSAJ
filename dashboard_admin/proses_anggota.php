<?php
session_start();
include_once("../config/koneksi.php");

// Fungsi mengamankan input
function clean_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Tambah Anggota
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["simpan_anggota"])) {
    $nama_anggota = clean_input($_POST['nama_anggota']);
    $telepon = clean_input($_POST['telepon']);
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    if (!empty($nama_anggota) && !empty($telepon) && !empty($username) && !empty($password)) {
        $query = "INSERT INTO anggota (nama_anggota, telepon, username, password) VALUES ('$nama_anggota', '$telepon', '$username', '$password')";
        if (mysqli_query($conn, $query)) {
            unset($_SESSION["showPopupTambah"]); // Hapus session
            header("Location: dashboard_admin.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
// Tambah Admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["simpan_admin"])) {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $query = "INSERT INTO admin (username, password) VALUES ('$username', '$password')";
        if (mysqli_query($conn, $query)) {
            unset($_SESSION["showPopupTambahAdmin"]); // Hapus session
            header("Location: dashboard_admin.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Edit Anggota
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_anggota"])) {
    $id_anggota = clean_input($_POST['id_anggota']);
    $nama_anggota = clean_input($_POST['nama_anggota']);
    $telepon = clean_input($_POST['telepon']);

    if (!empty($id_anggota) && !empty($nama_anggota) && !empty($telepon)) {
        $query = "UPDATE anggota SET nama_anggota='$nama_anggota', telepon='$telepon' WHERE id_anggota='$id_anggota'";
        if (mysqli_query($conn, $query)) {
            unset($_SESSION["showPopupEdit"]); // Hapus session
            header("Location: dashboard_admin.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
