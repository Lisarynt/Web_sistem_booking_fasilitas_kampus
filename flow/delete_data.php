<?php
// flow/delete_data.php
require_once "../koneksi/connection.php";

// 1. Ambil Token (samakan namanya dengan insup_peminjaman: user_auth_token)
$token = $_COOKIE['user_auth_token'] ?? '';

if ($token === '') {
    header("Location: ../index.php"); // Lempar ke login jika tidak ada token
    exit;
}

// 2. Ambil ID user asli dari database berdasarkan Token (Standar Dosen)
$tokenHash = hash('sha256', $token);
$stmt_user = $database_connection->prepare("SELECT id FROM data_user WHERE token = ?");
$stmt_user->execute([$tokenHash]);
$user_db = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user_db) {
    die("Sesi tidak valid.");
}

$id_mahasiswa_asli = $user_db['id']; 
$id_peminjaman = $_POST['id_peminjaman'] ?? '';

// 3. Eksekusi Hapus jika ID peminjaman ada
if (!empty($id_peminjaman)) {
    try {
        // Hapus hanya jika id_peminjaman cocok DAN milik user yang sedang login
        $sql = "DELETE FROM `peminjaman` WHERE `id_peminjaman` = ? AND `id` = ?";
        $connect = $database_connection->prepare($sql);
        $connect->execute([$id_peminjaman, $id_mahasiswa_asli]);

        // 4. Redirect kembali ke riwayat (PENTING!)
        header("Location: ../riwayat.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        die("Error deleting data: " . $e->getMessage());
    }
} else {
    header("Location: ../riwayat.php");
    exit;
}
?>