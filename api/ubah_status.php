
<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "pesan" => "Gunakan metode PUT"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (
    !is_array($input) ||
    empty($input['id_transaksi']) ||
    empty($input['status_cucian'])
) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "pesan" => "ID transaksi dan status wajib diisi"
    ]);
    exit;
}

$statusDiizinkan = [
    "Menunggu",
    "Sedang dicuci",
    "Dikeringkan",
    "Disetrika",
    "Selesai",
    "Sudah diambil"
];

if (!in_array($input['status_cucian'], $statusDiizinkan, true)) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "pesan" => "Status cucian tidak valid"
    ]);
    exit;
}

try {
    $query = "UPDATE transaksi
              SET status_cucian = :status
              WHERE id_transaksi = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':status' => $input['status_cucian'],
        ':id' => (int) $input['id_transaksi']
    ]);

    if ($stmt->rowCount() === 0) {
        $cek = $pdo->prepare(
            "SELECT id_transaksi FROM transaksi WHERE id_transaksi = :id"
        );
        $cek->execute([':id' => (int) $input['id_transaksi']]);

        if (!$cek->fetch()) {
            http_response_code(404);
            echo json_encode([
                "status" => false,
                "pesan" => "Transaksi tidak ditemukan"
            ]);
            exit;
        }
    }

    echo json_encode([
        "status" => true,
        "pesan" => "Status transaksi berhasil diperbarui"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "pesan" => "Gagal memperbarui status transaksi"
    ]);
}
?>
