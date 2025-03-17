<?php
session_start();
include_once("../config/koneksi.php");

class Admin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function hapusAdmin($id_admin) {
        $id_admin = intval($id_admin); // Pastikan ID hanya angka
        $query = "DELETE FROM admin WHERE id_admin = $id_admin";
        return mysqli_query($this->conn, $query);
    }
}

// Inisialisasi Kelas Admin
$admin = new Admin($conn);

// Cek apakah parameter ID ada
if (isset($_GET['id_admin'])) {
    if ($admin->hapusAdmin($_GET['id_admin'])) {
        $_SESSION["showDaftarAdmin"] = true; // Agar popup tetap muncul setelah hapus
        header("Location: dashboard_admin.php");
        exit();
    } else {
        echo "Error: Gagal menghapus admin!";
    }
} else {
    echo "ID Admin tidak valid!";
}
?>
