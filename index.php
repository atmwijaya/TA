<?php
session_start();

// data ketersediaan kamar (OOP)
class KamarKosan {
    public $nomor;
    public $tersedia;
    public $harga;

    public function __construct($nomor, $tersedia, $harga) {
        $this->nomor = $nomor;
        $this->tersedia = $tersedia;
        $this->harga = $harga;
    }

    public function cekKetersediaan() {
        return $this->tersedia;
    }
}

// list array
$kamar_kosan = array(
    new KamarKosan("001", true, 200000),
    new KamarKosan("002", true, 200000),
    new KamarKosan("003", true, 250000),
    new KamarKosan("004", false, 250000),
    new KamarKosan("005", true, 200000),
    new KamarKosan("006", true, 220000),
    new KamarKosan("007", false, 220000),
    new KamarKosan("008", true, 240000),
    new KamarKosan("009", true, 210000),
    new KamarKosan("010", true, 230000),
);

// riwayat pemesanan jika belum ada
if (!isset($_SESSION['riwayat'])) {
    $_SESSION['riwayat'] = array();
}

// Fungsi untuk memeriksa ketersediaan kamar
function cekKetersediaanKamar(string $nomor_kamar, array $kamar_kosan): bool {
    return isset($kamar_kosan[$nomor_kamar]) && $kamar_kosan[$nomor_kamar];
}

// periksa pemesanan sebelumnya
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']); 
}

// proses pemesanan kamar jika ada (if)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pengguna = $_POST['nama_pengguna'];
    $nomor_kamar = $_POST['nomor_kamar'];
    $tanggal_booking = $_POST['tanggal_booking'];
    
    $kamar_ditemukan = false;
    foreach ($kamar_kosan as $kamar) {
        if ($kamar->nomor == $nomor_kamar) {
            $kamar_ditemukan = true;
            if ($kamar->cekKetersediaan()) {
                $pesan = "Kamar $nomor_kamar berhasil dipesan oleh $nama_pengguna untuk tanggal $tanggal_booking.";
                $kamar->tersedia = false;

                // pemesanan ke riwayat (stack)
                array_push($_SESSION['riwayat'], array(
                    'nama_pengguna' => $nama_pengguna,
                    'nomor_kamar' => $nomor_kamar,
                    'tanggal_booking' => $tanggal_booking,
                    'harga' => $kamar->harga,
                    'pesan' => $pesan
                ));
                
                // Sinkronisasi ke hasil.php
                $_SESSION['pesan'] = $pesan; 
                header("Location: hasil.php");
                exit();
            } else {
                $pesan = "Kamar $nomor_kamar tidak tersedia untuk tanggal $tanggal_booking.";
            }
        }
    }
    if (!$kamar_ditemukan) {
        $pesan = "Kamar $nomor_kamar tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Kamar Kosan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Selamat Datang di Kosan Meteseh</h1>
        <h2>Ketersediaan Kamar</h2>
        <table>
            <tr>
                <th>Nomor Kamar</th>
                <th>Status</th>
                <th>Harga</th>
            </tr>
            <?php foreach ($kamar_kosan as $kamar) : ?>
                <tr>
                    <td><?php echo $kamar->nomor; ?></td>
                    <td><?php echo $kamar->tersedia ? "Tersedia" : "Tidak Tersedia"; ?></td>
                    <td><?php echo "Rp " . number_format($kamar->harga, 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Pesan Kamar</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <label for="nomor_kamar">Nomor Kamar:</label>
                <input type="text" id="nomor_kamar" name="nomor_kamar" required>
            </div>
            <div class="input-group">
                <label for="nama_pengguna">Nama:</label>
                <input type="text" id="nama_pengguna" name="nama_pengguna" required>
            </div>
            <div class="input-group">
                <label for="tanggal_booking">Tanggal Booking:</label>
                <input type="date" id="tanggal_booking" name="tanggal_booking" required>
            </div>
            <br>
            <button type="submit">Pesan</button>
        </form>

        <?php if (isset($pesan)) : ?>
            <p><?php echo $pesan; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
