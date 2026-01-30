<?php
require_once '../koneksi/connection.php';

// Proteksi Admin
$token = $_COOKIE['admin_auth_token'] ?? ''; 
if (!$token) { 
    header("Location: ../login_admin.php");
    exit(); 
}

// 2. VALIDASI TOKEN KE DB (Wajib untuk UAS)
$tokenHash = hash('sha256', $token);
$stmt_admin = $database_connection->prepare("SELECT role FROM data_user WHERE token = ? AND role = 'admin'");
$stmt_admin->execute([$tokenHash]);

if (!$stmt_admin->fetch()) {
    die("Akses Ditolak: Anda bukan Admin!");
}

// Logika Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    try {
        $sql = "DELETE FROM kategori_fasilitas WHERE id_kategori = ?";
        $database_connection->prepare($sql)->execute([$id]);
        header("Location: ../kelola_fasilitas.php?status=deleted");
        exit();
    } catch (PDOException $e) {
        die("Gagal hapus: Data ini mungkin sedang digunakan di tabel peminjaman.");
    }
}

// Logika Tambah & Edit
if (isset($_POST['simpan'])) {
    $id = $_POST['id_kategori'];
    $nama = $_POST['nama_kategori'];

    if (empty($id)) {
        // Mode Tambah
        $sql = "INSERT INTO kategori_fasilitas (nama_kategori) VALUES (?)";
        $database_connection->prepare($sql)->execute([$nama]);
    } else {
        // Mode Edit
        $sql = "UPDATE kategori_fasilitas SET nama_kategori = ? WHERE id_kategori = ?";
        $database_connection->prepare($sql)->execute([$nama, $id]);
    }
   header("Location: ../kelola_fasilitas.php?status=success");
    exit();
}
?>