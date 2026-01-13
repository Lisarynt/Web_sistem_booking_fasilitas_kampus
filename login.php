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

        setcookie("auth_token", $token, [
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
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="side-panel">
                <div>
                    <h3 class="fw-bold">UniReserve</h3>
                    <p class="extra-small">Campus Facility Booking System.</p>
                </div>
                <div>
                    <p class="small m-0">Portal Mahasiswa</p>
                    <p class="extra-small opacity-75">Gunakan NIM untuk mengakses layanan.</p>
                </div>
            </div>

            <div class="form-panel">
                <h5 class="fw-bold mb-1">Masuk Akun</h5>
                <p class="text-muted mb-4 extra-small">Silakan masukkan identitas kampus Anda.</p>

                <form id="loginForm">
                    <div class="mb-3 d-flex flex-column">
                        <label class="form-label fw-bold text-secondary">NOMOR INDUK MAHASISWA (NIM)</label>
                        <input type="text" name="nim" class="form-control" placeholder="Contoh: 21040101" required>
                    </div>
                    
                    <div class="mb-4 d-flex flex-column">
                        <label class="form-label fw-bold text-secondary">PASSWORD</label>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mb-3">MASUK SEKARANG</button>
                    
                    <div class="text-center">
                        <p class="extra-small text-muted">Belum punya akun? <br> 
                        <a href="regist.php" class="text-primary fw-bold text-decoration-none">Daftar Akun Baru</a></p>
                    </div>
                </form>
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