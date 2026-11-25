
<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Pelanggan.php";

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
    empty($input['nama_pelanggan']) ||
    empty($input['no_hp'])
) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "pesan" => "Nama dan nomor HP wajib diisi"
    ]);
    exit;
}

$pelanggan = new Pelanggan($pdo);

$berhasil = $pelanggan->create(
    $input['nama_pelanggan'],
    $input['no_hp'],
    $input['alamat'] ?? null
);

if ($berhasil) {
    http_response_code(201);
    echo json_encode([
        "status" => true,
        "pesan" => "Pelanggan berhasil ditambahkan"
    ]);
}
?>
