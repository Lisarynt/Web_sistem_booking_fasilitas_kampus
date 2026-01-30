<?php
require_once 'koneksi/connection.php';

// Proteksi Admin
$token = $_COOKIE['admin_auth_token'] ?? ''; 
if (!$token) {
    header("Location: login_admin.php");
    exit();
}

try {
    // Ambil semua data peminjaman, gabung dengan nama user dan nama kategori
    $sql = "SELECT p.*, u.nama, u.nim, k.nama_kategori 
            FROM peminjaman p
            JOIN data_user u ON p.id = u.id
            JOIN kategori_fasilitas k ON p.id_kategori = k.id_kategori
            ORDER BY 
                CASE WHEN p.status_pengajuan = 'Pending' THEN 1 ELSE 2 END, 
                p.tgl_pinjam DESC";
                
    $stmt = $database_connection->query($sql);
    $semua_peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi Peminjaman - UniReserve Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; }
        .main-content { margin-left: 260px; padding: 40px; min-height: 100vh; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        .table-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .status-badge { font-size: 0.85rem; padding: 6px 12px; border-radius: 50px; font-weight: 600; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-5 px-3">UniReserve Admin</h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard_admin.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="kelola_fasilitas.php"><i class="bi bi-building me-2"></i> Kelola Fasilitas</a>
        <a class="nav-link active" href="validasi_peminjaman.php"><i class="bi bi-check-circle me-2"></i> Validasi</a>
        <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-text me-2"></i> Laporan</a>
    </nav>
    <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
    <i class="bi bi-box-arrow-left"></i> Keluar</a>
</div>

<div class="main-content">
    <header class="mb-5">
        <h2 class="fw-bold">Persetujuan Peminjaman ✅</h2>
        <p class="text-muted">Kelola seluruh riwayat dan validasi pengajuan fasilitas mahasiswa.</p>
    </header>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Fasilitas & Tujuan</th>
                        <th>Waktu Pinjam</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($semua_peminjaman as $p): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($p['nama']) ?></div>
                            <small class="text-muted"><?= $p['nim'] ?></small>
                        </td>
                        <td>
                            <div class="fw-bold text-primary"><?= htmlspecialchars($p['nama_kategori']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($p['deskripsi_kegiatan']) ?></small>
                        </td>
                        <td>
                            <div class="small"><?= date('d M Y', strtotime($p['tgl_pinjam'])) ?></div>
                            <div class="small fw-bold"><?= date('H:i', strtotime($p['tgl_pinjam'])) ?> WIB</div>
                        </td>
                        <td>
                            <?php 
                                $s = $p['status_pengajuan'];
                                $class = ($s == 'Disetujui') ? 'success' : (($s == 'Ditolak') ? 'danger' : 'warning');
                            ?>
                            <span class="badge bg-<?= $class ?>-subtle text-<?= $class ?> status-badge">
                                <?= $s ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($s == 'Pending'): ?>
                                <button class="btn btn-sm btn-success rounded-pill px-3" onclick="validasi(<?= $p['id_peminjaman'] ?>, 'Setujui')">Setujui</button>
                                <button class="btn btn-sm btn-danger rounded-pill px-3" onclick="tolak(<?= $p['id_peminjaman'] ?>)">Tolak</button>
                            <?php else: ?>
                                <span class="text-muted small">Sudah Diproses</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1">
  <div class="modal-dialog">
    <form action="flow/proses_validasi.php" method="POST" class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Berikan Alasan Penolakan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_peminjaman" id="id_tolak">
        <input type="hidden" name="aksi" value="Ditolak">
        <textarea name="alasan" class="form-control" rows="4" placeholder="Sebutkan alasan penolakan agar mahasiswa mengerti..." required></textarea>
      </div>
      <div class="modal-footer border-0">
        <button type="submit" class="btn btn-danger w-100 rounded-pill">Konfirmasi Penolakan</button>
      </div>
    </form>
  </div>
  <?php include 'footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function validasi(id, aksi) {
        if(confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?')) {
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