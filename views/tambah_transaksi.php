
<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Pelanggan.php";
require_once __DIR__ . "/../models/Layanan.php";
require_once __DIR__ . "/../models/Transaksi.php";
require_once __DIR__ . "/../config/auth.php";

$pelangganModel = new Pelanggan($pdo);
$layananModel = new Layanan($pdo);

$daftarPelanggan = $pelangganModel->getAll();
$daftarLayanan = $layananModel->getAll();

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $transaksiModel = new Transaksi($pdo);

        $hasil = $transaksiModel->create(
            (int) $_POST['id_pelanggan'],
            (int) $_POST['id_layanan'],
            1,
            date('Y-m-d'),
            (float) $_POST['berat']
        );

        header("Location: transaksi.php?berhasil=1");
        exit;

    } catch (Exception $e) {
        $pesan = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - LaundryNote</title>
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<div class="app-container">

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="logo-box">🧺</div>
                <span>LaundryNote</span>
            </div>

            <div class="nav-label">RUANG KERJA</div>

            <ul class="nav-menu">
                <li><a href="../index.php" class="nav-link">Dasbor</a></li>
                <li><a href="pelanggan.php" class="nav-link">Data Pelanggan</a></li>
                <li><a href="transaksi.php" class="nav-link active">Transaksi</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="branch-card">
                <strong>Laundry Bersih</strong>
                <span>Cabang Dipatiukur, Bandung</span>
            </div>
                 <a href="logout.php" class="nav-link">Keluar</a>
        </div>
    </aside>

    <div class="main-wrapper">

        <header class="topbar">
            <div class="breadcrumb">
                Ruang kerja / Transaksi / <strong>Tambah Transaksi</strong>
            </div>

            <div class="topbar-right">
                <span class="date-display"><?= date('d F Y') ?></span>
                <div class="user-profile">
                    <div class="avatar">AP</div>
                    <div class="user-info">
                        <strong>Ayu Pratama</strong>
                        <span>Administrator</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">

            <div class="page-header">
                <div>
                    <h1>Tambah Transaksi</h1>
                    <p>Masukkan informasi cucian pelanggan.</p>
                </div>

                <a href="transaksi.php" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

            <div class="card" style="max-width:700px;">

                <strong>Formulir Transaksi</strong>

                <?php if ($pesan !== ""): ?>
                    <p style="color:red; margin-top:12px;">
                        <?= htmlspecialchars($pesan) ?>
                    </p>
                <?php endif; ?>

                <form method="POST" style="margin-top:24px;">

                    <div class="form-group">
                        <label>Pilih Pelanggan</label>
                        <select name="id_pelanggan" class="form-control" required>
                            <option value="">-- Pilih Pelanggan --</option>

                            <?php foreach ($daftarPelanggan as $p): ?>
                                <option value="<?= $p['id_pelanggan'] ?>">
                                    <?= htmlspecialchars($p['nama_pelanggan']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pilih Layanan</label>
                        <select name="id_layanan" id="layanan" class="form-control" required>
                            <option value="">-- Pilih Layanan --</option>

                            <?php foreach ($daftarLayanan as $l): ?>
                                <option
                                    value="<?= $l['id_layanan'] ?>"
                                    data-harga="<?= $l['harga_per_kg'] ?>">
                                    <?= htmlspecialchars($l['nama_layanan']) ?>
                                    - Rp<?= number_format($l['harga_per_kg'], 0, ',', '.') ?>/kg
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Berat Cucian (Kg)</label>
                        <input
                            type="number"
                            name="berat"
                            id="berat"
                            class="form-control"
                            min="0.1"
                            step="0.1"
                            placeholder="Masukkan berat cucian"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Estimasi Total Harga</label>
                        <input
                            type="text"
                            id="total"
                            class="form-control"
                            value="Rp0"
                            readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Simpan Transaksi
                    </button>

                </form>
            </div>

        </main>
    </div>
</div>

<script>
const layanan = document.getElementById('layanan');
const berat = document.getElementById('berat');
const total = document.getElementById('total');

function hitungTotal() {
    const harga = Number(
        layanan.options[layanan.selectedIndex]?.dataset.harga || 0
    );

    const jumlahBerat = Number(berat.value || 0);
    const hasil = harga * jumlahBerat;

    total.value = 'Rp' + hasil.toLocaleString('id-ID');
}

layanan.addEventListener('change', hitungTotal);
berat.addEventListener('input', hitungTotal);
</script>

</body>
</html>
