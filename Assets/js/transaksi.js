
document.addEventListener("DOMContentLoaded", function () {
    const tombolStatus = document.querySelectorAll(".btn-status");

    tombolStatus.forEach(function (tombol) {
        tombol.addEventListener("click", async function () {
            const id = this.dataset.id;
            const pilihan = document.querySelector(
                `#status-${id}`
            );

            const statusBaru = pilihan.value;

            if (!statusBaru) {
                alert("Pilih status terlebih dahulu!");
                return;
            }

            try {
                const response = await fetch(
                    "../api/ubah_status.php",
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            id_transaksi: id,
                            status_cucian: statusBaru
                        })
                    }
                );

                const hasil = await response.json();

                if (hasil.status) {
                    alert("Status transaksi berhasil diperbarui!");
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
