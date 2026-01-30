<?php
// 1. Logic PHP (Tetap di bagian paling atas)
require_once 'koneksi/connection.php';

// Cek apakah ada request POST (baik via Fetch JSON atau Form biasa)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $in = json_decode(file_get_contents("php://input"), true) ?? $_POST;

    $u = trim($in['nim'] ?? '');
    $p = (string)($in['password'] ?? '');

    if ($u === '' || $p === '') {
        http_response_code(400);
        echo json_encode(["success"=>false,"message"=>"NIM & password wajib"]);
        exit;
    }

    try {
        $stmt = $database_connection->prepare(
            "SELECT id, nim, nama, password FROM data_user WHERE nim=? LIMIT 1"
        );
        $stmt->execute([$u]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($p, $user['password'])) {
            http_response_code(401);
            echo json_encode(["success"=>false,"message"=>"NIM atau Password salah"]);
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $upd = $database_connection->prepare("UPDATE data_user SET token=? WHERE id=?");
        $upd->execute([$tokenHash, (int)$user['id']]);

        setcookie("user_auth_token", $token, [
            'expires' => time() + (60 * 60 * 24 * 14),
            'path' => '/',
            'httponly' => true,
            'secure' => false, 
            'samesite' => 'Lax'
        ]);

        echo json_encode([
            "success"=>true,
            "message"=>"Login Berhasil! Selamat datang " . $user['nama'],
            "redirect"=>"dashboard.php"
        ]);
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success"=>false,"message"=>"Server error","error"=>$e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Kampus</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="side-panel">
            <div class="side-content">
                <h3 class="fw-bold">UniReserve</h3>
                <p class="small">Campus Facility Booking System.</p>
            </div>
            <div class="side-content">
                <p class="small m-0 fw-bold">Portal Mahasiswa</p>
                <p class="extra-small opacity-75">Gunakan NIM untuk akses layanan.</p>
            </div>
        </div>

        <div class="form-panel">
            <div class="form-box">
                <h2>Masuk Akun</h2>
                <p class="text-muted mb-4">Selamat datang kembali di UniReserve.</p>

                <form id="loginForm" action="" method="POST">
                    <div class="mb-4">
                        <label class="form-label">NOMOR INDUK MAHASISWA (NIM)</label>
                        <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM Anda" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">PASSWORD</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary-res" style="background-color: #9de463 !important; color: #000 !important;">MASUK SEKARANG</button>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted">Belum punya akun? 
                        <a href="regist.php" style="color: #1A1C1E; font-weight: 700; text-decoration: none;">Daftar Disini</a></p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="landing_page.php" style="color: #999; font-size: 0.8rem; text-decoration: none;">← Kembali ke Beranda</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('login.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                window.location.href = result.redirect; // Pindah ke dashboard
            } else {
                alert("Gagal: " + result.message);
            }
        } catch (error) {
            alert("Terjadi kesalahan sistem.");
        }
    });
    </script>

</body>
</html>