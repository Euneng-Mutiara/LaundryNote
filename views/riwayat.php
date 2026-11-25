
<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

$query = "SELECT
            transaksi.*,
            pelanggan.nama_pelanggan,
            layanan.nama_layanan
          FROM transaksi
          JOIN pelanggan
            ON transaksi.id_pelanggan = pelanggan.id_pelanggan
          JOIN layanan
            ON transaksi.id_layanan = layanan.id_layanan
          WHERE transaksi.status_cucian IN ('Selesai', 'Sudah diambil')
          ORDER BY transaksi.tanggal_transaksi DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$dataRiwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRiwayat = count($dataRiwayat);
$totalPendapatan = array_sum(array_column($dataRiwayat, 'total_harga'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - LaundryNote</title>
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
                <li><a href="transaksi.php" class="nav-link">Transaksi</a></li>
                <li><a href="riwayat.php" class="nav-link active">Riwayat Transaksi</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="branch-card">
                <strong>Laundry Bersih</strong>
                <span>Cabang LaundryNote</span>
            </div>
             <a href="logout.php" class="nav-link">Keluar</a>
        </div>
    </aside>

    <div class="main-wrapper">

        <header class="topbar">
            <div class="breadcrumb">
                Ruang kerja / <strong>Riwayat Transaksi</strong>
            </div>

            <div class="topbar-right">
                <span class="date-display"><?= date('d F Y') ?></span>

                <div class="user-profile">
                    <div class="avatar">AD</div>
                    <div class="user-info">
                        <strong>Administrator</strong>
                        <span>Admin</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">

            <div class="page-header">
                <div>
                    <h1>Riwayat Transaksi</h1>
                    <p>Daftar transaksi laundry yang telah selesai diproses.</p>
                </div>
            </div>

            <div class="grid-2-1">

                <div class="card stat-card">
                    <div class="stat-title">Total Transaksi Selesai</div>
                    <div class="stat-value"><?= $totalRiwayat ?></div>
                    <div class="stat-desc">Transaksi berstatus selesai atau sudah diambil</div>
                </div>

                <div class="card stat-card">
                    <div class="stat-title">Total Pendapatan</div>
                    <div class="stat-value">
                        Rp<?= number_format($totalPendapatan, 0, ',', '.') ?>
                    </div>
                    <div class="stat-desc">Nilai seluruh transaksi dalam riwayat</div>
                </div>

            </div>

            <div class="card">

                <div style="margin-bottom:20px;">
                    <strong>Daftar Riwayat</strong>
                    <p style="color:var(--text-muted); font-size:12px; margin-top:4px;">
                        Data diambil langsung dari database LaundryNote.
                    </p>
                </div>

                <div class="table-responsive">

                    <table>
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal</th>
                                <th>Berat</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($dataRiwayat)): ?>

                            <?php foreach ($dataRiwayat as $item): ?>

                                <tr>
                                    <td>
                                        <strong>#<?= htmlspecialchars($item['id_transaksi']) ?></strong>
                                    </td>

                                    <td><?= htmlspecialchars($item['nama_pelanggan']) ?></td>

                                    <td><?= htmlspecialchars($item['nama_layanan']) ?></td>

                                    <td>
                                        <?= date('d-m-Y', strtotime($item['tanggal_transaksi'])) ?>
                                    </td>

                                    <td><?= htmlspecialchars($item['berat']) ?> Kg</td>

                                    <td>
                                        <strong>
                                            Rp<?= number_format($item['total_harga'], 0, ',', '.') ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <span style="color:#15803D; font-weight:600;">
                                            <?= htmlspecialchars($item['status_cucian']) ?>
                                        </span>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="7" style="text-align:center; padding:30px;">
                                    Belum ada transaksi yang selesai.
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
