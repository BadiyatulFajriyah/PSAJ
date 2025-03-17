<?php
session_start();
include_once("../config/koneksi.php");

$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login_admin.php");
    exit;
}

class Pinjaman {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTotalRows($search) {
        $sql = "SELECT COUNT(*) as total FROM pinjaman p JOIN anggota a ON p.id_anggota = a.id_anggota WHERE a.nama_anggota LIKE '%$search%'";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'];
    }

    public function getPinjaman($search, $limit, $offset) {
        $sql = "SELECT p.id_pinjaman, a.nama_anggota, p.nominal_pinjaman, p.sisa_pinjaman, p.tanggal_pinjaman, p.status 
                FROM pinjaman p 
                JOIN anggota a ON p.id_anggota = a.id_anggota 
                WHERE a.nama_anggota LIKE '%$search%' 
                ORDER BY a.nama_anggota ASC
                LIMIT $limit OFFSET $offset";
        return $this->conn->query($sql);
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$pinjaman = new Pinjaman($conn);
$total_rows = $pinjaman->getTotalRows($search);
$total_pages = ceil($total_rows / $limit);
$result = $pinjaman->getPinjaman($search, $limit, $offset);
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiJam - Pinjaman</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/popup_pinjaman.css">
    <link rel="stylesheet" href="../css/search.css">
    <link rel="stylesheet" href="../css/pinjaman+.css">
</head>
<body>

<?php 
include_once("header.php"); 
?>

<div class="container">
    <div class="search-container">
        <button class="btn-tambah" onclick="showPopup()"><i class="fa fa-plus"></i> Input Pinjaman</button>
        <form method="GET" class="search-container">
            <input type="search" name="search" class="search-box" placeholder="Cari..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" name="submit_search" class="btn-search">
            <i class="fa fa-search"></i> Cari
        </button>
        </form>
    </div>

    <!-- Overlay Background -->
    <div class="overlay" id="overlay"></div>
    <!-- Modal Input Pinjaman -->
    <div class="popup" id="popup">
        <form method="POST" action="update_pinjaman.php">
            <h3>Input Pinjaman</h3>
            <select id="id_anggota" name="id_anggota" required>
                <option value="" disabled selected>Pilih Nama Anggota</option>
                <?php
                $anggota_query = "SELECT * FROM anggota ORDER BY nama_anggota ASC"; // Ambil semua anggota dan urutkan
                $anggota_result = $conn->query($anggota_query);
                while ($anggota = $anggota_result->fetch_assoc()) {
                echo "<option value='" . $anggota['id_anggota'] . "'>" . $anggota['nama_anggota'] . "</option>";
                }
                ?>
            </select>


            <input type="number" id="total_pinjaman" name="total_pinjaman" placeholder="Masukkan Nominal" required>
            <button type="submit" name="tambah_pinjaman">Tambah Pinjaman</button>
            <button class="close-btn" type="button" onclick="closePopup()" style="display: block; margin-top: 10px;">Batal</button>
        </form>
    </div>

    <!-- Modal Pengurangan Pinjaman -->
    <div class="popup" id="popup-reduce">
        <form method="POST" action="update_pinjaman.php">
            <h3>Bayar Pinjaman</h3>
            <input type="hidden" id="id_pinjaman_reduced" name="id_pinjaman_reduced">
            <input type="number" id="jumlah_pengurangan" name="jumlah_pengurangan" placeholder="Masukkan Nominal" required>
            <button type="submit" name="reduce_pinjaman">Bayar</button>
            <button class="close-btn" type="button" onclick="closePopupReduce()">Batal</button>
        </form>
    </div>

<!-- Modal Input Pinjaman Tambahan -->
<div class="popup" id="popup-add">
    <form method="POST" action="update_pinjaman.php">
        <h3>Tambah Pinjaman</h3>
        <!-- Input untuk ID pinjaman yang sedang ditambahkan -->
        <input type="hidden" id="id_pinjaman" name="id_pinjaman">
        <input type="number" id="additional_amount" name="additional_amount" placeholder="Masukkan Nominal" required>
        <button type="submit" name="add_pinjaman">Tambah Pinjaman</button>
        <button class="close-btn" type="button" onclick="closePopupAdd()">Batal</button>
    </form>
</div>

<div class="table-container">
    <div style="font-size: 18px; color: #375248; margin-bottom: 10px;">
        👋Selamat datang, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
    </div>
    <div class="header-container">
        <h4>Daftar Pinjaman</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Pinjaman</th>
                <th>Sisa Pinjaman</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                            echo "<td>" . $row['tanggal_pinjaman'] . "</td>";
                            echo "<td>" . $row['nama_anggota'] . "</td>";
                            echo "<td>" . number_format($row['nominal_pinjaman'], 0, ',', '.') . " 
                                    <a href='#' class='add' onclick='showPopupAdd(" . $row['id_pinjaman'] . ", " . $row['nominal_pinjaman'] . ")'>
                                        <i class='fa-solid fa-circle-plus fa-xl' style='color: #0BA16F'></i>
                                    </a>
                                </td>";
                            echo "<td>" . number_format($row['sisa_pinjaman'], 0, ',', '.') . "</td>";

                            // Menentukan warna status
                            $statusColor = ($row['status'] == 'Lunas') ? '#0BA16F' : '#D84040';
                            echo "<td style='color: $statusColor; font-weight: bold;'>" . $row['status'] . "</td>";
                            echo "<td>
                                    <a href='#' class='reduce' onclick='showPopupReduce(" . $row['id_pinjaman'] . ", " . $row['nominal_pinjaman'] . ")'>
                                    <i class='fa-solid fa-circle-minus fa-2xl' style='color: #D84040'></i>
                                    </a>
                                    <a href='delete_pinjaman.php?id=" . $row['id_pinjaman'] . "' class='delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus pinjaman ini?\")'>Hapus</a>
                                </td>";
                        echo "</tr>";
                    }
                } else {
                     echo "<tr><td colspan='6'>Tidak ada data pinjaman</td></tr>";
                }
            ?>
        </tbody>
        </table>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?search=<?php echo $search; ?>&page=<?php echo $page - 1; ?>">&laquo; Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?search=<?php echo $search; ?>&page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?search=<?php echo $search; ?>&page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
</div>

<script>
// Fungsi untuk menampilkan modal input pinjaman
function showPopup() {
    document.getElementById('popup').classList.add('active');  
    document.getElementById('overlay').classList.add('active'); 
    document.getElementById('total_pinjaman').value = ""; // Kosongkan input nominal
}

// Fungsi untuk menutup modal input pinjaman
function closePopup() {
    document.getElementById('popup').classList.remove('active');  
    document.getElementById('overlay').classList.remove('active'); 
}

function showPopupReduce(id_pinjaman) {
    document.getElementById('id_pinjaman_reduced').value = id_pinjaman;
    document.getElementById('jumlah_pengurangan').value = ""; // Kosongkan input
    document.getElementById('popup-reduce').classList.add('active');  
    document.getElementById('overlay').classList.add('active'); 
}

// Fungsi untuk menutup modal pengurangan pinjaman
function closePopupReduce() {
    document.getElementById('popup-reduce').classList.remove('active');  
    document.getElementById('overlay').classList.remove('active'); 
}

// Fungsi untuk menampilkan modal input pinjaman tambahan
function showPopupAdd(id_pinjaman) {
    document.getElementById('id_pinjaman').value = id_pinjaman;
    document.getElementById('additional_amount').value = ""; // Kosongkan input nominal
    document.getElementById('popup-add').classList.add('active');  
    document.getElementById('overlay').classList.add('active'); 
}

// Fungsi untuk menutup modal input pinjaman tambahan
function closePopupAdd() {
    document.getElementById('popup-add').classList.remove('active');  
    document.getElementById('overlay').classList.remove('active'); 
}

</script>

</body>
</html>