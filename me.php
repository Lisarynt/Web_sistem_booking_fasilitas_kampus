<?php
require_once 'koneksi/connection.php'; // Sesuaikan path

$token = $_COOKIE['auth_token'] ?? '';

if ($token === '') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No auth cookie"]);
    exit;
}

try {
    $tokenHash = hash('sha256', $token);

    // SESUAIKAN: Ambil kolom id, nim, nama dari tabel data_user
    $stmt = $database_connection->prepare(
        "SELECT id, nim, nama FROM data_user WHERE token=? LIMIT 1"
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
            "nim" => $user['nim'],
            "nama" => $user['nama']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error", "error" => $e->getMessage()]);
}
?>