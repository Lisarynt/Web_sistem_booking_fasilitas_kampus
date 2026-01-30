<?php
require_once '../koneksi/connection.php';

$token = $_COOKIE['user_auth_token'] ?? $_COOKIE['admin_auth_token'] ?? '';

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
    $cookie_options = [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ];
        setcookie("user_auth_token", "", $cookie_options);
        setcookie("admin_auth_token", "", $cookie_options);

        echo json_encode(["success" => true]);
    } catch (Throwable $e) {
        echo json_encode(["success" => false]);
    }
?>