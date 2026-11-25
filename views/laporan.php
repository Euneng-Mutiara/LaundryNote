
<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

$tanggalMulai = $_GET['mulai'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['selesai'] ?? date('Y-m-d');

$query = "SELECT
            COUNT(*) AS total_transaksi,
            COALESCE(SUM(berat), 0) AS total_berat,
            COALESCE(SUM(total_harga), 0) AS total_pendapatan
          FROM transaksi
          WHERE status_cucian IN ('Selesai', 'Sudah diambil')
          AND tanggal_transaksi BETWEEN :mulai AND :selesai";

$stmt = $pdo->prepare($query);
$stmt->execute([
    ':mulai' => $tanggalMulai,
    ':selesai' => $tanggalSelesai
]);

$laporan = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan - LaundryNote</title>
    <link rel="stylesheet" href="../Assets/css/style.css">
</head>
<body>

<div class="app-container">

    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2>LaundryNote</h2>
        </div>

        <p class="nav-label">MENU UTAMA</p>

        <ul class="nav-menu">
            <li><a href="../index.php" class="nav-link">Dashboard</a></li>
            <li><a href="pelanggan.php" class="nav-link">Data Pelanggan</a></li>
            <li><a href="transaksi.php" class="nav-link">Transaksi</a></li>
            <li><a href="riwayat.php" class="nav-link">Riwayat Transaksi</a></li>
            <li><a href="laporan.php" class="nav-link active">Laporan Pendapatan</a></li>
        </ul>
    </aside>

    <main class="main-wrapper">

        <header class="topbar">
            <div class="breadcrumb">Dashboard / Laporan Pendapatan</div>
        </header>

        <section class="content">

            <div class="page-header">
                <h1>Laporan Pendapatan</h1>
                <p>Ringkasan pendapatan dari transaksi laundry yang telah selesai.</p>
            </div>

            <div class="card">
                <h3>Filter Periode</h3>

                <form method="GET">
                    <div class="grid-2-1">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="mulai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($tanggalMulai) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="selesai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($tanggalSelesai) ?>"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Tampilkan Laporan
                    </button>
                </form>
            </div>

            <div class="grid-3">
                <div class="stat-card">
                    <h3>Total Transaksi</h3>
                    <h2><?= $laporan['total_transaksi'] ?></h2>
                </div>

                <div class="stat-card">
                    <h3>Total Berat Cucian</h3>
                    <h2><?= number_format($laporan['total_berat'], 2, ',', '.') ?> kg</h2>
                </div>

                <div class="stat-card">
                    <h3>Total Pendapatan</h3>
                    <h2>Rp <?= number_format($laporan['total_pendapatan'], 0, ',', '.') ?></h2>
                </div>
            </div>

        </section>

    </main>
</div>

</body>
</html>
