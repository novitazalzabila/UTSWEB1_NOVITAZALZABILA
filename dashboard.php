<?php
session_start();

// redirect ke login kalau belum
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// --- proses tambah item ke keranjang (session) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tambah item
    if (isset($_POST['add_item'])) {
        $kode   = trim($_POST['kode'] ?? '');
        $nama   = trim($_POST['nama'] ?? '');
        $harga  = (int) ($_POST['harga'] ?? 0);
        $jumlah = (int) ($_POST['jumlah'] ?? 0);

        if ($kode !== '' && $nama !== '' && $harga > 0 && $jumlah > 0) {
            $item = [
                'kode' => $kode,
                'nama' => $nama,
                'harga'=> $harga,
                'jumlah'=> $jumlah,
                'total'=> $harga * $jumlah
            ];

            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            $_SESSION['cart'][] = $item;
        } else {
            $msg_error = "Mohon isi semua field dengan benar (harga & jumlah > 0).";
        }
    }

    // Kosongkan keranjang
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
    }

    // Reset form (batal)
    if (isset($_POST['cancel'])) {
        // tidak perlu apa-apa, form akan kosong setelah submit
    }
}

// ambil isi keranjang
$cart = $_SESSION['cart'] ?? [];

// hitung totals
$grandtotal = 0;
foreach ($cart as $it) {
    $grandtotal += $it['total'];
}
$diskon = $grandtotal * 0.05; // 5%
$totalBayar = $grandtotal - $diskon;

// --- data statis (dashboard lama) tetap ada di bawah ---
$static_pembelian = [
    ["kode" => "BARANG01", "nama" => "Kopi Kapal Api", "harga" => 5000, "jumlah" => 3],
    ["kode" => "BARANG03", "nama" => "Indomie Goreng", "harga" => 3500, "jumlah" => 5],
    ["kode" => "BARANG05", "nama" => "Chitato", "harga" => 10000, "jumlah" => 2],
];
$static_grand = 0;
foreach ($static_pembelian as $p) $static_grand += $p['harga'] * $p['jumlah'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>POLGAN MART - Dashboard</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f3f6f9; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .topbar { background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding:18px 28px; border-bottom:1px solid #eef2f6; }
        .brand { display:flex; align-items:center; gap:12px; }
        .brand img { width:56px; height:56px; border-radius:12px; object-fit:cover; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
        .brand h4 { margin:0; font-weight:700; color:#222; }
        .brand small { color:#6b7280; display:block; margin-top:2px; }
        .userbox { text-align:right; }
        .card-form { border-radius:12px; box-shadow: 0 6px 18px rgba(14,30,37,0.06); }
        .table-custom th { background: #f8fafc; color:#333; border-top:0; }
        .summary-row { background:#ffffff; font-weight:700; }
        .summary-label { text-align:right; padding-right:20px; }
        .btn-clear { background:#fff; border:1px solid #e6e9ef; color:#333; }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar d-flex justify-content-between align-items-center">
    <div class="brand">
        <!-- gunakan gambar yang diupload (pastikan path dapat diakses oleh web server) -->
        <img src="https://c8.alamy.com/comp/2WDB7C0/pm-initial-handwriting-logo-with-circle-2WDB7C0.jpg" alt="logo">
        <div>
            <h4 class="mb-0">--POLGAN MART--</h4>
            <small>Sistem Penjualan Sederhana</small>
        </div>
    </div>

    <div class="userbox">
        <div>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
        <div><small class="text-muted">Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?></small></div>
        <div class="mt-2">
            <a href="logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
        </div>
    </div>
</div>

<!-- CONTAINER -->
<div class="container my-4">

    <!-- CARD: Form input & cart -->
    <div class="card card-form p-4 mb-4">
        <div class="row">
            <div class="col-md-7">
                <h5 class="mb-3">Tambah Barang (Manual)</h5>

                <?php if (!empty($msg_error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($msg_error) ?></div>
                <?php endif; ?>

                <form method="post" class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Kode Barang</label>
                        <input name="kode" class="form-control" placeholder="Masukkan Kode Barang" />
                    </div>

                    <div class="col-12">
                        <label class="form-label">Nama Barang</label>
                        <input name="nama" class="form-control" placeholder="Masukkan Nama Barang" />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Harga</label>
                        <input name="harga" type="number" class="form-control" placeholder="Masukkan Harga" />
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jumlah</label>
                        <input name="jumlah" type="number" class="form-control" placeholder="Masukkan Jumlah" />
                    </div>

                    <div class="col-12">
                        <button name="add_item" class="btn btn-primary">Tambahkan</button>
                        <button name="cancel" class="btn btn-light btn-outline-secondary" formnovalidate>Batal</button>
                    </div>
                </form>
            </div>

            <div class="col-md-5">
                <h5 class="mb-3 text-center">Daftar Pembelian</h5>

                <!-- CART TABLE -->
                <div class="table-responsive">
                    <table class="table table-borderless table-sm text-center">
                        <thead class="table-custom">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($cart)): ?>
                            <tr><td colspan="5" class="text-muted">Keranjang kosong</td></tr>
                        <?php else: ?>
                            <?php foreach ($cart as $it): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($it['kode']); ?></td>
                                    <td><?php echo htmlspecialchars($it['nama']); ?></td>
                                    <td>Rp <?php echo number_format($it['harga'],0,',','.'); ?></td>
                                    <td><?php echo $it['jumlah']; ?></td>
                                    <td>Rp <?php echo number_format($it['total'],0,',','.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- summary -->
                <div class="mt-3">
                    <table class="table table-borderless w-100">
                        <tr>
                            <td class="summary-label">Total Belanja</td>
                            <td class="text-end">Rp <?php echo number_format($grandtotal,0,',','.'); ?></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Diskon (5%)</td>
                            <td class="text-end">Rp <?php echo number_format($diskon,0,',','.'); ?></td>
                        </tr>
                        <tr class="summary-row">
                            <td class="summary-label">Total Bayar</td>
                            <td class="text-end">Rp <?php echo number_format($totalBayar,0,',','.'); ?></td>
                        </tr>
                    </table>

                    <form method="post" class="d-inline">
                        <button name="clear_cart" class="btn btn-outline-danger btn-sm">Kosongkan Keranjang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- --- HERE: Dashboard Lama (tetap tampil di bawah) --- -->
    <div class="card p-3">
        <h5 class="mb-3 text-center">Dashboard (Versi Sebelumnya)</h5>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Harga (Rp)</th>
                        <th>Jumlah Beli</th>
                        <th>Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $gt = 0;
                foreach ($static_pembelian as $item) {
                    $total = $item['harga'] * $item['jumlah'];
                    $gt += $total;
                    echo "<tr>";
                    echo "<td>{$item['kode']}</td>";
                    echo "<td>{$item['nama']}</td>";
                    echo "<td>" . number_format($item['harga'],0,',','.') . "</td>";
                    echo "<td>{$item['jumlah']}</td>";
                    echo "<td>" . number_format($total,0,',','.') . "</td>";
                    echo "</tr>";
                }
                ?>
                <tr class="table-info" style="font-weight:700;">
                    <td colspan="4" class="text-end">Grand Total</td>
                    <td>Rp <?php echo number_format($gt,0,',','.'); ?></td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- struk -->
        <div class="mt-4 p-3" style="border:1px dashed #dbe6f0; background:#fff;">
            <h6 style="text-align:center;">===== STRUK PEMBELIAN =====</h6>
            <p>Tanggal : <?php echo date("d-m-Y H:i:s"); ?><br>
            Kasir   : <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <hr>
            <?php foreach ($static_pembelian as $p): 
                $t = $p['harga'] * $p['jumlah'];
                echo "<div>{$p['nama']} ({$p['jumlah']} x Rp " . number_format($p['harga'],0,',','.') . ") = Rp " . number_format($t,0,',','.') . "</div>";
            endforeach; ?>
            <hr>
            <div style="text-align:right; font-weight:700;">Total Belanja : Rp <?php echo number_format($static_grand,0,',','.'); ?></div>
            <p class="mt-3" style="text-align:center;">Terima Kasih Telah Berbelanja di POLGAN MART!</p>
        </div>

    </div>

</div>

<!-- bootstrap js (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>