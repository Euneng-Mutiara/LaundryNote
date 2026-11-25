
<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Transaksi.php";
require_once __DIR__ . "/../config/auth.php";

$transaksiModel = new Transaksi($pdo);
$dataTransaksi = $transaksiModel->getAll();

function formatStatus($status) {
    $kelas = [
        'Menunggu' => 'badge-menunggu',
        'Sedang dicuci' => 'badge-dicuci',
        'Dikeringkan' => 'badge-dikeringkan',
        'Disetrika' => 'badge-disetrika',
        'Selesai' => 'badge-selesai',
        'Sudah diambil' => 'badge-diambil'
    ];

    return $kelas[$status] ?? 'badge-menunggu';
}

$daftarStatus = [
    'Menunggu',
    'Sedang dicuci',
    'Dikeringkan',
    'Disetrika',
    'Selesai',
    'Sudah diambil'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - LaundryNote</title>
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
                <li>
                    <a href="../index.php" class="nav-link">Dasbor</a>
                </li>
                <li>
                    <a href="pelanggan.php" class="nav-link">Data Pelanggan</a>
                </li>
                <li>
                    <a href="transaksi.php" class="nav-link active">Transaksi</a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="branch-card">
                <strong>Laundry Bersih</strong>
                <span>Cabang Gowa, Makassar</span>
            </div>
                        <a href="logout.php" class="nav-link">Keluar</a>
        </div>
    </aside>

    <div class="main-wrapper">

        <header class="topbar">
            <div class="breadcrumb">
                Ruang kerja / <strong>Transaksi</strong>
            </div>

            <div class="topbar-right">
                <span class="date-display">
                    <?= date('d F Y') ?>
                </span>

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
                    <h1>Data Transaksi</h1>
                    <p>Kelola dan pantau seluruh transaksi laundry.</p>
                </div>

                <a href="tambah_transaksi.php" class="btn btn-primary">
                    + Transaksi Baru
                </a>
            </div>

            <?php if (isset($_GET['berhasil'])): ?>
                <div class="card" style="margin-bottom:20px; color:#15803D;">
                    Transaksi berhasil ditambahkan!
                </div>
            <?php endif; ?>

            <div class="grid-3">

                <div class="card stat-card">
                    <div class="stat-title">Total Transaksi</div>
                    <div class="stat-value">
                        <?= count($dataTransaksi) ?>
                    </div>
                    <div class="stat-desc">Seluruh transaksi tercatat</div>
                </div>

                <div class="card stat-card">
                    <div class="stat-title">Total Berat Cucian</div>
                    <div class="stat-value">
                        <?= number_format(array_sum(array_column($dataTransaksi, 'berat')), 1, ',', '.') ?> Kg
                    </div>
                    <div class="stat-desc">Akumulasi berat cucian</div>
                </div>

                <div class="card stat-card">
                    <div class="stat-title">Total Pendapatan</div>
                    <div class="stat-value">
                        Rp<?= number_format(array_sum(array_column($dataTransaksi, 'total_harga')), 0, ',', '.') ?>
                    </div>
                    <div class="stat-desc">Total nilai transaksi</div>
                </div>

            </div>

            <div class="card">

                <div style="margin-bottom:20px;">
                    <strong>Daftar Transaksi</strong>
                    <p style="color:var(--text-muted); font-size:12px; margin-top:4px;">
                        Data diambil langsung dari database LaundryNote.
                    </p>
                </div>

                <div class="table-responsive">

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Tanggal</th>
                                <th>Berat</th>
                                <th>Total Harga</th>
                                <th>Status Cucian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($dataTransaksi)): ?>

                            <?php foreach ($dataTransaksi as $item): ?>

                                <tr>
                                    <td>
                                        <strong>#<?= htmlspecialchars($item['id_transaksi']) ?></strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($item['nama_pelanggan']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($item['nama_layanan']) ?>
                                    </td>

                                    <td>
                                        <?= date('d-m-Y', strtotime($item['tanggal_transaksi'])) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($item['berat']) ?> Kg
                                    </td>

                                    <td>
                                        <strong>
                                            Rp<?= number_format($item['total_harga'], 0, ',', '.') ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="badge <?= formatStatus($item['status_cucian']) ?>">
                                            <?= htmlspecialchars($item['status_cucian']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <select
                                            class="form-control"
                                            id="status-<?= (int)$item['id_transaksi'] ?>"
                                            style="min-width:150px; margin-bottom:8px;">

                                            <?php foreach ($daftarStatus as $status): ?>
                                                <option
                                                    value="<?= htmlspecialchars($status) ?>"
                                                    <?= $item['status_cucian'] === $status ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($status) ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>

                                        <button
                                            type="button"
                                            class="btn btn-primary btn-status"
                                            data-id="<?= (int)$item['id_transaksi'] ?>">
                                            Simpan
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-hapus-transaksi"
                                            data-id="<?= (int)$item['id_transaksi'] ?>"
                                            style="margin-top:5px;">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="8" style="text-align:center; padding:30px;">
                                    Belum ada transaksi yang tercatat.
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

<script src="../Assets/js/transaksi.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const tombolHapus = document.querySelectorAll(".btn-hapus-transaksi");

    tombolHapus.forEach(function (tombol) {
        tombol.addEventListener("click", async function () {

            const id = this.dataset.id;

            const yakin = confirm(
                "Apakah kamu yakin ingin menghapus transaksi #" + id + "?"
            );

            if (!yakin) {
                return;
            }

            try {
                const response = await fetch("../api/hapus_transaksi.php", {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id_transaksi: id
                    })
                });

                const hasil = await response.json();

                if (hasil.status) {
                    alert("Transaksi berhasil dihapus!");
                    location.reload();
                } else {
                    alert(hasil.pesan);
                }

            } catch (error) {
                alert("Gagal menghubungi server.");
                console.error(error);
            }

        });
    });
});
</script>

</body>
</html>
