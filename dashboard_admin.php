<?php
require_once 'koneksi/connection.php';

// Logika Keamanan: Cek apakah yang login adalah admin
$token = $_COOKIE['admin_auth_token'] ?? ''; 
if (!$token) {
    header("Location: login_admin.php");
    exit();
}

try {
    // Ambil Data Statistik Utama
    $sql_stat = "SELECT 
        COUNT(CASE WHEN status_pengajuan = 'Pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status_pengajuan = 'Disetujui' THEN 1 END) as disetujui,
        (SELECT COUNT(*) FROM kategori_fasilitas) as total_fasilitas
        FROM peminjaman";
    $stmt_stat = $database_connection->query($sql_stat);
    $stat = $stmt_stat->fetch(PDO::FETCH_ASSOC);

    // Ambil Daftar Pengajuan yang perlu divalidasi (Status Pending)
    $sql_pending = "SELECT p.*, u.nama, k.nama_kategori 
                    FROM peminjaman p
                    JOIN data_user u ON p.id = u.id
                    JOIN kategori_fasilitas k ON p.id_kategori = k.id_kategori
                    WHERE p.status_pengajuan = 'Pending'
                    ORDER BY p.tgl_pinjam ASC LIMIT 5";
    $pending_list = $database_connection->query($sql_pending)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UniReserve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; z-index: 100; }
        .main-content { margin-left: 260px; padding: 40px; min-height: 100vh; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        .stat-card { border: none; border-radius: 20px; padding: 25px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .table-card { background: #fff; border-radius: 25px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .badge-pending { background: #FFF4E5; color: #FF9800; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column">
    <h3 class="fw-bold mb-5 px-3">UniReserve <span class="badge bg-danger" style="font-size: 0.4em;">ADMIN</span></h3>
    <nav class="nav flex-column flex-grow-1">
        <a class="nav-link active" href="dashboard_admin.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="kelola_fasilitas.php"><i class="bi bi-building me-2"></i> Kelola Fasilitas</a>
        <a class="nav-link" href="validasi_peminjaman.php"><i class="bi bi-check-circle me-2"></i> Validasi</a>
        <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-text me-2"></i> Laporan</a>
    </nav>
   <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
    <i class="bi bi-box-arrow-left"></i> Keluar</a>
</div>

<div class="main-content">
    <header class="mb-5">
        <h2 class="fw-bold">Admin Central Control 🛠️</h2>
        <p class="text-muted">Kelola ketersediaan fasilitas dan validasi pengajuan hari ini.</p>
    </header>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card text-center">
                <h6 class="text-muted mb-2">Perlu Validasi</h6>
                <h2 class="fw-bold text-warning"><?= $stat['pending'] ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <h6 class="text-muted mb-2">Total Disetujui</h6>
                <h2 class="fw-bold text-success"><?= $stat['disetujui'] ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <h6 class="text-muted mb-2">Total Fasilitas</h6>
                <h2 class="fw-bold text-dark"><?= $stat['total_fasilitas'] ?></h2>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold">Antrean Pengajuan Masuk</h5>
            <a href="validasi_peminjaman.php" class="btn btn-sm btn-outline-dark">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Fasilitas</th>
                        <th>Waktu Pinjam</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pending_list)): ?>
                        <tr><td colspan="4" class="text-center py-4">Tidak ada pengajuan pending.</td></tr>
                    <?php else: ?>
                        <?php foreach($pending_list as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['nama']) ?></strong></td>
                            <td><?= htmlspecialchars($p['nama_kategori']) ?></td>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($p['tgl_pinjam'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-success rounded-pill px-3" onclick="validasi(<?= $p['id_peminjaman'] ?>, 'Setujui')">Setujui</button>
                                <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="tolak(<?= $p['id_peminjaman'] ?>)">Tolak</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1">
  <div class="modal-dialog">
    <form action="flow/proses_validasi.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alasan Penolakan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_peminjaman" id="id_tolak">
        <input type="hidden" name="aksi" value="Ditolak">
        <textarea name="alasan" class="form-control" placeholder="Contoh: Ruangan digunakan untuk acara rektorat" required></textarea>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Kirim & Tolak</button>
      </div>
    </form>
  </div>
  <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function validasi(id, aksi) {
        if(confirm('Setujui peminjaman ini?')) {
            window.location.href = `flow/proses_validasi.php?id=${id}&aksi=Disetujui`;
        }
    }

    function tolak(id) {
        document.getElementById('id_tolak').value = id;
        new bootstrap.Modal(document.getElementById('modalTolak')).show();
    }
</script>
</body>
</html>