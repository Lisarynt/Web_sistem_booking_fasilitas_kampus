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
    $admin = $check->fetch();
    
    if (!$admin) {
        header("Location: login_admin.php");
        exit();
    }
    
    $sql = "SELECT p.*, u.nama, u.nim, k.nama_kategori 
            FROM peminjaman p 
            JOIN data_user u ON p.id = u.id 
            JOIN kategori_fasilitas k ON p.id_kategori = k.id_kategori 
            ORDER BY p.tgl_pinjam DESC";
    $stmt = $database_connection->prepare($sql);
    $stmt->execute();
    $semua_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Admin - UniReserve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; }
        .main-content { margin-left: 260px; padding: 40px; min-height: 100vh; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        .report-card { background: #fff; border-radius: 20px; padding: 30px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .badge-admin { background: #FF4B5C; color: white; font-size: 0.7rem; padding: 2px 8px; border-radius: 5px; text-transform: uppercase; }
        .btn-export { background: #1A1C1E; color: white; border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: 0.3s; }
        .btn-export:hover { background: #B4F481; color: #1A1C1E; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-1 px-3 text-white">UniReserve</h3>
    <div class="px-3 mb-5"><span class="badge-admin">Admin</span></div>
    
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard_admin.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="kelola_fasilitas.php"><i class="bi bi-building me-2"></i> Kelola Fasilitas</a>
        <a class="nav-link" href="validasi_peminjaman.php"><i class="bi bi-check-circle me-2"></i> Validasi</a>
        <a class="nav-link active" href="laporan.php"><i class="bi bi-file-earmark-text me-2"></i> Laporan</a>
    </nav>
    <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
    <i class="bi bi-box-arrow-left"></i> Keluar</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Laporan Central Control 📊</h2>
            <p class="text-muted">Rekapitulasi seluruh data peminjaman dalam format Excel.</p>
        </div>
        <button onclick="downloadLaporanExcel()" class="btn btn-export shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet-fill me-2"></i> Eksport Excel
        </button>
    </div>

<div class="report-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tabelLaporan">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">NIM</th>
                        <th class="py-3">Peminjam</th>
                        <th class="py-3">Fasilitas</th>
                        <th class="py-3">Waktu Pinjam</th>
                        <th class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($semua_data as $row): ?>
                    <tr>
                        <td><?= $row['nim'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td><?= date('d M Y, H:i', strtotime($row['tgl_pinjam'])) ?></td>
                        <td>
                            <?php 
                                $s = $row['status_pengajuan'];
                                $color = ($s == 'Disetujui') ? 'success' : (($s == 'Ditolak') ? 'danger' : 'warning');
                            ?>
                            <span class="badge bg-<?= $color ?>-subtle text-<?= $color ?> rounded-pill px-3">
                                <?= $s ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<script>
function downloadLaporanExcel() {
    var table = document.getElementById("tabelLaporan");
    var ws = XLSX.utils.table_to_sheet(table);
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Laporan Peminjaman");

    var fileName = "Laporan_UniReserve_" + new Date().toLocaleDateString().replace(/\//g, '-') + ".xlsx";
    XLSX.writeFile(wb, fileName);
}
</script>

</body>
</html>