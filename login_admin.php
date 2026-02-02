<?php
require_once 'koneksi/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $in = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    $u = trim($in['username'] ?? '');
    $p = (string)($in['password'] ?? '');

    try {
        $stmt = $database_connection->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$u]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($p, $admin['password'])) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);

            $upd = $database_connection->prepare("UPDATE admins SET cookie_token = ? WHERE id_admin = ?");
            $upd->execute([$tokenHash, (int)$admin['id_admin']]);

            setcookie("admin_auth_token", $token, [
                'expires' => time() + (60 * 60 * 24),
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

        echo json_encode(["success" => true, "message" => "Login Berhasil!", "redirect" => "dashboard_admin.php"]);
            exit;
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Username atau Password salah!"]);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database Error"]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Admin - UniReserve</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> </head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="side-panel">
            <div class="side-content">
                <h3 class="fw-bold text-white">UniReserve</h3>
                <p class="small text-white opacity-75">Campus Facility Booking System.</p>
            </div>
            <div class="side-content">
                <p class="small m-0 fw-bold text-white">Portal Administrator</p>
                <p class="extra-small text-white opacity-75">Gunakan akun admin untuk akses sistem.</p>
            </div>
        </div>

        <div class="form-panel">
            <div class="form-box">
                <h2>Admin Login</h2>
                <p class="text-muted mb-4">Selamat datang di Panel Kontrol.</p>

                <form id="adminLoginForm">
                    <div class="mb-4">
                        <label class="form-label">USERNAME</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">PASSWORD</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-res" style="background-color: #9de463 !important; color: #000 !important;">MASUK SEKARANG</button>
                    
                    <div class="text-center mt-4">
                        <a href="landing_page.php" style="color: #999; font-size: 0.8rem; text-decoration: none;">← Kembali ke Beranda</a>
                    </div>
                </form>
            </div>
    <footer class="auth-footer-wrapper">
        <?php include 'footer.php'; ?>
    </footer>
        </div>
    </div>
</div>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('login_admin.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            window.location.href = result.redirect;
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