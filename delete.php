<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('danger', 'Produk tidak ditemukan.');
    header('Location: index.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $deleteStmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $deleteStmt->execute([$id]);

    $logStmt = $pdo->prepare(
        'INSERT INTO stock_logs (product_id, product_name, action, quantity, note)
         VALUES (?, ?, ?, ?, ?)'
    );
    $logStmt->execute([
        $product['id'],
        $product['name'],
        'deleted',
        $product['stock'],
        'Produk dihapus melalui aplikasi inventaris',
    ]);

    $pdo->commit();
    setFlash('success', 'Produk berhasil dihapus dan tercatat di log aktivitas.');
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Delete product failed: ' . $e->getMessage());
    setFlash('danger', 'Gagal menghapus produk, silakan coba lagi.');
}

header('Location: index.php');
exit;
