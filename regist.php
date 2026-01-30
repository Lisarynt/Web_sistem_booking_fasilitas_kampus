<?php
include 'koneksi/connection.php'; 

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nim = $_POST['nim']; 
    $password = $_POST['password'];
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $database_connection->prepare("SELECT nim FROM data_user WHERE nim = :nim");
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Gagal: NIM sudah terdaftar!'); window.location='regist.php';</script>";
        } else {
            $insert = $database_connection->prepare("INSERT INTO data_user (nama, nim, password) VALUES (:nama, :nim, :password)");
            $insert->bindParam(':nama', $nama);
            $insert->bindParam(':nim', $nim);
            $insert->bindParam(':password', $password_hashed);
            
            if ($insert->execute()) {
                echo "<script>alert('Registrasi Berhasil! Silakan Login menggunakan NIM Anda'); window.location='login.php';</script>";
                exit();
            }
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error Database: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - UniReserve</title>
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-card flex-row-reverse">
        <div class="side-panel">
            <div class="side-content">
                <h3 class="fw-bold">UniReserve</h3>
                <p>Campus Facility Booking System.</p>
            </div>
            <div class="side-content">
                <h5 class="fw-semibold">Bergabunglah!</h5>
                <p class="opacity-75">Satu akun untuk semua peminjaman fasilitas.</p>
            </div>
        </div>

        <div class="form-panel">
            <div class="form-box">
                <h2>Daftar Akun</h2>
                <p class="text-muted mb-4">Lengkapi data diri untuk buat akun baru.</p>

                <form action="regist.php" method="POST">
                    <div class="mb-4">
                        <label class="form-label">NAMA LENGKAP</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Sesuai KTM" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">NIM (NOMOR INDUK MAHASISWA)</label>
                        <input type="text" name="nim" class="form-control" placeholder="Contoh: 21040101" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">PASSWORD</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-primary-res" style="background-color: #9de463 !important; color: #000 !important;">DAFTAR SEKARANG</button>
                    
                    <div class="text-center mt-4">
                        <p class="small text-muted">Sudah punya akun? 
                        <a href="login.php" style="color: #1A1C1E; font-weight: 700; text-decoration: none;">Masuk Disini</a></p>
                    </div>
                </form>
            </div>
            
            <!-- Footer dipindah keluar dari form-box, sama seperti di login.php -->
            <footer class="auth-footer-wrapper">
                <?php include 'footer.php'; ?>
            </footer>
        </div>
    </div>
</div>

<script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>