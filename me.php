<?php
require_once 'koneksi/connection.php'; // Sesuaikan path

$token = $_COOKIE['user_auth_token'] ?? $_COOKIE['admin_auth_token'] ?? '';

if ($token === '') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No auth cookie"]);
    exit;
}

try {
    $tokenHash = hash('sha256', $token);

    $stmt = $database_connection->prepare(
        "SELECT id, nim, nama, role FROM data_user WHERE token=? LIMIT 1"
    );
    $stmt->execute([$tokenHash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid token"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "data" => [
            "id" => (int)$user['id'],
            "nim" => $user['nim'] ?? 'ADMIN',
            "nama" => $user['nama'],
            "role" => $user['role']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error", "error" => $e->getMessage()]);
}
?>