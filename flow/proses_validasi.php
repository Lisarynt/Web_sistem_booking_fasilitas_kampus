<?php
require_once '../koneksi/connection.php';

// 1. Ambil Cookie
$token = $_COOKIE['admin_auth_token'] ?? ''; 
if (!$token) {
    header("Location: ../login_admin.php");
    exit();
}   

$tokenHash = hash('sha256', $token);
$stmt_admin = $database_connection->prepare("SELECT id_admin FROM admins WHERE cookie_token = ?");
$stmt_admin->execute([$tokenHash]); // Pakai $stmt_admin

if (!$stmt_admin->fetch()) { 
    die("Akses Ditolak: Token tidak ditemukan di database!"); 
}

$id_peminjaman = $_REQUEST['id_peminjaman'] ?? $_GET['id'] ?? '';
$aksi = $_REQUEST['aksi'] ?? ''; 
$alasan = $_POST['alasan'] ?? null;

if (!$id_peminjaman || !$aksi) {
    die("Error: Parameter tidak lengkap.");
}

try {
    if ($aksi === 'Disetujui') {
        $sql = "UPDATE peminjaman SET status_pengajuan = 'Disetujui', alasan_penolakan = NULL WHERE id_peminjaman = ?";
        $stmt_update = $database_connection->prepare($sql);
        $stmt_update->execute([$id_peminjaman]);
    } 
    elseif ($aksi === 'Ditolak') {
        $sql = "UPDATE peminjaman SET status_pengajuan = 'Ditolak', alasan_penolakan = ? WHERE id_peminjaman = ?";
        $stmt_update = $database_connection->prepare($sql);
        $stmt_update->execute([$alasan, $id_peminjaman]);
    }

    header("Location: ../dashboard_admin.php?status=success");
    exit();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>