<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniReserve - Campus Facility Booking</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { 
            --primary-color: #B4F481; 
            --dark-color: #1A1C1E;   
            --bg-light: #F8F9FD;
        }
        
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; background-color: var(--bg-light); }


        .navbar { 
            background: white !important; 
            padding: 15px 0; 
            position: fixed; 
            width: 100%; 
            top: 0; 
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        
        .navbar-brand { color: var(--dark-color) !important; font-weight: 800; font-size: 1.6rem; }
        .navbar-brand span { color: #82c91e; } 
        .nav-link { color: #555 !important; font-weight: 500; }
        .nav-link:hover { color: var(--dark-color) !important; }


        .btn-portal {
            background-color: var(--dark-color);
            color: var(--primary-color) !important;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-portal:hover {
            background-color: #000;
            transform: translateY(-2px);
        }

        .hero {
            height: 100vh;
            background: linear-gradient(rgba(26, 28, 30, 0.7), rgba(26, 28, 30, 0.6)), url('assets/images/lpage.jpg');
            background-size: cover; 
            background-position: center;
            display: flex; 
            align-items: center; 
            color: white; 
            text-align: center;
        }

        .btn-start {
            background-color: var(--primary-color);
            color: var(--dark-color) ;
            border: none;
            border-radius: 12px;
            transition: 0.3s;
        }
        .btn-start:hover {
            background-color: #9de463;
            transform: scale(1.05);
            color: var(--dark-color);
        }

        .step-icon {
            width: 70px; height: 70px;
            background: rgba(180, 244, 129, 0.2);
            color: var(--dark-color);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem; font-weight: 700;
            transition: 0.4s;
        }
        .step-card:hover .step-icon { 
            background: var(--primary-color); 
            transform: rotate(10deg);
        }

        .section-title { font-weight: 700; font-size: 2.3rem; margin-bottom: 40px; color: var(--dark-color); }
        .section-title span { background: var(--primary-color); padding: 0 10px; border-radius: 5px; }
        
        .card-facility { 
            border: none; 
            border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            transition: 0.3s; 
            overflow: hidden; 
            height: 100%; 
            background: white;
        }
        .card-facility:hover { transform: translateY(-10px); }
        .facility-img { height: 250px; object-fit: cover; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Uni<span>Reserve</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a href="login.php" class="nav-link me-4">Login Mahasiswa</a>
                    </li>
                    <li class="nav-item">
                        <a href="login_admin.php" class="btn btn-portal px-4 py-2">Portal Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <h1 class="display-4 fw-bold mb-3">Solusi Cerdas Peminjaman Fasilitas Kampus</h1>
                    <p class="lead mb-5 opacity-75">UniReserve hadir untuk menghilangkan birokrasi rumit. Kini, mahasiswa dapat memantau ketersediaan ruangan secara real-time hanya dalam beberapa klik.</p>
                    <a href="regist.php" class="btn btn-start py-3 px-5 fw-bold shadow">MULAI SEKARANG</a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5 bg-white border-bottom">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Alur <span>Peminjaman</span></h2>
                <p class="text-muted">Proses digital yang cepat, transparan, dan terukur.</p>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6 step-card">
                    <div class="step-icon">1</div>
                    <h6 class="fw-bold">Cek Ruangan</h6>
                    <p class="small text-muted d-none d-md-block">Lihat jadwal ketersediaan ruangan secara real-time.</p>
                </div>
                <div class="col-md-3 col-6 step-card">
                    <div class="step-icon">2</div>
                    <h6 class="fw-bold">Isi Form</h6>
                    <p class="small text-muted d-none d-md-block">Lengkapi data agenda dan durasi peminjaman.</p>
                </div>
                <div class="col-md-3 col-6 step-card">
                    <div class="step-icon">3</div>
                    <h6 class="fw-bold">Approval</h6>
                    <p class="small text-muted d-none d-md-block">Validasi instan dari Admin atau Kepala Lab.</p>
                </div>
                <div class="col-md-3 col-6 step-card">
                    <div class="step-icon">4</div>
                    <h6 class="fw-bold">Unduh PDF</h6>
                    <p class="small text-muted d-none d-md-block">Cetak bukti izin resmi untuk penggunaan fasilitas.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="section-title">Fasilitas <span>Tersedia</span></h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card card-facility">
                        <img src="assets/images/lpclass.jpg" class="facility-img" alt="Kelas">
                        <div class="card-body p-4 text-center">
                            <h4 class="fw-bold">Ruang Kelas Teori</h4>
                            <p class="text-muted small">Fasilitas AC dan proyektor untuk menunjang kegiatan seminar atau rapat organisasi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-facility">
                        <img src="assets/images/lplab.jpg" class="facility-img" alt="Lab">
                        <div class="card-body p-4 text-center">
                            <h4 class="fw-bold">Laboratorium Komputer</h4>
                            <p class="text-muted small">Fasilitas komputer high-spec untuk kegiatan praktikum atau pelatihan software.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>