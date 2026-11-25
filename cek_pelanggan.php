
<?php

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/Pelanggan.php";

$pelanggan = new Pelanggan($pdo);

$data = $pelanggan->getAll();

echo "<h2>Data Pelanggannya LaundryNote</h2>";

foreach ($data as $row) {
    echo $row['nama_pelanggan'] . " - ";
    echo $row['no_hp'] . "<br>";
}

?>
