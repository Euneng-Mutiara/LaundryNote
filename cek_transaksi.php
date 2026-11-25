
<?php

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/models/Transaksi.php";

$transaksi = new Transaksi($pdo);

$data = $transaksi->getAll();

echo "<h2>Data Transaksi LaundryNote</h2>";

if (empty($data)) {
    echo "Belum ada data transaksi.";
} else {
    foreach ($data as $row) {
        echo $row['nama_pelanggan'] . " - ";
        echo $row['nama_layanan'] . " - ";
        echo "Rp " . $row['total_harga'] . "<br>";
    }
}

?>
