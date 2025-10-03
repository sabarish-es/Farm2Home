<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';

require_role('farmer');
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
  flash('error', 'Invalid product.');
  header('Location: ../farmer/products.php');
  exit;
}

$pdo = Database::getConnection();
$farmerId = current_user()['id'];

$pdo->beginTransaction();
try {
  // Fetch image path for cleanup
  $sel = $pdo->prepare("SELECT image_path FROM products WHERE id = ? AND farmer_id = ?");
  $sel->execute([$id, $farmerId]);
  $row = $sel->fetch();

  if (!$row) {
    $pdo->rollBack();
    flash('error', 'Product not found or not owned by you.');
    header('Location: ../farmer/products.php');
    exit;
  }

  if (!empty($row['image_path'])) {
    $full = __DIR__ . '/../' . $row['image_path'];
    if (is_file($full)) { @unlink($full); }
  }

  $del = $pdo->prepare("DELETE FROM products WHERE id = ? AND farmer_id = ?");
  $del->execute([$id, $farmerId]);

  $pdo->commit();
  flash('success', 'Product deleted.');
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  // If DB constraints block deletion (e.g., linked orders), let the user know
  flash('error', 'Could not delete product. It may be referenced by existing orders.');
}
header('Location: ../farmer/products.php');
