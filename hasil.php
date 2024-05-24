<?php
session_start();

// Fungsi untuk menghapus riwayat (modul 4 function)
function hapusRiwayat(): void {
    if (isset($_SESSION['riwayat'])) {
        unset($_SESSION['riwayat']);
    }
    $S_SESSION['pesan'] = "Riwayat pemesanan telah dihapus.";
}

// Periksa jika ada request untuk menghapus riwayat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_riwayat'])) {
    hapusRiwayat();
    header("Location: hasil.php");
    exit();
}

// periksa pemesanan sebelumnya
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
} else {
    $pesan = "Tidak ada data pemesanan";
}
// Jika form untuk menghapus riwayat dikirimkan
$riwayat = isset($_SESSION['riwayat']) ? $_SESSION['riwayat'] : array();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemesanan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<body>
    <div class="container">
        <h1>Hasil Pemesanan</h1>
        <p><?php echo $pesan; ?></p>
        <h2>Riwayat Pemesanan</h2>
        <table>
            <tr>
                <th>Nama Pengguna</th>
                <th>Nomor Kamar</th>
                <th>Tanggal Booking</th>
                <th>Harga</th>
            </tr>
            <?php 
            // Mengambil pesanan terbaru terlebih dahulu
            $riwayat = array_reverse($riwayat);
            foreach ($riwayat as $pesanan) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($pesanan['nama_pengguna']); ?></td>
                    <td><?php echo htmlspecialchars($pesanan['nomor_kamar']); ?></td>
                    <td><?php echo htmlspecialchars($pesanan['tanggal_booking']); ?></td>
                    <td><?php echo "Rp " . number_format($pesanan['harga'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <button type="submit" name="hapus_riwayat">Hapus Riwayat</button>
        </form>
        </form>
        <a href="index.php" class="exit">Keluar</a>
    </div>
</body>
</html>
