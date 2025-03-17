<?php
session_start();
include_once("../config/koneksi.php");

$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login_admin.php");
    exit;
}

// Kelas Database
class Database {
    private $conn;

    public function __construct($host, $username, $password, $database) {
        $this->conn = new mysqli($host, $username, $password, $database);
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }

    public function close() {
        $this->conn->close();
    }
}

// Kelas Anggota
class Anggota {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getTotalAnggota($search) {
        $search_safe = $this->db->escape($search);
        $query = "SELECT COUNT(*) AS total FROM anggota WHERE nama_anggota LIKE '%$search_safe%'";
        $result = $this->db->query($query);
        return $result->fetch_assoc()['total'] ?? 0;
    }

    public function getAnggotaList($search, $limit, $offset) {
        $search_safe = $this->db->escape($search);
        $query = "SELECT a.id_anggota, a.nama_anggota, 
                         COALESCE(s.simpanan_pokok, 0) AS simpanan_pokok, 
                         COALESCE(s.simpanan_wajib, 0) AS simpanan_wajib, 
                         COALESCE(s.simpanan_sukarela, 0) AS simpanan_sukarela,
                         DATE(COALESCE(s.tanggal_simpanan, CURDATE())) AS tanggal_simpanan
                  FROM anggota a 
                  LEFT JOIN simpanan s ON a.id_anggota = s.id_anggota 
                  WHERE a.nama_anggota LIKE '%$search_safe%'
                  ORDER BY a.nama_anggota ASC
                  LIMIT $limit OFFSET $offset";
        return $this->db->query($query);
    }
    
}

// Inisialisasi Database dan Anggota
$db = new Database($host, $username, $password, $database);
$anggota = new Anggota($db);

// Ambil data pencarian dan pagination
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$limit = 6;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Ambil total halaman dan data anggota
$total_rows = $anggota->getTotalAnggota($search);
$total_pages = ceil($total_rows / $limit);
$result = $anggota->getAnggotaList($search, $limit, $offset);

// Tangani modal
$showModal = false;
$modalType = "";
$id_anggota_modal = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["open_modal"])) {
    $showModal = true;
    $modalType = $_POST["modal_type"];
    $id_anggota_modal = $_POST["id_anggota"];
}

$db->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiJam - Simpanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/popup_simpanan.css">
    <link rel="stylesheet" href="../css/search.css">
    <link rel="stylesheet" href="../css/popup_admin.css">
    <link rel="stylesheet" href="../css/simpanan+.css">
</head>
<body>

<?php include_once("header.php"); ?>

<div class="container">
    <form method="GET" class="search-container">
        <input type="search" name="search" class="search-box" placeholder="Cari..." 
            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="btn-search">
            <i class="fa fa-search"></i> Cari
        </button>
    </form>
    <div class="table-container">
        <div style="font-size: 18px; color: #375248; margin-bottom: 10px;">
            👋Selamat datang, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
        </div>
        <div class="header-container">
            <h4>Daftar Simpanan</h4>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Simpanan Pokok</th>
                    <th>Simpanan Wajib</th>
                    <th>Simpanan Sukarela</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td><?= htmlspecialchars($row['tanggal_simpanan']); ?></td>
                        <td><?= htmlspecialchars($row['nama_anggota']); ?></td>
                        <td><?= number_format($row['simpanan_pokok'], 0, ',', '.'); ?></td>
                        <td><?= number_format($row['simpanan_wajib'], 0, ',', '.'); ?></td>
                        <td style="display: flex; align-items: center; gap: 8px; justify-content: center;" >
                            <?= number_format($row['simpanan_sukarela'], 0, ',', '.'); ?>

                            <!-- Tombol Tarik Simpanan di Samping Simpanan Sukarela -->
                            <form method="POST">
                                <input type="hidden" name="id_anggota" value="<?= $row['id_anggota']; ?>">
                                <input type="hidden" name="modal_type" value="tarik">
                                <button type="submit" name="open_modal" style="background: none; border: none; ">
                                    <i class="fa-solid fa-circle-minus fa-2xl" style="color: #D84040"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <!-- Tombol Setor Simpanan di Bawah Aksi -->
                            <form method="POST">
                                <input type="hidden" name="id_anggota" value="<?= $row['id_anggota']; ?>">
                                <input type="hidden" name="modal_type" value="setor">
                                <button type="submit" name="open_modal" style="background: none; border: none;">
                                    <i class="fa-solid fa-circle-plus fa-2xl" style="color: #0BA16F"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Tidak ada data anggota.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
     <!-- Pagination -->
     <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?search=<?= urlencode($search); ?>&page=<?= $page - 1; ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?search=<?= urlencode($search); ?>&page=<?= $i; ?>" class="<?= ($i == $page) ? 'active' : ''; ?>"><?= $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?search=<?= urlencode($search); ?>&page=<?= $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Popup -->
<div class="modal" id="modalPopup" style="display: <?= $showModal ? 'flex' : 'none'; ?>">
    <div class="modal-content">
        <form method="POST">
            <button  name="close_popup" class="close-btn" style="top: 10px; left: 190px;">
                <i class="fa-regular fa-circle-xmark fa-2xl" style="color: #9d1010;"></i>
            </button>    
        </form>
        <h4><?= $modalType == "setor" ? "Setor Simpanan" : "Tarik Simpanan"; ?></h4>
        <form method="POST" action="update_simpanan.php">
            <input type="hidden" name="id_anggota" value="<?= htmlspecialchars($id_anggota_modal); ?>">
            <input type="hidden" name="action" value="<?= $modalType; ?>"> <!-- Tambahkan action -->
            <input type="number" name="jumlah" placeholder="Masukkan nominal" required>

        <?php if ($modalType == "setor"): ?>
        <h4 style="font-weight: bold; text-align: left;">Pilih Simpanan</h4>
            <div class="radio-group">
                <label>
                    <input type="radio" name="jenis_simpanan" value="simpanan_pokok" required>
                Simpanan Pokok
                </label>
                <label>
                    <input type="radio" name="jenis_simpanan" value="simpanan_wajib">
                Simpanan Wajib
                </label>
                <label>
                    <input type="radio" name="jenis_simpanan" value="simpanan_sukarela">
                Simpanan Sukarela
                </label>
            </div>
        <?php else: ?>
            <input type="hidden" name="jenis_simpanan" value="simpanan_sukarela">
        <?php endif; ?>

        <button type="submit">
            <?= $modalType == "setor" ? "Setor" : "Tarik"; ?>
        </button>
        </form>
    </div>
</div>
</body>
</html>