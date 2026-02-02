<?php
require_once 'koneksi/connection.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$token = $_COOKIE['user_auth_token'] ?? ''; 
if (!$token) {
    header("Location: login.php");
    exit();
}

$tokenHash = hash('sha256', $token);
$stmt_auth = $database_connection->prepare("SELECT id FROM data_user WHERE token = ?");
$stmt_auth->execute([$tokenHash]);
$user_logged = $stmt_auth->fetch();

if (!$user_logged) {
    header("Location: login.php");
    exit();
}
$id_mahasiswa_login = $user_logged['id'];

$data_edit = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $stmt = $database_connection->prepare("SELECT * FROM peminjaman WHERE id_peminjaman = ?");
    $stmt->execute([$id_edit]);
    $data_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
    $query_kategori = $database_connection->query("SELECT * FROM kategori_fasilitas");
    $categories = $query_kategori->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pinjam Fasilitas - UniReserve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; }
        .main-content { margin-left: 260px; padding: 40px; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        .form-card { background: #fff; border-radius: 25px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); border: none; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; border: 1px solid #E9EAEB; background: #FBFBFB; }
        .btn-submit { background: #B4F481; color: #1A1C1E; border: none; padding: 15px; border-radius: 12px; font-weight: 700; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #a2db74; transform: translateY(-2px); }
    </style>

    <?php include 'checkcookie.php'; ?>
    <script>
        checkLoginStatus(); 
    </script>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-5 px-3 text-white">UniReserve</h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="katalog.php"><i class="bi bi-search me-2"></i> Katalog</a>
        <a class="nav-link active" href="form_pinjam.php"><i class="bi bi-plus-square me-2"></i> Form Pinjam</a>
        <a class="nav-link" href="riwayat.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
        <a class="nav-link" href="profil.php"><i class="bi bi-person me-2"></i> Profil</a>
    </nav>
    <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
        <i class="bi bi-box-arrow-left"></i> Keluar
    </a>
</div>

<div class="main-content">
    <div class="container-fluid">
        <header class="mb-5">
            <h2 class="fw-bold"><?= isset($data_edit) ? 'Edit Pengajuan' : 'Form Pengajuan Peminjaman' ?></h2>
            <p class="text-muted">Silakan lengkapi data untuk meminjam fasilitas kampus.</p>
        </header>

        <div class="row">
            <div class="col-lg-8">
                <div class="form-card">
                    <form action="flow/insup_peminjaman.php" method="POST">
                        <input type="hidden" name="id_peminjaman" value="<?= $data_edit['id_peminjaman'] ?? '' ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Pilih Fasilitas</label>
                                <select name="id_kategori" id="id_kategori" class="form-select" required onchange="updateLabel()">
                                    <option value="" disabled <?= !isset($data_edit) ? 'selected' : '' ?>>Pilih kategori...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id_kategori'] ?>" <?= (isset($data_edit) && $data_edit['id_kategori'] == $cat['id_kategori']) ? 'selected' : '' ?>>
                                            <?= $cat['nama_kategori'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label id="label_kapasitas" class="form-label fw-semibold">Jumlah/Kapasitas</label>
                                <input type="number" name="kapasitas_jumlah" class="form-control" value="<?= $data_edit['kapasitas_jumlah'] ?? '' ?>" placeholder="Contoh: 30" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Pinjam</label>
                                <input type="datetime-local" name="tgl_pinjam" class="form-control" value="<?= isset($data_edit) ? date('Y-m-d\TH:i', strtotime($data_edit['tgl_pinjam'])) : '' ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Kembali</label>
                                <input type="datetime-local" name="tgl_kembali" class="form-control" value="<?= isset($data_edit) ? date('Y-m-d\TH:i', strtotime($data_edit['tgl_kembali'])) : '' ?>" required>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <label class="form-label fw-semibold">Deskripsi Kegiatan</label>
                                <textarea name="deskripsi_kegiatan" class="form-control" rows="4" placeholder="Jelaskan tujuan peminjaman Anda..." required><?= $data_edit['deskripsi_kegiatan'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn-submit">
                                    <?= isset($data_edit) ? 'Simpan Perubahan' : 'Kirim Pengajuan Sekarang' ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 p-4 text-white" style="background: #1A1C1E;">
                    <h5 class="fw-bold mb-3">Informasi Peminjaman</h5>
                    <ul class="small opacity-75">
                        <li class="mb-2">Pengajuan diproses maksimal 2x24 jam.</li>
                        <li class="mb-2">Pastikan jadwal tidak bentrok dengan kegiatan lain.</li>
                        <li>Cek status secara berkala di menu Riwayat.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<script>
    function updateLabel() {
        const select = document.getElementById('id_kategori');
        const label = document.getElementById('label_kapasitas');
        const selectedText = select.options[select.selectedIndex].text;

        if (selectedText.toLowerCase() === 'alat') {
            label.innerText = 'Jumlah Alat yang Dibutuhkan';
        } else if (selectedText.toLowerCase() === 'ruangan') {
            label.innerText = 'Kapasitas Orang (Peserta)';
        } else {
            label.innerText = 'Jumlah/Kapasitas';
        }
    }
    window.onload = updateLabel;
</script>

</body>
</html>