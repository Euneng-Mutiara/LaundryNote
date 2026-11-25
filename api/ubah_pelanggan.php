
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
    empty($input['id_pelanggan']) ||
    empty($input['nama_pelanggan']) ||
    empty($input['no_hp'])
) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "pesan" => "ID, nama, dan nomor HP wajib diisi"
    ]);
    exit;
}

try {
    $query = "UPDATE pelanggan
              SET nama_pelanggan = :nama,
                  no_hp = :no_hp,
                  alamat = :alamat
              WHERE id_pelanggan = :id";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        ':nama' => $input['nama_pelanggan'],
        ':no_hp' => $input['no_hp'],
        ':alamat' => $input['alamat'] ?? '',
        ':id' => (int) $input['id_pelanggan']
    ]);

    if ($stmt->rowCount() === 0) {
        $cek = $pdo->prepare(
            "SELECT id_pelanggan FROM pelanggan WHERE id_pelanggan = :id"
        );
        $cek->execute([':id' => (int) $input['id_pelanggan']]);

        if (!$cek->fetch()) {
            http_response_code(404);
            echo json_encode([
                "status" => false,
                "pesan" => "Pelanggan tidak ditemukan"
            ]);
            exit;
        }
    }

    echo json_encode([
        "status" => true,
        "pesan" => "Data pelanggan berhasil diperbarui"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "pesan" => "Gagal memperbarui data pelanggan"
    ]);
}
?>
