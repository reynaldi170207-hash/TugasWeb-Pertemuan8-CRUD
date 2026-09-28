<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = Database::getInstance()->getConnection();

$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 5;
$offset = ($page - 1) * $perPage;

$whereClause = '';
$params = [];

if ($search !== '') {
    $whereClause = 'WHERE p.name LIKE ? OR c.name LIKE ? OR s.name LIKE ?';
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
}

$countSql = "SELECT COUNT(*) AS total
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN suppliers s ON p.supplier_id = s.id
    {$whereClause}";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int) $countStmt->fetch()['total'];
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$sql = "SELECT p.id, p.name, p.price, p.stock,
        c.name AS category_name,
        s.name AS supplier_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN suppliers s ON p.supplier_id = s.id
    {$whereClause}
    ORDER BY p.name ASC
    LIMIT {$perPage} OFFSET {$offset}";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Inventaris Barang</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <header class="page-header">
        <h1>Manajemen Inventaris Barang</h1>
        <a class="btn btn-primary" href="create.php">Tambah Produk</a>
    </header>

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <form class="search-form" method="GET" action="index.php">
        <input type="text" name="search" placeholder="Cari nama produk, kategori, atau supplier" value="<?= e($search) ?>">
        <button type="submit" class="btn btn-secondary">Cari</button>
        <?php if ($search !== ''): ?>
            <a class="btn btn-link" href="index.php">Reset</a>
        <?php endif; ?>
    </form>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Supplier</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" class="empty-row">Data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><?= e($p['supplier_name']) ?></td>
                    <td><?= formatRupiah($p['price']) ?></td>
                    <td><?= e($p['stock']) ?></td>
                    <td class="actions">
                        <a class="btn btn-small btn-edit" href="edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
                        <form method="POST" action="delete.php" class="inline-form" onsubmit="return confirm('Hapus produk ini?');">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" class="btn btn-small btn-delete">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a class="page-link <?= $i === $page ? 'active' : '' ?>"
                   href="index.php?page=<?= $i ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
