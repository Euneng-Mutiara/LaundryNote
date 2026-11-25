
<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Pelanggan.php";

$pelanggan = new Pelanggan($pdo);

$data = $pelanggan->getAll();

echo json_encode([
    "status" => true,
    "pesan" => "Data pelanggan berhasil diambil",
    "data" => $data
]);

?>
