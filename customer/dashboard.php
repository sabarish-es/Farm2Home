<?php require_once __DIR__ . '/../partials/header.php'; require_role('customer'); ?>
<h2>Welcome back!</h2>
<?php
require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();
$latest = null;
try {
  $st = $pdo->prepare("SELECT id, status, delivery_time, created_at FROM orders WHERE customer_id = ? ORDER BY created_at DESC LIMIT 1");
  $st->execute([current_user()['id']]);
  $latest = $st->fetch();
} catch (Exception $e) { /* ignore */ }
?>
<?php if ($latest): ?>
  <div class="card" style="margin-bottom: 16px;">
    <div class="content">
      <strong>Latest Order #<?= (int)$latest['id'] ?></strong>
      <p>Status: <?= e(ucfirst($latest['status'])) ?></p>
      <p>
        Delivery Time:
        <?php if (!empty($latest['delivery_time'])): ?>
          <?= e(date('M d, Y H:i', strtotime($latest['delivery_time']))) ?>
        <?php else: ?>
          <em>Not scheduled yet</em>
        <?php endif; ?>
      </p>
      <p><small>Placed at: <?= e(date('M d, Y H:i', strtotime($latest['created_at']))) ?></small></p>
      <a class="btn" href="<?= e(APP_URL) ?>/customer/orders.php">View all orders</a>
    </div>
  </div>
<?php endif; ?>
<div class="grid grid-4">
  <a class="card" href="<?= e(APP_URL) ?>/products/catalog.php">
    <div class="content"><strong>Browse Products</strong><p>Find fresh items</p></div>
  </a>
  <a class="card" href="<?= e(APP_URL) ?>/customer/cart.php">
    <div class="content"><strong>Cart</strong><p>Review your cart</p></div>
  </a>
  <a class="card" href="<?= e(APP_URL) ?>/customer/orders.php">
    <div class="content"><strong>My Orders</strong><p>Track orders</p></div>
  </a>
  <a class="card" href="<?= e(APP_URL) ?>/customer/chat.php">
    <div class="content"><strong>Chat with Farmers</strong><p>Get more details</p></div>
  </a>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
