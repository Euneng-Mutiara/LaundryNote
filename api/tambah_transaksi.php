
<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Transaksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "pesan" => "Metode tidak diizinkan"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (
    !is_array($input) ||
    empty($input['id_pelanggan']) ||
    empty($input['id_layanan']) ||
    empty($input['berat']) ||
    !is_numeric($input['berat']) ||
    $input['berat'] <= 0
) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "pesan" => "Data transaksi belum lengkap atau tidak valid"
    ]);
    exit;
}

try {

    $transaksi = new Transaksi($pdo);

    $hasil = $transaksi->create(
        (int) $input['id_pelanggan'],
        (int) $input['id_layanan'],
        1,
        date('Y-m-d'),
        (float) $input['berat']
    );

    http_response_code(201);

    echo json_encode([
        "status" => true,
        "pesan" => "Transaksi berhasil ditambahkan",
        "data" => $hasil
    ]);

} catch (Exception $e) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "pesan" => $e->getMessage()
    ]);

}

?>
