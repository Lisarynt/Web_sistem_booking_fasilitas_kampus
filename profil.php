<?php
require_once 'koneksi/connection.php';

$token = $_COOKIE['user_auth_token'] ?? '';
if ($token === '') {
    header("Location: index.php");
    exit;
}

$tokenHash = hash('sha256', $token);
$stmt = $database_connection->prepare("SELECT * FROM data_user WHERE token = ?");
$stmt->execute([$tokenHash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User tidak ditemukan. Silakan login kembali.");
}

$nama_user = $user['nama'];
$nim_user = $user['nim'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - UniReserve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #F8F9FD; font-family: 'Inter', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: #1A1C1E; color: #fff; position: fixed; padding: 30px 20px; z-index: 100; }
        .main-content { margin-left: 260px; padding: 40px; }
        .nav-link { color: #8E9196; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { background: #B4F481; color: #1A1C1E; font-weight: 600; }
        
        .profile-card-horizontal { 
            background: #fff; 
            border-radius: 25px; 
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: none;
            display: flex;
            align-items: stretch;
            min-height: 350px;
        }
        
        .profile-left { 
            background: #1A1C1E; 
            width: 35%; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            padding: 40px; 
            color: #fff;
        }
        
        .profile-avatar-large { 
            width: 140px; 
            height: 140px; 
            background: #2A2D30; 
            color: #B4F481; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 60px; 
            margin-bottom: 20px; 
            border: 4px solid rgba(180, 244, 129, 0.2);
        }

        .profile-right { 
            width: 65%; 
            padding: 50px; 
            display: flex; 
            flex-direction: column; 
            justify-content: center;
        }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px; }
        .info-item label { color: #8E9196; font-size: 0.85rem; display: block; margin-bottom: 5px; }
        .info-item span { font-weight: 600; color: #1A1C1E; font-size: 1.1rem; }

        .btn-edit-profile { 
            background: #1A1C1E; 
            color: #fff; 
            border: none; 
            padding: 12px 30px; 
            border-radius: 12px; 
            font-weight: 600; 
            transition: 0.3s; 
            margin-top: 40px;
            align-self: flex-start;
        }
        .btn-edit-profile:hover { background: #B4F481; color: #1A1C1E; transform: translateX(5px); }
        
        .modal-content { border-radius: 20px; border: none; }
        .form-control { border-radius: 10px; padding: 12px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="fw-bold mb-5 px-3 text-white">UniReserve</h3>
    <nav class="nav flex-column">
        <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
        <a class="nav-link" href="katalog.php"><i class="bi bi-search me-2"></i> Katalog</a>
        <a class="nav-link" href="form_pinjam.php"><i class="bi bi-plus-square me-2"></i> Form Pinjam</a>
        <a class="nav-link" href="riwayat.php"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
        <a class="nav-link active" href="profil.php"><i class="bi bi-person me-2"></i> Profil</a>
    </nav>
     <a href="flow/logout.php" id="logout" class="nav-link text-danger mt-auto">
        <i class="bi bi-box-arrow-left me-2"></i> Keluar
    </a>
</div>

<div class="main-content">
    <div class="container">
        <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 15px;">
                <i class="bi bi-check-circle-fill me-2"></i> Profil Anda berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <header class="mb-5 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold">Informasi Akun</h2>
                <p class="text-muted">Detail identitas Anda yang terdaftar di UniReserve.</p>
            </div>
            <div class="profile-avatar-sm d-flex align-items-center bg-white p-2 pe-3 rounded-pill shadow-sm">
                <div style="width: 35px; height: 35px; background: #B4F481; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #1A1C1E; margin-right: 10px;">
                    <?= strtoupper(substr($nama_user, 0, 1)) ?>
                </div>
                <span class="small fw-bold text-dark"><?= htmlspecialchars($nama_user) ?></span>
            </div>
        </header>

        <div class="profile-card-horizontal">
            <div class="profile-left">
                <div class="profile-avatar-large">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h4 class="fw-bold mb-1 text-center"><?= htmlspecialchars($nama_user) ?></h4>
                <span class="badge rounded-pill px-3 py-2 mt-2" style="background: rgba(180, 244, 129, 0.2); color: #B4F481;">
                    <i class="bi bi-mortarboard-fill me-1"></i> Mahasiswa Aktif
                </span>
            </div>

            <div class="profile-right">
                <h5 class="fw-bold mb-4" style="color: #1A1C1E;">Detail Personal</h5>
                
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nama Lengkap</label>
                        <span><?= htmlspecialchars($nama_user) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Nomor Induk Mahasiswa (NIM)</label>
                        <span><?= htmlspecialchars($nim_user) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Status Verifikasi</label>
                        <span class="text-success"><i class="bi bi-patch-check-fill me-1"></i> Terverifikasi Sistem</span>
                    </div>
                    <div class="info-item">
                        <label>Instansi</label>
                        <span>Universitas Terkait</span>
                    </div>
                </div>

                <button class="btn-edit-profile" data-bs-toggle="modal" data-bs-target="#modalEdit">
                    <i class="bi bi-pencil-square me-2"></i> Edit Informasi Profil
                </button>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold">Edit Informasi Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="flow/update_profile.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">NAMA LENGKAP</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama_user) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">NIM (TIDAK DAPAT DIUBAH)</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($nim_user) ?>" readonly>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted fw-bold">PASSWORD BARU</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4" style="background: #1A1C1E;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>