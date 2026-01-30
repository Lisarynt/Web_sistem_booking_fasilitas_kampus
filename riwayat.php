<?php
require_once 'koneksi/connection.php';
// Ambil token dari cookie
$token = $_COOKIE['user_auth_token'] ?? ''; 

if (!$token) {
    header("Location: login.php");
    exit();
}

// Cari user berdasarkan TOKEN (Sama seperti di Dashboard)
$tokenHash = hash('sha256', $token);
$sql_user = "SELECT id FROM data_user WHERE token = ?";
$stmt_user = $database_connection->prepare($sql_user);
$stmt_user->execute([$tokenHash]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit();
}

$id_user = $user['id']; // Sekarang berisi ID pengguna yang login

try {
    // Ambil data riwayat pinjam gabung dengan nama kategori (JOIN)
    $sql = "SELECT p.*, k.nama_kategori 
            FROM peminjaman p 
            JOIN kategori_fasilitas k ON p.id_kategori = k.id_kategori 
            WHERE p.id = ? 
            ORDER BY p.tgl_pinjam DESC";
    $stmt = $database_connection->prepare($sql);
    $stmt->execute([$id_user]);
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman - UniReserve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; }
        .main-content { margin-left: 260px; padding: 40px; min-height: 100vh; display: flex; flex-direction: column; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        .table-card { background: #fff; border-radius: 20px; padding: 30px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .btn-action { display: flex; gap: 5px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-5 px-3 text-white">UniReserve</h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="katalog.php"><i class="bi bi-search me-2"></i> Katalog</a>
        <a class="nav-link" href="form_pinjam.php"><i class="bi bi-plus-square me-2"></i> Form Pinjam</a>
        <a class="nav-link active" href="riwayat.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
        <a class="nav-link" href="profil.php"><i class="bi bi-person me-2"></i> Profil</a> 
    </nav>
    <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
        <i class="bi bi-box-arrow-left"></i> Keluar
    </a>
</div>

<div class="main-content">
    <?php if (isset($_GET['status'])): ?>
            <?php 
                $msg = "";
                $class = "info";
                if($_GET['status'] == 'success') { $msg = "ditambahkan"; $class = "success"; }
                if($_GET['status'] == 'updated') { $msg = "diperbarui"; $class = "info"; }
                if($_GET['status'] == 'deleted') { $msg = "dibatalkan/dihapus"; $class = "danger"; }
            ?>
            <div class="alert alert-<?= $class ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
                <i class="bi bi-check-circle-fill me-2"></i>
                Data peminjaman berhasil **<?= $msg ?>**!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

    <h2 class="fw-bold mb-4">Riwayat Peminjaman</h2>
    
    <div class="table-card">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Fasilitas</th>
                    <th>Kegiatan</th>
                    <th>Waktu Pinjam</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($riwayat)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-info-circle me-2"></i> Belum ada data peminjaman untuk akun kamu.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($riwayat as $row): ?>
                <tr>
                    <td>
                        <span class="fw-bold"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                        <?php if ($row['status_pengajuan'] == 'Ditolak' && !empty($row['alasan_penolakan'])): ?>
                            <div class="mt-2 p-2 small bg-danger-subtle text-danger rounded border border-danger-subtle" style="max-width: 200px;">
                                <strong>Alasan:</strong> <?= htmlspecialchars($row['alasan_penolakan']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['deskripsi_kegiatan']) ?></td>
                    <td class="small text-muted"><?= date('d M Y, H:i', strtotime($row['tgl_pinjam'])) ?></td>
                    <td>
                        <?php 
                        $status_raw = $row['status_pengajuan'];
                        $class = $status_raw == 'Disetujui' ? 'success' : ($status_raw == 'Ditolak' ? 'danger' : 'warning');
                        ?>
                        <span class="badge bg-<?= $class ?>-subtle text-<?= $class ?> rounded-pill px-3"><?= $status_raw ?></span>
                    </td>
                    <td>
                        <div class="btn-action justify-content-center">
                            <?php if ($status_raw == 'Disetujui'): ?>
                                <a href="cetak_bukti.php?id=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-outline-dark rounded-pill" title="Eksport PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            <?php elseif ($status_raw == 'Pending'): ?>
                                <button onclick="alert('Pengajuan masih diproses. Eksport PDF tersedia setelah disetujui admin.')" class="btn btn-sm btn-outline-secondary rounded-pill" title="Eksport PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </button>
                            <?php else: ?>
                                <button onclick="alert('Pengajuan ditolak. Dokumen PDF tidak tersedia.')" class="btn btn-sm btn-outline-danger rounded-pill" title="Eksport PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </button>
                            <?php endif; ?>

                            <?php if($status_raw == 'Pending'): ?>
                                <a href="form_pinjam.php?edit=<?= $row['id_peminjaman'] ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="flow/delete_data.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')" style="display:inline;">
                                    <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>