<?php
require_once 'koneksi/connection.php';

$token = $_COOKIE['user_auth_token'] ?? ''; 

if (!$token) {
    header("Location: login.php");
    exit();
}

try {
    $tokenHash = hash('sha256', $token);
    $sql_user = "SELECT id, nama FROM data_user WHERE token = ?";
    $stmt_user = $database_connection->prepare($sql_user);
    $stmt_user->execute([$tokenHash]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        setcookie("user_auth_token", "", time() - 3600, "/");
        header("Location: login.php");
        exit();
    }

    $id_mhs = $user['id']; 
    $nama_user = $user['nama']; 

    $sql_stat = "SELECT 
        COUNT(CASE WHEN status_pengajuan = 'Pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status_pengajuan = 'Disetujui' THEN 1 END) as sukses,
        COUNT(CASE WHEN status_pengajuan = 'Ditolak' THEN 1 END) as gagal
        FROM peminjaman WHERE id = ?"; 
    $stmt_stat = $database_connection->prepare($sql_stat);
    $stmt_stat->execute([$id_mhs]);
    $stat = $stmt_stat->fetch(PDO::FETCH_ASSOC);

    $pending_count = (int)($stat['pending'] ?? 0);
    $sukses_count = (int)($stat['sukses'] ?? 0);
    $gagal_count = (int)($stat['gagal'] ?? 0);

    $sql_chart = "SELECT WEEKDAY(tgl_pinjam) as index_hari, COUNT(*) as total 
                FROM peminjaman 
                WHERE id = ? 
                AND tgl_pinjam >= CURDATE() 
                AND tgl_pinjam <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                GROUP BY index_hari";
    $stmt_chart = $database_connection->prepare($sql_chart);
    $stmt_chart->execute([$id_mhs]);
    $chart_results = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);

    $chart_values = [0, 0, 0, 0, 0, 0, 0]; 
    foreach ($chart_results as $row) {
        $chart_values[(int)$row['index_hari']] = (int)$row['total'];
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniReserve - Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; z-index: 100; }
        .main-content { margin-left: 260px; padding: 40px; min-height: 100vh; display: flex; flex-direction: column; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        .stat-card { border: none; border-radius: 20px; padding: 25px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .chart-container, .action-card { background: #fff; border-radius: 25px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); height: 100%; }
        .btn-action-primary { background: #1A1C1E; color: #fff; border-radius: 12px; padding: 12px; width: 100%; border: none; font-weight: 600; margin-bottom: 12px; }
        .btn-action-outline { background: transparent; color: #1A1C1E; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px; width: 100%; font-weight: 600; }
        
        .dropdown-menu { border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 15px; padding: 10px; }
        .dropdown-item { border-radius: 8px; padding: 10px 15px; transition: 0.2s; }
        .dropdown-item:hover { background-color: #F8F9FD; color: #1A1C1E; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column">
    <h3 class="fw-bold mb-5 px-3">UniReserve</h3>
    <nav class="nav flex-column flex-grow-1">
        <a class="nav-link active" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="katalog.php"><i class="bi bi-search me-2"></i> Katalog</a>
        <a class="nav-link" href="form_pinjam.php"><i class="bi bi-plus-square me-2"></i> Form Pinjam</a>
        <a class="nav-link" href="riwayat.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
        <a class="nav-link" href="profil.php"><i class="bi bi-person me-2"></i> Profil</a> 
    </nav>
    <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
        <i class="bi bi-box-arrow-left me-2"></i> Keluar
    </a>
</div>

<div class="main-content">
    <header class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold">Welcome back, <?= htmlspecialchars($nama_user) ?>! 👋</h2>
            <p class="text-muted small">Cek status peminjaman fasilitas kamu hari ini.</p>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-sm-block">
                <p class="mb-0 fw-bold small text-dark"><?= htmlspecialchars($nama_user) ?></p>
                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Mahasiswa</p> 
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="shadow-sm d-flex align-items-center justify-content-center fw-bold" 
                         style="width: 45px; height: 45px; background-color: #B4F481; color: #1A1C1E; border-radius: 50%; cursor: pointer;">
                         <?= strtoupper(substr($nama_user, 0, 1)) ?>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-2">
                    <li><h6 class="dropdown-header text-dark fw-bold">Menu Akun</h6></li>
                    <li><a class="dropdown-item" href="profil.php"><i class="bi bi-person-circle me-2"></i> Lihat Profil</a></li>
                    <li><a class="dropdown-item" href="riwayat.php"><i class="bi bi-clock-history me-2"></i> Riwayat Pinjam</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="flow/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Keluar</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <span class="badge bg-warning-subtle text-warning rounded-pill mb-2 px-3">Pending</span>
                <p class="text-muted small mb-1">Menunggu Antrian</p>
                <h3 class="fw-bold mb-0"><?= $pending_count ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <span class="badge bg-success-subtle text-success rounded-pill mb-2 px-3">Disetujui</span>
                <p class="text-muted small mb-1">Peminjaman Sukses</p>
                <h3 class="fw-bold mb-0"><?= $sukses_count ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <span class="badge bg-danger-subtle text-danger rounded-pill mb-2 px-3">Ditolak</span>
                <p class="text-muted small mb-1">Pengajuan Gagal</p>
                <h3 class="fw-bold mb-0"><?= $gagal_count ?></h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="fw-bold mb-4">Tren Aktivitas Peminjaman</h5>
                <canvas id="peminjamanChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="action-card">
                <h5 class="fw-bold mb-4">Aksi Cepat</h5>
                <button onclick="window.location.href='form_pinjam.php'" class="btn-action-primary">Buat Pengajuan Baru</button>
                <button onclick="window.location.href='riwayat.php'" class="btn-action-outline">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Download (PDF)
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('peminjamanChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Total Pinjam',
                    data: <?= json_encode($chart_values) ?>,
                    backgroundColor: '#B4F481',
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        function downloadPDF() {
            var canvas = document.getElementById('peminjamanChart');
            var imgData = canvas.toDataURL('image/png');
            var docDefinition = {
                content: [
                    { text: 'LAPORAN AKTIVITAS PEMINJAMAN', fontSize: 18, bold: true, margin: [0, 0, 0, 10] },
                    { text: 'Mahasiswa: <?= htmlspecialchars($nama_user) ?>', margin: [0, 0, 0, 20] },
                    { image: imgData, width: 500 }
                ]
            };
            pdfMake.createPdf(docDefinition).download('Laporan_Peminjaman.pdf');
        }
    </script>
    <?php include 'footer.php'; ?>
</div>
</body>
</html>