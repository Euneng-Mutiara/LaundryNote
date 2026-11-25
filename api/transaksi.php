
<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Transaksi.php";

try {

    $transaksi = new Transaksi($pdo);
    $data = $transaksi->getAll();

    echo json_encode([
        "status" => true,
        "pesan" => "Data transaksi berhasil diambil",
        "data" => $data
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "pesan" => "Gagal mengambil data transaksi"
    ]);

}

?>
