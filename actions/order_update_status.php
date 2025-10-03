<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_role('farmer');
verify_csrf();

$action = $_POST['action'] ?? '';
$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0 || !$action) {
  flash('error', 'Invalid request.');
  header('Location: ../farmer/orders.php');
  exit;
}

$pdo = Database::getConnection();

// Verify this order contains at least one item from current farmer
$check = $pdo->prepare("
  SELECT 1
  FROM orders o
  JOIN order_items oi ON oi.order_id = o.id
  JOIN products p ON p.id = oi.product_id
  WHERE o.id = ? AND p.farmer_id = ?
  LIMIT 1
");
$check->execute([$orderId, current_user()['id']]);
if (!$check->fetch()) {
  http_response_code(403);
  exit('Forbidden.');
}

try {
  if ($action === 'receive') {
    $st = $pdo->prepare("UPDATE orders SET status = 'received' WHERE id = ?");
    $st->execute([$orderId]);
    flash('success', 'Order marked as received.');
  } elseif ($action === 'set_delivery_time') {
    // Expecting input like 2025-10-05T15:30 from <input type="datetime-local">
    $dt = trim($_POST['delivery_time'] ?? '');
    if (!$dt) {
      flash('error', 'Please provide a delivery time.');
      header('Location: ../farmer/orders.php');
      exit;
    }
    // Normalize to MySQL DATETIME
    $dt = str_replace('T', ' ', $dt) . (strlen($dt) === 16 ? ':00' : '');
    $st = $pdo->prepare("
      UPDATE orders
      SET delivery_time = ?, status = IF(status = 'placed','received', status)
      WHERE id = ?
    ");
    $st->execute([$dt, $orderId]);
    flash('success', 'Delivery time set.');
  } else {
    flash('error', 'Unknown action.');
  }
} catch (Exception $e) {
  flash('error', 'Failed to update order.');
}

header('Location: ../farmer/orders.php');
