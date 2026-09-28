<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = Database::getInstance()->getConnection();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $supplierId = (int) ($_POST['supplier_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    if ($name === '') {
        $errors[] = 'Nama produk wajib diisi.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Kategori wajib dipilih.';
    }
    if ($supplierId <= 0) {
        $errors[] = 'Supplier wajib dipilih.';
    }
    if ($price < 0) {
        $errors[] = 'Harga tidak boleh negatif.';
    }
    if ($stock < 0) {
        $errors[] = 'Stok tidak boleh negatif.';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO products (name, category_id, supplier_id, price, stock)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $categoryId, $supplierId, $price, $stock]);

        setFlash('success', 'Produk berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$suppliers = $pdo->query('SELECT id, name FROM suppliers ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Produk</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <header class="page-header">
        <h1>Tambah Produk</h1>
        <a class="btn btn-link" href="index.php">Kembali</a>
    </header>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="data-form" method="POST" action="create.php">
        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" id="name" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select id="category_id" name="category_id" required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int) ($_POST['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">Pilih Supplier</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= (int) $s['id'] ?>" <?= (int) ($_POST['supplier_id'] ?? 0) === (int) $s['id'] ? 'selected' : '' ?>>
                        <?= e($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="price">Harga</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="<?= e($_POST['price'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="stock">Stok</label>
            <input type="number" id="stock" name="stock" min="0" value="<?= e($_POST['stock'] ?? '') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
</body>
</html>
