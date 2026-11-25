
<?php

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/Layanan.php";

$layanan = new Layanan($pdo);

$data = $layanan->getAll();

echo "<h2>Data Layanan LaundryNote</h2>";

foreach ($data as $row) {
    echo $row['nama_layanan'] . " - ";
    echo "Rp " . $row['harga_per_kg'] . " per kg - ";
    echo $row['estimasi_hari'] . " hari<br>";
}

?>
