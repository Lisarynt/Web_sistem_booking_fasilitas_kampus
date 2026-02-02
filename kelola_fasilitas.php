<?php
require_once 'koneksi/connection.php';

$token = $_COOKIE['admin_auth_token'] ?? ''; 
if (!$token) {
    header("Location: login_admin.php");
    exit();
}

try {
    $tokenHash = hash('sha256', $token);
    $check = $database_connection->prepare("SELECT id_admin FROM admins WHERE cookie_token = ? LIMIT 1");
    $check->execute([$tokenHash]);
    if (!$check->fetch()) {
        header("Location: login_admin.php");
        exit();
    }
    $sql = "SELECT * FROM kategori_fasilitas ORDER BY nama_kategori ASC";
    $stmt = $database_connection->query($sql);
    $fasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Fasilitas - UniReserve Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; }
        .main-content { margin-left: 260px; padding: 40px; min-height: 100vh; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        .table-card { background: #fff; border-radius: 20px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-5 px-3">UniReserve Admin</h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard_admin.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link active" href="kelola_fasilitas.php"><i class="bi bi-building me-2"></i> Kelola Fasilitas</a>
        <a class="nav-link" href="validasi_peminjaman.php"><i class="bi bi-check-circle me-2"></i> Validasi</a>
        <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-text me-2"></i> Laporan</a>
    </nav>
    <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
    <i class="bi bi-box-arrow-left"></i> Keluar</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manajemen Fasilitas</h2>
        <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-2"></i> Tambah Fasilitas
        </button>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 15px;">
            Data fasilitas berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="table-card">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Fasilitas</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fasilitas as $f): ?>
                <tr>
                    <td>#<?= $f['id_kategori'] ?></td>
                    <td><strong><?= htmlspecialchars($f['nama_kategori']) ?></strong></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                onclick="editFasilitas(<?= $f['id_kategori'] ?>, '<?= htmlspecialchars($f['nama_kategori']) ?>')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="flow/proses_fasilitas.php?hapus=<?= $f['id_kategori'] ?>" 
                           class="btn btn-sm btn-outline-danger rounded-pill" 
                           onclick="return confirm('Hapus fasilitas ini? Semua data peminjaman terkait mungkin akan terpengaruh.')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</div>

<div class="modal fade" id="modalFasilitas" tabindex="-1">
    <div class="modal-dialog">
        <form action="flow/proses_fasilitas.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Fasilitas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_kategori" id="id_kategori">
                <div class="mb-3">
                    <label class="form-label">Nama Fasilitas/Ruangan</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" placeholder="Contoh: Lab Komputer 1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="simpan" class="btn btn-dark px-4">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modal = new bootstrap.Modal(document.getElementById('modalFasilitas'));
    const modalTambah = document.querySelector('[data-bs-target="#modalTambah"]');
    
    modalTambah.addEventListener('click', () => {
        document.getElementById('modalTitle').innerText = "Tambah Fasilitas Baru";
        document.getElementById('id_kategori').value = "";
        document.getElementById('nama_kategori').value = "";
        modal.show();
    });

    function editFasilitas(id, nama) {
        document.getElementById('modalTitle').innerText = "Edit Nama Fasilitas";
        document.getElementById('id_kategori').value = id;
        document.getElementById('nama_kategori').value = nama;
        modal.show();
    }
</script>
</body>
</html>