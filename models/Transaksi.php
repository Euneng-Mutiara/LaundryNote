
<?php

class Transaksi {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Mengambil semua data transaksi
    public function getAll() {

        $query = "SELECT
                    transaksi.*,
                    pelanggan.nama_pelanggan,
                    layanan.nama_layanan
                  FROM transaksi
                  JOIN pelanggan
                    ON transaksi.id_pelanggan = pelanggan.id_pelanggan
                  JOIN layanan
                    ON transaksi.id_layanan = layanan.id_layanan";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menambahkan transaksi baru
    public function create(
        $id_pelanggan,
        $id_layanan,
        $id_user,
        $tanggal,
        $berat
    ) {

        // Mengambil harga layanan
        $queryHarga = "SELECT harga_per_kg
                       FROM layanan
                       WHERE id_layanan = :id_layanan";

        $stmtHarga = $this->pdo->prepare($queryHarga);
        $stmtHarga->execute([
            ':id_layanan' => $id_layanan
        ]);

        $layanan = $stmtHarga->fetch(PDO::FETCH_ASSOC);

        if (!$layanan) {
            throw new Exception("Layanan tidak ditemukan");
        }

        // Menghitung total harga
        $total = $layanan['harga_per_kg'] * $berat;

        // Menyimpan transaksi
        $query = "INSERT INTO transaksi
                  (id_pelanggan, id_layanan, id_user,
                   tanggal_transaksi, berat, total_harga)
                  VALUES
                  (:pelanggan, :layanan, :user,
                   :tanggal, :berat, :total)";

        $stmt = $this->pdo->prepare($query);

        $stmt->execute([
            ':pelanggan' => $id_pelanggan,
            ':layanan' => $id_layanan,
            ':user' => $id_user,
            ':tanggal' => $tanggal,
            ':berat' => $berat,
            ':total' => $total
        ]);

        return [
            "id_transaksi" => $this->pdo->lastInsertId(),
            "total_harga" => $total
        ];
    }
}

?>
