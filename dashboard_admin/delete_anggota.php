<?php
include_once("../config/koneksi.php");
session_start();

class Anggota {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function cekPinjaman($id_anggota) {
        $query = "SELECT * FROM pinjaman WHERE id_anggota = $id_anggota";
        $result = mysqli_query($this->conn, $query);
        return mysqli_num_rows($result) > 0;
    }

    public function hapusAnggota($id_anggota) {
        if ($this->cekPinjaman($id_anggota)) {
            echo "<script>alert('Maaf, data anggota ini masih ada pinjaman!'); window.location.href='dashboard_admin.php';</script>";
            exit();
        }

        $query = "DELETE FROM anggota WHERE id_anggota = $id_anggota";
        if (mysqli_query($this->conn, $query)) {
            echo "<script>window.location.href='dashboard_admin.php';</script>";
            exit();
        } else {
            echo "<script>alert('Gagal menghapus anggota: " . mysqli_error($this->conn) . "'); window.location.href='dashboard_admin.php';</script>";
            exit();
        }
    }
}

if (isset($_GET['id_anggota'])) {
    $id_anggota = $_GET['id_anggota'];
    $anggota = new Anggota($conn);
    $anggota->hapusAnggota($id_anggota);
} else {
    echo "<script>alert('ID Anggota tidak valid!'); window.location.href='dashboard_admin.php';</script>";
    exit();
}
?>
