<?php
include 'koneksi/connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nim = $_POST['nim']; // Ganti email jadi nim
    $password = $_POST['password'];
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    try {
        // PERBAIKAN: Nama tabel 'data_user' dan kolom 'nim' sesuai database terbaru
        $stmt = $database_connection->prepare("SELECT nim FROM data_user WHERE nim = :nim");
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // NOTIFIKASI JIKA NIM SUDAH ADA
            echo "<script>alert('Gagal: NIM sudah terdaftar!'); window.location='regist.php';</script>";
        } else {
            // PERBAIKAN: Insert ke tabel 'data_user'
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
                    <h5 class="fw-semibold">Bergabunglah!</h5>
                    <p class="extra-small opacity-75">Satu akun untuk semua peminjaman fasilitas.</p>
                </div>
            </div>

            <div class="form-panel">
                <h5 class="fw-bold mb-1">Daftar Akun Mahasiswa</h5>
                <p class="text-muted mb-4 extra-small">Lengkapi data diri untuk buat akun baru.</p>

                <form action="" method="POST">
                    <div class="mb-3 d-flex flex-column">
                        <label class="form-label fw-bold text-secondary">NAMA LENGKAP</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Sesuai KTM" required>
                    </div>

                    <div class="mb-3 d-flex flex-column">
                        <label class="form-label fw-bold text-secondary">NIM (NOMOR INDUK MAHASISWA)</label>
                        <input type="text" name="nim" class="form-control" placeholder="Contoh: 21040101" required>
                    </div>
                    
                    <div class="mb-4 d-flex flex-column">
                        <label class="form-label fw-bold text-secondary">PASSWORD</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-primary w-100 fw-bold py-2 mb-3">DAFTAR SEKARANG</button>
                    
                    <div class="text-center">
                        <p class="extra-small text-muted">Sudah punya akun? <br> 
                        <a href="login.php" class="text-primary fw-bold text-decoration-none">Masuk Disini</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>