
<?php
require_once __DIR__ . "/../config/auth.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - LaundryNote</title>
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
                <li><a href="pelanggan.php" class="nav-link active">Data Pelanggan</a></li>
                <li><a href="transaksi.php" class="nav-link">Transaksi</a></li>
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
                Ruang kerja / <strong>Data Pelanggan</strong>
            </div>

            <div class="topbar-right">
                <span class="date-display">LaundryNote</span>
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
                    <h1>Data pelanggan</h1>
                    <p>Simpan dan kelola informasi pelanggan laundry Anda.</p>
                </div>

                <button class="btn btn-primary"
                    onclick="bukaModalTambah()">
                    + Tambah pelanggan
                </button>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Pelanggan</th>
                                <th>Nomor Telepon</th>
                                <th>Alamat</th>
                                <th>Total Transaksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="daftarPelanggan">
                            <tr>
                                <td colspan="5">Memuat data pelanggan...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Tambah pelanggan</h3>
            <button class="modal-close" onclick="tutupModal('modalTambah')">&times;</button>
        </div>

        <form id="formPelanggan">

            <div class="form-group">
                <label>Nama pelanggan *</label>
                <input type="text" id="nama_pelanggan" class="form-control"
                    placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="form-group">
                <label>Nomor telepon *</label>
                <input type="tel" id="no_hp" class="form-control"
                    placeholder="Contoh: 081234567890" required>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <input type="text" id="alamat" class="form-control"
                    placeholder="Masukkan alamat pelanggan">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary"
                    onclick="tutupModal('modalTambah')">Batal</button>

                <button type="submit" class="btn btn-primary">
                    Simpan pelanggan
                </button>
            </div>

        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Edit pelanggan</h3>
            <button class="modal-close" onclick="tutupModal('modalEdit')">&times;</button>
        </div>

        <form id="formEdit">

            <input type="hidden" id="edit_id">

            <div class="form-group">
                <label>Nama pelanggan *</label>
                <input type="text" id="edit_nama" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Nomor telepon *</label>
                <input type="tel" id="edit_no_hp" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <input type="text" id="edit_alamat" class="form-control">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary"
                    onclick="tutupModal('modalEdit')">Batal</button>

                <button type="submit" class="btn btn-primary">
                    Simpan perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<script>

// Membuka dan menutup modal
function bukaModalTambah() {
    document.getElementById("modalTambah").classList.add("show");
}

function tutupModal(id) {
    document.getElementById(id).classList.remove("show");
}

// Mengambil data pelanggan
async function tampilkanPelanggan() {

    const tabel = document.getElementById("daftarPelanggan");

    try {

        const response = await fetch("../api/pelanggan.php");
        const hasil = await response.json();

        tabel.innerHTML = "";

        if (!hasil.status || hasil.data.length === 0) {
            tabel.innerHTML = `
                <tr>
                    <td colspan="5">Belum ada data pelanggan.</td>
                </tr>
            `;
            return;
        }

        hasil.data.forEach(function(pelanggan) {

            const baris = document.createElement("tr");

            const nama = document.createElement("td");
            const noHp = document.createElement("td");
            const alamat = document.createElement("td");
            const total = document.createElement("td");
            const aksi = document.createElement("td");

            nama.textContent = pelanggan.nama_pelanggan;
            noHp.textContent = pelanggan.no_hp;
            alamat.textContent = pelanggan.alamat || "-";
            total.textContent = pelanggan.total_transaksi + " transaksi";

            const tombolEdit = document.createElement("button");
            tombolEdit.className = "btn btn-primary";
            tombolEdit.textContent = "Edit";
            tombolEdit.type = "button";

            tombolEdit.addEventListener("click", function() {
                bukaModalEdit(pelanggan);
            });

            const tombolHapus = document.createElement("button");
            tombolHapus.className = "btn btn-secondary";
            tombolHapus.textContent = "Hapus";
            tombolHapus.type = "button";
            tombolHapus.style.marginLeft = "5px";

            tombolHapus.addEventListener("click", function() {
                hapusPelanggan(pelanggan.id_pelanggan);
            });

            aksi.append(tombolEdit, tombolHapus);
            baris.append(nama, noHp, alamat, total, aksi);
            tabel.appendChild(baris);

        });

    } catch (error) {

        tabel.innerHTML = `
            <tr>
                <td colspan="5">Gagal mengambil data pelanggan.</td>
            </tr>
        `;

        console.error(error);
    }
}

// Menambahkan pelanggan
document.getElementById("formPelanggan").addEventListener("submit", async function(event) {

    event.preventDefault();

    const dataPelanggan = {
        nama_pelanggan: document.getElementById("nama_pelanggan").value,
        no_hp: document.getElementById("no_hp").value,
        alamat: document.getElementById("alamat").value
    };

    try {

        const response = await fetch("../api/tambah_pelanggan.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(dataPelanggan)
        });

        const hasil = await response.json();

        if (hasil.status) {
            alert("Pelanggan berhasil ditambahkan!");
            document.getElementById("formPelanggan").reset();
            tutupModal("modalTambah");
            tampilkanPelanggan();
        } else {
            alert(hasil.pesan || "Gagal menambahkan pelanggan.");
        }

    } catch (error) {
        alert("Terjadi kesalahan saat menghubungi API.");
        console.error(error);
    }

});

// Membuka modal edit
function bukaModalEdit(pelanggan) {

    document.getElementById("edit_id").value = pelanggan.id_pelanggan;
    document.getElementById("edit_nama").value = pelanggan.nama_pelanggan;
    document.getElementById("edit_no_hp").value = pelanggan.no_hp;
    document.getElementById("edit_alamat").value = pelanggan.alamat || "";

    document.getElementById("modalEdit").classList.add("show");
}

// Menyimpan perubahan pelanggan
document.getElementById("formEdit").addEventListener("submit", async function(event) {

    event.preventDefault();

    const data = {
        id_pelanggan: document.getElementById("edit_id").value,
        nama_pelanggan: document.getElementById("edit_nama").value,
        no_hp: document.getElementById("edit_no_hp").value,
        alamat: document.getElementById("edit_alamat").value
    };

    try {

        const response = await fetch("../api/ubah_pelanggan.php", {
            method: "PUT",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(data)
        });

        const hasil = await response.json();

        if (hasil.status) {
            alert("Data pelanggan berhasil diperbarui!");
            tutupModal("modalEdit");
            tampilkanPelanggan();
        } else {
            alert(hasil.pesan);
        }

    } catch (error) {
        alert("Gagal menghubungi server.");
        console.error(error);
    }

});

// Menghapus pelanggan
async function hapusPelanggan(id) {

    const yakin = confirm("Apakah kamu yakin ingin menghapus pelanggan ini?");

    if (!yakin) return;

    try {

        const response = await fetch("../api/hapus_pelanggan.php", {
            method: "DELETE",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({id_pelanggan: id})
        });

        const hasil = await response.json();

        if (hasil.status) {
            alert("Pelanggan berhasil dihapus!");
            tampilkanPelanggan();
        } else {
            alert(hasil.pesan);
        }

    } catch (error) {
        alert("Gagal menghubungi server.");
        console.error(error);
    }

}

// Menampilkan data saat halaman dibuka
tampilkanPelanggan();

</script>

</body>
</html>
