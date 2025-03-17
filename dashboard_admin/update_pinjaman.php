<?php
session_start();
include_once("../config/koneksi.php");

class Pinjaman {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function tambahPinjaman($id_anggota, $total_pinjaman) {
        $tanggal_pinjaman = date('Y-m-d');
        
        // Cek apakah anggota sudah memiliki pinjaman
        $cek_pinjaman = mysqli_query($this->conn, "SELECT * FROM pinjaman WHERE id_anggota = '$id_anggota'");
        if (mysqli_num_rows($cek_pinjaman) > 0) {
            echo "<script>alert('Anggota ini sudah memiliki pinjaman!'); window.location.href='dashboard_pinjaman.php';</script>";
            exit();
        }
        
        // Validasi anggota
        $cek_anggota = mysqli_query($this->conn, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota'");
        if (mysqli_num_rows($cek_anggota) > 0) {
            $query = "INSERT INTO pinjaman (id_anggota, nominal_pinjaman, sisa_pinjaman, tanggal_pinjaman, status) 
                      VALUES ('$id_anggota', '$total_pinjaman', '$total_pinjaman', '$tanggal_pinjaman', 'Belum Lunas')";
            if (mysqli_query($this->conn, $query)) {
                header("Location: dashboard_pinjaman.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($this->conn);
            }
        } else {
            echo "ID Anggota tidak ditemukan!";
        }
    }

    public function kurangiPinjaman($id_pinjaman, $jumlah_pengurangan) {
        $result = mysqli_query($this->conn, "SELECT sisa_pinjaman FROM pinjaman WHERE id_pinjaman = '$id_pinjaman'");
        if ($row = mysqli_fetch_assoc($result)) {
            $sisa_pinjaman = $row['sisa_pinjaman'];
            if ($sisa_pinjaman >= $jumlah_pengurangan) {
                $new_sisa_pinjaman = $sisa_pinjaman - $jumlah_pengurangan;
                $status = ($new_sisa_pinjaman == 0) ? 'Lunas' : 'Belum Lunas';
                $query = "UPDATE pinjaman SET sisa_pinjaman = '$new_sisa_pinjaman', status = '$status' WHERE id_pinjaman = '$id_pinjaman'";
                if (mysqli_query($this->conn, $query)) {
                    header("Location: dashboard_pinjaman.php");
                    exit();
                } else {
                    echo "Error: " . mysqli_error($this->conn);
                }
            } else {
                echo "Jumlah pengurangan melebihi sisa pinjaman!";
            }
        } else {
            echo "Pinjaman tidak ditemukan!";
        }
    }

    public function tambahKePinjaman($id_pinjaman, $additional_amount) {
        $result = mysqli_query($this->conn, "SELECT nominal_pinjaman, sisa_pinjaman FROM pinjaman WHERE id_pinjaman = '$id_pinjaman'");
        if ($row = mysqli_fetch_assoc($result)) {
            $new_nominal_pinjaman = $row['nominal_pinjaman'] + $additional_amount;
            $new_sisa_pinjaman = $row['sisa_pinjaman'] + $additional_amount;
            $new_status = ($new_sisa_pinjaman > 0) ? 'Belum Lunas' : 'Lunas';
            $query = "UPDATE pinjaman SET nominal_pinjaman = '$new_nominal_pinjaman', sisa_pinjaman = '$new_sisa_pinjaman', status = '$new_status' WHERE id_pinjaman = '$id_pinjaman'";
            if (mysqli_query($this->conn, $query)) {
                header("Location: dashboard_pinjaman.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($this->conn);
            }
        } else {
            echo "Pinjaman tidak ditemukan!";
        }
    }
}

$pinjaman = new Pinjaman($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_pinjaman'])) {
        $pinjaman->tambahPinjaman($_POST['id_anggota'], $_POST['total_pinjaman']);
    } elseif (isset($_POST['reduce_pinjaman'])) {
        $pinjaman->kurangiPinjaman($_POST['id_pinjaman_reduced'], $_POST['jumlah_pengurangan']);
    } elseif (isset($_POST['add_pinjaman'])) {
        $pinjaman->tambahKePinjaman($_POST['id_pinjaman'], $_POST['additional_amount']);
    }
}

mysqli_close($conn);
?>
