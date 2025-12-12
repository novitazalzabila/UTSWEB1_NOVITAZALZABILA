<?php
// =========================
// DATA BARANG
// =========================
$items = [
    'BRG001' => ['nama' => 'Sabun Mandi', 'harga' => 15000],
    'BRG002' => ['nama' => 'Sikat Gigi',  'harga' => 8000],
    'BRG003' => ['nama' => 'Pasta Gigi',  'harga' => 12000],
    'BRG004' => ['nama' => 'Shampoo',     'harga' => 20000],
    'BRG005' => ['nama' => 'Handuk',      'harga' => 35000],
];

$kode = $nama = '';
$harga = 0;
$jumlah = 1;
$lineTotal = $grandtotal = $diskon = $totalbayar = 0;
$d = "0%";

// =========================
// PROSES SAAT FORM SUBMIT
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode   = $_POST['kode']   ?? '';
    $jumlah = (int)($_POST['jumlah'] ?? 1);

    // jika kode ada di array, ambil nama & harga
    if ($kode !== '' && isset($items[$kode])) {
        $nama  = $items[$kode]['nama'];
        $harga = $items[$kode]['harga'];
    }

    // hitung total
    $lineTotal  = $harga * $jumlah;
    $grandtotal = $lineTotal;

    // hitung diskon
    if ($grandtotal == 0) {
        $d = "0%";
        $diskon = 0;
    } elseif ($grandtotal < 50000) {
        $d = "5%";
        $diskon = 0.05 * $grandtotal;
    } elseif ($grandtotal <= 100000) {
        $d = "10%";
        $diskon = 0.10 * $grandtotal;
    } else {
        $d = "15%";
        $diskon = 0.15 * $grandtotal;
    }

    $totalbayar = $grandtotal - $diskon;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>POLGAN MART - Single Input</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-3">Input Barang</h2>

    <form method="post" class="mb-4">

        <!-- Kode Barang (SELECT) -->
        <div class="mb-3">
            <label class="form-label">Kode Barang</label>
            <select name="kode" class="form-select" required onchange="this.form.submit()">
                <option value="">Pilih Kode Barang</option>
                <?php foreach ($items as $k => $v): ?>
                    <option value="<?php echo $k; ?>"
                        <?php echo ($kode === $k) ? 'selected' : ''; ?>>
                        <?php echo $k . ' - ' . $v['nama']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nama Barang -->
        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" name="nama" class="form-control"
                   value="<?php echo htmlspecialchars($nama); ?>" readonly>
        </div>

        <!-- Harga -->
        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number" name="harga" class="form-control" min="0"
                   value="<?php echo $harga; ?>" readonly>
        </div>

        <!-- Jumlah -->
        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1"
                   value="<?php echo $jumlah; ?>">
        </div>

        <!-- Tombol Tambahkan & Batal -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Tambahkan</button>
            <button type="reset" class="btn btn-secondary">Batal</button>
        </div>

    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h2>Ringkasan Pembelian</h2>

        <table class="table table-bordered">
            <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th class="text-end">Harga (Rp)</th>
                <th class="text-center">Jumlah</th>
                <th class="text-end">Total Baris (Rp)</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo htmlspecialchars($kode); ?></td>
                <td><?php echo htmlspecialchars($nama); ?></td>
                <td class="text-end"><?php echo number_format($harga, 0, ',', '.'); ?></td>
                <td class="text-center"><?php echo $jumlah; ?></td>
                <td class="text-end"><?php echo number_format($lineTotal, 0, ',', '.'); ?></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                <td class="text-end"><?php echo number_format($grandtotal, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-end"><strong>Diskon (<?php echo $d; ?>)</strong></td>
                <td class="text-end"><?php echo number_format($diskon, 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-end"><strong>Total Bayar</strong></td>
                <td class="text-end"><?php echo number_format($totalbayar, 0, ',', '.'); ?></td>
            </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>

</body>
</html>