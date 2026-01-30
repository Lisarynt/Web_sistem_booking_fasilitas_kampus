<?php
require_once '../koneksi/connection.php';

$token = $_COOKIE['user_auth_token'] ?? '';

try {
    if ($token !== '') {
        $tokenHash = hash('sha256', $token);
        // Sesuaikan nama tabel ke 'data_user'
        $stmt = $database_connection->prepare(
            "UPDATE data_user SET token=NULL WHERE token=?"
        );
        $stmt->execute([$tokenHash]);
    }

    // Menghapus cookie di browser
    setcookie("user_auth_token", "", [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

    echo json_encode(["success" => true, "message" => "Logout success"]);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Server error"]);
}
?>