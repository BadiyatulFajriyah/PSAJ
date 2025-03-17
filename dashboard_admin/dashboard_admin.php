<?php
session_start();
include_once("../config/koneksi.php");

$current_page = basename($_SERVER['PHP_SELF']);


if (!isset($_SESSION['username'])) {
    header("Location: ../login/login_admin.php");
    exit;
}

class AnggotaController {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAnggota($start, $limit, $search = "") {
        if ($search) {
            $query = "SELECT * FROM anggota WHERE nama_anggota LIKE '%$search%' OR telepon LIKE '%$search%' OR username LIKE '%$search%' ORDER BY nama_anggota ASC LIMIT $start, $limit";
        } else {
            $query = "SELECT * FROM anggota ORDER BY nama_anggota ASC LIMIT $start, $limit";
        }
        return mysqli_query($this->conn, $query);
    }
    
    public function countAnggota() {
        $total_query = "SELECT COUNT(*) AS total FROM anggota";
        $total_result = mysqli_query($this->conn, $total_query);
        $total_row = mysqli_fetch_assoc($total_result);
        return $total_row['total'];
    }
}

class AdminController {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }
    public function getAdmin($start, $limit) {
        return mysqli_query($this->conn, "SELECT id_admin, username, password FROM admin ORDER BY id_admin ASC LIMIT $start, $limit");
    }
    public function countAdmin() {
        $total_query = "SELECT COUNT(*) AS total FROM admin";
        $total_result = mysqli_query($this->conn, $total_query);
        $total_row = mysqli_fetch_assoc($total_result);
        return $total_row['total'];
    }
}

$anggotaController = new AnggotaController($conn);
$adminController = new AdminController($conn);

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : "";
$result = $anggotaController->getAnggota($start, $limit, $search);
$total_pages = ceil($anggotaController->countAnggota() / $limit);

// Pagination untuk admin
$admin_limit = 5;
$admin_page = isset($_GET['admin_page']) ? (int)$_GET['admin_page'] : 1;
$admin_start = ($admin_page - 1) * $admin_limit;
$admin_result = $adminController->getAdmin($admin_start, $admin_limit);
$total_admin_pages = ceil($adminController->countAdmin() / $admin_limit);

// Mengontrol popup menggunakan session
if (isset($_POST['open_popup'])) $_SESSION['showPopupTambah'] = true;
if (isset($_POST['open_popup_admin'])) $_SESSION['showPopupTambahAdmin'] = true;
if (isset($_POST['open_edit_popup'])) $_SESSION['showPopupEdit'] = $_POST['id_anggota'];
if (isset($_POST['lihat_daftar_admin'])) $_SESSION['showDaftarAdmin'] = true;
if (isset($_POST['tutup_daftar_admin'])) unset($_SESSION['showDaftarAdmin']);
if (isset($_POST['close_popup'])) {
    unset($_SESSION['showPopupTambah'], $_SESSION['showPopupTambahAdmin'], $_SESSION['showPopupEdit'], $_SESSION['showDaftarAdmin']);
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiJam - Anggota</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/popup_admin.css">
    <link rel="stylesheet" href="../css/search.css">
    <link rel="stylesheet" href="../css/admin+.css">
</head>
<body>

<?php
include_once("header.php");
?>

<!-- Kontainer utama -->
<div class="container">
    <div class="search-container">
        <form method="POST" class="btn-container">
            <button type="submit" name="open_popup" class="btn-tambah">
                <i class="fa fa-plus"></i> Tambah Anggota
            </button>
        </form>

        <form method="POST" class="search-container">
            <input type="search" name="search" class="search-box" placeholder="Cari..." 
                value="<?= isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '' ?>">
            <button type="submit" name="submit_search" class="btn-search">
                <i class="fa fa-search"></i> Cari
            </button>
        </form>

        <form method="POST">
            <button type="submit" name="open_popup_admin" class="btn-tambah-admin">
                <i class="fa fa-plus"></i> Tambah Admin
            </button>
        </form>
    </div>

    <div class="table-container">
        <div style="font-size: 18px; color: #375248; margin-bottom: 10px;">
            👋Selamat datang, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
        </div>
        <div class="header-container">
            <h4>Daftar Anggota</h4>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No. Telepon</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = $start + 1;
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_anggota']); ?></td>
                    <td><?= htmlspecialchars($row['telepon']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['password']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_anggota" value="<?= $row['id_anggota']; ?>">
                            <button type="submit" name="open_edit_popup" class="edit">Edit</button>
                        </form>
                        <a href="delete_anggota.php?id_anggota=<?= $row['id_anggota']; ?>" class="delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
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

<!-- Popup Tambah Anggota -->
<?php if (isset($_SESSION["showPopupTambah"])): ?>
    <div class="overlay"></div>
    <div class="popup">
        <form method="POST">
            <button  name="close_popup" class="close-btn">
                <i class="fa-regular fa-circle-xmark fa-xl" style="color: #9d1010;"></i>
            </button>    
        </form>
        <h3>Tambah Anggota</h3>
        <form method="POST" action="proses_anggota.php">
            <input type="text" name="nama_anggota" placeholder="Masukkan Nama Lengkap" required><br>
            <input type="text" name="telepon" placeholder="Masukkan No. Telp" required><br>
            <input type="text" name="username" placeholder="Masukkan username" required><br>
            <input type="password" name="password" placeholder="Masukkan password" required 
                   style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; 
                   border-radius: 5px; box-sizing: border-box; ">
            <button type="submit" name="simpan_anggota">Simpan</button>
        </form>  
    </div>
<?php endif; ?>


<!-- Popup Tambah Admin -->
<?php if (isset($_SESSION["showPopupTambahAdmin"])): ?>
    <div class="overlay"></div>
    <div class="popup">
        <form method="POST">
            <button  name="close_popup" class="close-btn">
                <i class="fa-regular fa-circle-xmark fa-xl" style="color: #9d1010;"></i>
            </button>    
        </form>
        <h3>Tambah Admin</h3>
        <form method="POST" action="proses_anggota.php">
            <input type="text" name="username" placeholder="Masukkan username" required><br>
            <input type="password" name="password" placeholder="Masukkan password" required 
                   style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; 
                   border-radius: 5px; box-sizing: border-box; ">
            <button type="submit" name="simpan_admin">Simpan</button>
        </form>

        <!-- Tombol untuk melihat daftar admin -->
        <form method="POST">
            <button type="submit" name="lihat_daftar_admin">Lihat Daftar Admin</button>
        </form>
    </div>
<?php endif; ?>


<!-- Popup Daftar Admin -->
<?php if (isset($_SESSION["showDaftarAdmin"])): ?>
    <div class="overlay"></div>
    <div class="popup">
        <h3>Daftar Admin</h3>
        <div class="table-scroll">
            <table width="100%">
            <tr>
                <th>Username</th>
                <th>Password</th>
                <th>Aksi</th>
            </tr>
                <?php while ($row = mysqli_fetch_assoc($admin_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['password']); ?></td>
                    <td>
                        <a href='delete_admin.php?id_admin=<?= $row['id_admin']; ?>' 
                            onclick='return confirm("Yakin ingin menghapus admin ini?")' 
                            class='delete-admin'>
                            <i class='fa fa-trash' style='color: red;'></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Pagination Admin -->
        <div class="pagination">
            <?php if ($total_admin_pages > 1): ?>
                <?php for ($i = 1; $i <= $total_admin_pages; $i++): ?>
                    <a href="?admin_page=<?= $i ?>" class="<?= $i == $admin_page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            <?php endif; ?>
        </div>

        <form method="POST">
            <button  name="close_popup" class="close-btn">
                <i class="fa-regular fa-circle-xmark fa-xl" style="color: #9d1010;"></i>
            </button>    
        </form>
    </div>
<?php endif; ?>

<!-- Popup Edit Anggota -->
<?php 
if (isset($_SESSION["showPopupEdit"])): 
    $id = $_SESSION["showPopupEdit"];
    $result_edit = mysqli_query($conn, "SELECT * FROM anggota WHERE id_anggota='$id'");
    $anggota = mysqli_fetch_assoc($result_edit);
?>
    <div class="overlay"></div>
    <div class="popup">
        <form method="POST">
            <button  name="close_popup" class="close-btn">
                <i class="fa-regular fa-circle-xmark fa-xl" style="color: #9d1010;"></i>
            </button>    
        </form>
        <h3>Edit Anggota</h3>
        <form method="POST" action="proses_anggota.php">
            <input type="hidden" name="id_anggota" value="<?= $anggota['id_anggota']; ?>">
            <input type="text" name="nama_anggota" value="<?= htmlspecialchars($anggota['nama_anggota']); ?>" required><br>
            <input type="text" name="telepon" value="<?= htmlspecialchars($anggota['telepon']); ?>" required><br>
            <button type="submit" name="update_anggota">Update</button>
        </form>
    </div>
<?php endif; ?>
</body>
</html>
