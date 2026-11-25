
<?php

class Pelanggan {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Mengambil seluruh data pelanggan beserta jumlah transaksinya
    public function getAll() {

        $query = "SELECT
                    pelanggan.*,
                    COUNT(transaksi.id_transaksi) AS total_transaksi
                  FROM pelanggan
                  LEFT JOIN transaksi
                    ON pelanggan.id_pelanggan = transaksi.id_pelanggan
                  GROUP BY pelanggan.id_pelanggan
                  ORDER BY pelanggan.id_pelanggan ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menambahkan pelanggan
    public function create($nama, $no_hp, $alamat) {

        $query = "INSERT INTO pelanggan
                  (nama_pelanggan, no_hp, alamat)
                  VALUES (:nama, :no_hp, :alamat)";

        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([
            ':nama' => $nama,
            ':no_hp' => $no_hp,
            ':alamat' => $alamat
        ]);
    }

}

?>
