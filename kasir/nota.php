<?php
$koneksi = new mysqli("localhost", "root", "", "toko");

if (!isset($_GET['id'])) {
    echo "ID pesanan tidak ditemukan.";
    exit;
}

$id_pesanan = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil data pesanan
$data_pesanan = mysqli_fetch_assoc($koneksi->query("SELECT * FROM tb_pesanan WHERE id_pesanan='$id_pesanan'"));

// Ambil data pembayaran
$data_bayar = mysqli_fetch_assoc($koneksi->query("SELECT * FROM tb_pembayaran WHERE id_pesanan='$id_pesanan'"));

// Ambil detail item pesanan
$items = $koneksi->query("SELECT * FROM tb_detailpes WHERE id_pesanan='$id_pesanan'");

// Update status pesanan menjadi 'Lunas' (agar hilang dari index.php)
$koneksi->query("UPDATE tb_pesanan SET status_pesanan='Lunas' WHERE id_pesanan='$id_pesanan'");

// Lokasi file QR Code
$qr_path = '../qr/qrcode_' . $id_pesanan . '.png';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Kasir</title>
    <style>
        body { font-family: 'Courier New', monospace; background: #fff; padding: 20px; color: #000; }
        .nota-container { width: 300px; margin: auto; background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .nota-header { text-align: center; margin-bottom: 10px; }
        .nota-header h3 { margin: 0; font-size: 18px; font-weight: bold; }
        .nota-header small { font-size: 12px; color: #777; }
        .nota-line { border-top: 1px dashed #000; margin: 5px 0; }
        .nota-table td { vertical-align: top; padding: 5px 0; font-size: 14px; }
        .nota-table td:first-child { font-weight: bold; }
        .nota-footer { margin-top: 10px; font-size: 14px; }
        .nota-footer p { margin: 5px 0; }
        .center { text-align: center; margin-top: 20px; }
        .center img { width: 120px; display: block; margin: 10px auto; }
        .btn-print { display: block; width: 100%; padding: 10px; margin-top: 15px; background: #000; color: #fff; border: none; cursor: pointer; border-radius: 5px; }
        .btn-print:hover { background: #333; }
        @media print { .btn-print { display: none; } body { padding: 0; } .nota-container { width: 100%; padding: 10px; box-shadow: none; } }
    </style>
</head>
<body>

<div class="nota-container">
    <div class="nota-header">
        <h3>TOKO THECAKERY</h3>
        <small>Jl. Anggrek No.123, Telp. 0812-3456-7890</small>
    </div>

    <div class="nota-line"></div>

    <p>ID: <?= $data_pesanan['id_pesanan'] ?><br>
       Tgl: <?= date('d-m-Y H:i', strtotime($data_bayar['tanggal_pembayaran'])) ?></p>

    <div class="nota-line"></div>

    <table class="nota-table">
        <?php while ($row = mysqli_fetch_assoc($items)): ?>
        <tr>
            <td><?= $row['nama_produk'] ?> x<?= $row['jumlah'] ?></td>
            <td style="text-align:right;">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="nota-line"></div>

    <div class="nota-footer">
        <p>Total     : Rp <?= number_format($data_pesanan['total_harga'], 0, ',', '.') ?></p>
        <p>Dibayar   : Rp <?= number_format($data_bayar['jumlah_bayar'], 0, ',', '.') ?></p>
        <p>Kembalian : Rp <?= number_format($data_bayar['kembalian'], 0, ',', '.') ?></p>
    </div>

    <div class="nota-line"></div>

    <p class="center"><strong>Terima kasih atas pembayaran Anda!</strong></p>

    <?php if (!empty($qr_path) && file_exists($qr_path)): ?>
    <div class="center">
        <p>Scan untuk beri review:</p>
        <img src="<?= $qr_path ?>" alt="QR Code Review">
    </div>
    <?php endif; ?>

    <button class="btn-print" onclick="window.print()">Cetak Nota</button>
</div>

<script>
document.querySelector('.btn-print').addEventListener('click', function() {
    setTimeout(function() {
        window.location.href = "index.php";
    }, 3000);
});
</script>

</body>
</html>
