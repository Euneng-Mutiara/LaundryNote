
<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "pesan" => "Gunakan metode DELETE"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input) || empty($input['id_transaksi'])) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "pesan" => "ID transaksi wajib diisi"
    ]);
    exit;
}

try {
    $query = "DELETE FROM transaksi WHERE id_transaksi = :id";
    $stmt = $pdo->prepare($query);

    $stmt->execute([
        ':id' => (int) $input['id_transaksi']
    ]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "status" => false,
            "pesan" => "Transaksi tidak ditemukan"
        ]);
        exit;
    }

    echo json_encode([
        "status" => true,
        "pesan" => "Transaksi berhasil dihapus"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "pesan" => "Gagal menghapus transaksi"
    ]);
}
?>
