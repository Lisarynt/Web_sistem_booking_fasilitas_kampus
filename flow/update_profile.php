<?php
require_once "../koneksi/connection.php";

$token = $_COOKIE['user_auth_token'] ?? '';
if ($token === '') {
    header("Location: ../index.php");
    exit;
}

$tokenHash = hash('sha256', $token);
$stmt_user = $database_connection->prepare("SELECT id FROM data_user WHERE token = ?");
$stmt_user->execute([$tokenHash]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Sesi tidak valid.");
}

$id_user = $user['id'];
$nama_baru = $_POST['nama'];
$password_baru = $_POST['password'];

try {
    if (!empty($password_baru)) {
        $sql = "UPDATE data_user SET nama = ?, password = ? WHERE id = ?";
        $stmt = $database_connection->prepare($sql);
        $stmt->execute([$nama_baru, $password_baru, $id_user]);
    } else {
        $sql = "UPDATE data_user SET nama = ? WHERE id = ?";
        $stmt = $database_connection->prepare($sql);
        $stmt->execute([$nama_baru, $id_user]);
    }

    header("Location: ../profil.php?status=updated");
} catch (PDOException $e) {
    die("Gagal update profil: " . $e->getMessage());
}
?>