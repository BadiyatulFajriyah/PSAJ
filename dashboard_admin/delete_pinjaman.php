<?php
session_start();
include_once("../config/koneksi.php");

class Pinjaman {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function hapus($id_pinjaman) {
        $id_pinjaman = intval($id_pinjaman); // Pastikan ID adalah angka
        $query = "DELETE FROM pinjaman WHERE id_pinjaman = $id_pinjaman";
        return mysqli_query($this->conn, $query);
    }
}

// Inisialisasi Kelas Pinjaman
$pinjaman = new Pinjaman($conn);

// Cek apakah parameter ID ada
if (isset($_GET['id_pinjaman']) || isset($_GET['id'])) {
    $id_pinjaman = isset($_GET['id_pinjaman']) ? $_GET['id_pinjaman'] : $_GET['id'];

    if ($pinjaman->hapus($id_pinjaman)) {
        $_SESSION["showDaftarPinjaman"] = true; // Popup tetap muncul setelah hapus
        header("Location: dashboard_pinjaman.php");
        exit();
    } else {
        echo "Error: Gagal menghapus pinjaman!";
    }
} else {
    echo "<script>alert('ID Pinjaman tidak ditemukan!'); window.location.href = 'dashboard_pinjaman.php';</script>";
}
?>
