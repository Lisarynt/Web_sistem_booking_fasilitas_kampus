<?php
require_once 'koneksi/connection.php';

try {
    // Mengambil semua data kategori fasilitas
    $query = $database_connection->query("SELECT * FROM kategori_fasilitas");
    $fasilitas = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Fasilitas - UniReserve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; }
        .main-content { margin-left: 260px; padding: 40px; min-height: 100vh; display: flex; flex-direction: column; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        
        /* Card Styling ala Eduplex */
        .katalog-card { border: none; border-radius: 20px; transition: 0.3s; background: #fff; overflow: hidden; }
        .katalog-card:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-box { width: 60px; height: 60px; background: #F0FDF4; color: #16A34A; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; mb-3; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-5 px-3 text-white">UniReserve</h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link active" href="katalog.php"><i class="bi bi-search me-2"></i> Katalog</a>
        <a class="nav-link" href="form_pinjam.php"><i class="bi bi-plus-square me-2"></i> Form Pinjam</a>
        <a class="nav-link" href="riwayat.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
        <a class="nav-link" href="profil.php"><i class="bi bi-person me-2"></i> Profil</a> 
    </nav>
    <a href="#" id="logout" class="nav-link text-danger mt-auto">
        <i class="bi bi-box-arrow-left"></i> Keluar
    </a>
</div>

<div class="main-content">
    <header class="mb-5">
        <h2 class="fw-bold">Katalog Fasilitas</h2>
        <p class="text-muted">Daftar sarana dan prasarana yang tersedia untuk dipinjam.</p>
    </header>

    <div class="row g-4">
        <?php foreach ($fasilitas as $item): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card katalog-card p-4">
                <div class="icon-box">
                    <i class="bi <?= (strtolower($item['nama_kategori']) == 'ruangan') ? 'bi-building' : 'bi-tools' ?>"></i>
                </div>
                <h5 class="fw-bold mb-2"><?= $item['nama_kategori'] ?></h5>
                <p class="text-muted small mb-4"><?= $item['deskripsi_kategori'] ?></p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="badge bg-light text-dark rounded-pill px-3">Tersedia</span>
                    <a href="form_pinjam.php" class="btn btn-sm btn-dark rounded-pill px-3">Pesan Sekarang</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div><?php include 'footer.php'; ?>
</div>
</body>
</html>