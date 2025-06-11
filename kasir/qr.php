<?php
// Include library QR
include('lib/phpqrcode/qrlib.php');

// URL tujuan QR (tanpa id_pesanan)
$url_review = "http://localhost/Toko_TheCakery/review.php";

// Lokasi file untuk menyimpan QR Code
$filename = "qrcodes/review_qr.png";

// Buat QR Code dan simpan ke file
QRcode::png($url_review, $filename, 'L', 4, 2);

// Tampilkan QR Code
echo "<h3>Scan QR untuk memberi review:</h3>";
echo "<img src='$filename'>";
?>
