
<?php

require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/config/database.php";

require_once __DIR__ . "/config/database.php";

// 1. Mengambil total transaksi
$stmt = $pdo->query("SELECT COUNT(*) FROM transaksi");
$totalTransaksi = $stmt->fetchColumn();

// 2. Mengambil jumlah cucian yang masih diproses
$stmt = $pdo->query("
    SELECT COUNT(*) FROM transaksi
    WHERE status_cucian IN (
        'Menunggu', 'Sedang dicuci', 'Dikeringkan', 'Disetrika'
    )
");
$totalDiproses = $stmt->fetchColumn();

// 3. Mengambil jumlah cucian selesai
$stmt = $pdo->query("
    SELECT COUNT(*) FROM transaksi
    WHERE status_cucian IN ('Selesai', 'Sudah diambil')
");
$totalSelesai = $stmt->fetchColumn();

// 4. Mengambil total pendapatan
$stmt = $pdo->query("
    SELECT COALESCE(SUM(total_harga), 0)
    FROM transaksi
    WHERE status_cucian IN ('Selesai', 'Sudah diambil')
");
$totalPendapatan = $stmt->fetchColumn();

// 5. Mengambil jumlah setiap status cucian
$stmt = $pdo->query("
    SELECT status_cucian, COUNT(*) AS jumlah
    FROM transaksi
    GROUP BY status_cucian
");
$dataStatus = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// 6. Mengambil pendapatan tujuh hari terakhir
$stmt = $pdo->query("
    SELECT
        tanggal_transaksi,
        SUM(total_harga) AS pendapatan
    FROM transaksi
    WHERE tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
      AND tanggal_transaksi <= CURDATE()
      AND status_cucian IN ('Selesai', 'Sudah diambil')
    GROUP BY tanggal_transaksi
");
$dataGrafik = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Membuat data grafik tujuh hari
$namaHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
$grafik = [];
$pendapatanMaksimal = 0;

for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $pendapatan = (float) ($dataGrafik[$tanggal] ?? 0);

    $grafik[] = [
        'tanggal' => $tanggal,
        'hari' => $namaHari[(int) date('w', strtotime($tanggal))],
        'pendapatan' => $pendapatan
    ];

    if ($pendapatan > $pendapatanMaksimal) {
        $pendapatanMaksimal = $pendapatan;
    }
}

// 7. Mengambil lima transaksi terbaru
$stmt = $pdo->query("
    SELECT
        transaksi.*,
        pelanggan.nama_pelanggan,
        layanan.nama_layanan
    FROM transaksi
    JOIN pelanggan
        ON transaksi.id_pelanggan = pelanggan.id_pelanggan
    JOIN layanan
        ON transaksi.id_layanan = layanan.id_layanan
    ORDER BY transaksi.id_transaksi DESC
    LIMIT 5
");
$transaksiTerbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fungsi warna status
function kelasStatus($status) {
    $kelas = [
        'Menunggu' => 'badge-menunggu',
        'Sedang dicuci' => 'badge-dicuci',
        'Dikeringkan' => 'badge-dikeringkan',
        'Disetrika' => 'badge-disetrika',
        'Selesai' => 'badge-selesai',
        'Sudah diambil' => 'badge-diambil'
    ];

    return $kelas[$status] ?? 'badge';
}

$tanggalSekarang = date('d-m-Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor - LaundryNote</title>
    <link rel="stylesheet" href="Assets/css/style.css">
</head>
<body>

<div class="app-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="logo-box">🧺</div>
                <span>LaundryNote</span>
            </div>

            <div class="nav-label">RUANG KERJA</div>

            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link active">Dasbor</a></li>
                <li><a href="views/pelanggan.php" class="nav-link">Data Pelanggan</a></li>
                <li><a href="views/transaksi.php" class="nav-link">Transaksi</a></li>
                <li><a href="views/riwayat.php" class="nav-link">Riwayat Transaksi</a></li>
                <li><a href="views/laporan.php" class="nav-link">Laporan Pendapatan</a></li>
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

    <!-- Main Content -->
    <div class="main-wrapper">

        <header class="topbar">
            <div class="breadcrumb">
                Ruang kerja / <strong>Dasbor</strong>
            </div>

            <div class="topbar-right">
                <span class="date-display">
                    <?= $tanggalSekarang ?>
                </span>

                <div class="user-profile">
                    <div class="avatar">EU</div>
                    <div class="user-info">
                        <strong>Euneng Mutiara</strong>
                        <span>Administrator</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">

            <div class="page-header">
                <div>
                    <h1>Selamat datang di LaundryNote</h1>
                    <p>Pantau cucian dan pendapatan Anda dalam satu tempat.</p>
                </div>

                <a href="views/tambah_transaksi.php" class="btn btn-primary">
                    + Transaksi baru
                </a>
            </div>

            <!-- Statistik -->
            <div class="grid-4">

                <div class="card stat-card">
                    <div class="stat-title">Total transaksi</div>
                    <div class="stat-value">
                        <?= $totalTransaksi ?>
                    </div>
                    <div class="stat-desc">Seluruh transaksi</div>
                </div>

                <div class="card stat-card">
                    <div class="stat-title">Cucian diproses</div>
                    <div class="stat-value">
                        <?= $totalDiproses ?>
                    </div>
                    <div class="stat-desc">Masih dalam pengerjaan</div>
                </div>

                <div class="card stat-card">
                    <div class="stat-title">Cucian selesai</div>
                    <div class="stat-value">
                        <?= $totalSelesai ?>
                    </div>
                    <div class="stat-desc">Selesai atau sudah diambil</div>
                </div>

                <div class="card stat-card">
                    <div class="stat-title">Total pendapatan</div>
                    <div class="stat-value">
                        Rp<?= number_format($totalPendapatan, 0, ',', '.') ?>
                    </div>
                    <div class="stat-desc text-green">Transaksi selesai</div>
                </div>

            </div>

            <!-- Grafik dan Antrean -->
            <div class="grid-2-1">

                <div class="card">
                    <strong>Pendapatan 7 Hari Terakhir</strong>

                    <div style="font-size:12px; color:var(--text-muted); margin-top:5px;">
                        Berdasarkan transaksi yang telah selesai
                    </div>

                    <div class="bar-chart">

                        <?php foreach ($grafik as $item): ?>

                            <?php
                            $tinggi = $pendapatanMaksimal > 0
                                ? ($item['pendapatan'] / $pendapatanMaksimal) * 130
                                : 0;

                            if ($item['pendapatan'] > 0 && $tinggi < 5) {
                                $tinggi = 5;
                            }
                            ?>

                            <div class="bar-col">
                                <div
                                    class="bar"
                                    style="height: <?= $tinggi ?>px;"
                                    title="Rp<?= number_format($item['pendapatan'], 0, ',', '.') ?>">
                                </div>

                                <span class="bar-label">
                                    <?= $item['hari'] ?>,
                                    <?= date('d', strtotime($item['tanggal'])) ?>
                                </span>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>

                <div class="card">
                    <strong style="display:block; margin-bottom:16px;">
                        Antrean cucian
                    </strong>

                    <?php
                    $daftarStatus = [
                        'Menunggu',
                        'Sedang dicuci',
                        'Dikeringkan',
                        'Disetrika'
                    ];

                    foreach ($daftarStatus as $status):
                    ?>

                        <div style="display:flex; justify-content:space-between; margin-bottom:14px;">
                            <span><?= htmlspecialchars($status) ?></span>
                            <strong><?= $dataStatus[$status] ?? 0 ?></strong>
                        </div>

                    <?php endforeach; ?>

                    <a href="views/transaksi.php"
                       style="color:var(--primary); font-weight:600; text-decoration:none; font-size:13px;">
                        Lihat semua transaksi →
                    </a>
                </div>

            </div>

            <!-- Transaksi Terbaru -->
            <div class="card">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <strong>Transaksi terbaru</strong>

                    <a href="views/transaksi.php"
                       style="color:var(--primary); font-size:13px; text-decoration:none;">
                        Lihat semua →
                    </a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status Cucian</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (count($transaksiTerbaru) > 0): ?>

                                <?php foreach ($transaksiTerbaru as $transaksi): ?>

                                    <tr>
                                        <td>
                                            <strong>
                                                TRX-<?= str_pad($transaksi['id_transaksi'], 5, '0', STR_PAD_LEFT) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($transaksi['nama_pelanggan']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($transaksi['nama_layanan']) ?>
                                            · <?= $transaksi['berat'] ?> kg
                                        </td>

                                        <td>
                                            Rp<?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                                        </td>

                                        <td>
                                            <span class="badge <?= kelasStatus($transaksi['status_cucian']) ?>">
                                                <?= htmlspecialchars($transaksi['status_cucian']) ?>
                                            </span>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="5" style="text-align:center;">
                                        Belum ada transaksi.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>

            </div>

        </main>
    </div>
</div>

</body>
</html>
