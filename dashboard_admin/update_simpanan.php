<?php
include_once("../config/koneksi.php");

class Simpanan {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function cekAtauBuatSimpanan($id_anggota) {
        $cek_anggota = mysqli_query($this->conn, "SELECT COUNT(*) as count FROM simpanan WHERE id_anggota = '$id_anggota'");
        $result_cek = mysqli_fetch_assoc($cek_anggota);

        if ($result_cek['count'] == 0) {
            $tanggal_simpanan = date("Y-m-d");
            mysqli_query($this->conn, "INSERT INTO simpanan (id_anggota, simpanan_pokok, simpanan_wajib, simpanan_sukarela, tanggal_simpanan) 
                                      VALUES ('$id_anggota', 0, 0, 0, '$tanggal_simpanan')");
        }
    }

    public function tarikSimpanan($id_anggota, $jenis_simpanan, $jumlah) {
        $tanggal_simpanan = date("Y-m-d");
        $cek_saldo = mysqli_query($this->conn, "SELECT $jenis_simpanan FROM simpanan WHERE id_anggota = '$id_anggota'");
        $saldo_result = mysqli_fetch_assoc($cek_saldo);

        if (!$saldo_result || $saldo_result[$jenis_simpanan] < $jumlah) {
            die("Saldo tidak mencukupi untuk ditarik.");
        }
        
        $query = "UPDATE simpanan SET $jenis_simpanan = $jenis_simpanan - $jumlah, tanggal_simpanan = '$tanggal_simpanan' WHERE id_anggota = '$id_anggota'";
        return mysqli_query($this->conn, $query);
    }

    public function setorSimpanan($id_anggota, $jenis_simpanan, $jumlah) {
        $tanggal_simpanan = date("Y-m-d");
        $query = "UPDATE simpanan SET $jenis_simpanan = $jenis_simpanan + $jumlah, tanggal_simpanan = '$tanggal_simpanan' WHERE id_anggota = '$id_anggota'";
        return mysqli_query($this->conn, $query);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_anggota = $_POST['id_anggota'] ?? '';
    $jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;
    $jenis_simpanan = $_POST['jenis_simpanan'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($id_anggota == '' || !is_numeric($jumlah) || $jumlah <= 0 || empty($jenis_simpanan)) {
        die("Data tidak valid.");
    }

    $simpanan = new Simpanan($conn);
    $simpanan->cekAtauBuatSimpanan($id_anggota);

    if ($action === "tarik") {
        if ($simpanan->tarikSimpanan($id_anggota, $jenis_simpanan, $jumlah)) {
            header("Location: dashboard_simpanan.php");
            exit();
        } else {
            echo "Gagal memperbarui data: " . mysqli_error($conn);
        }
    } elseif ($action === "setor") {
        if ($simpanan->setorSimpanan($id_anggota, $jenis_simpanan, $jumlah)) {
            header("Location: dashboard_simpanan.php");
            exit();
        } else {
            echo "Gagal memperbarui data: " . mysqli_error($conn);
        }
    } else {
        die("Aksi tidak valid.");
    }
}
?>
