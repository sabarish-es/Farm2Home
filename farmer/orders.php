<?php
$pageTitle = 'Farmer Orders';
require_once __DIR__ . '/../partials/header.php';
require_role('farmer');

require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();

$st = $pdo->prepare("
  SELECT o.id, o.status, o.total_amount, o.created_at, u.name AS customer_name,
         o.address, o.delivery_slot, o.payment_method
  FROM orders o
  JOIN order_items oi ON oi.order_id = o.id
  JOIN products p ON p.id = oi.product_id
  JOIN users u ON u.id = o.customer_id
  WHERE p.farmer_id = ?
  GROUP BY o.id, o.status, o.total_amount, o.created_at, u.name, o.address, o.delivery_slot, o.payment_method
  ORDER BY o.created_at DESC
");
$st->execute([current_user()['id']]);
$orders = $st->fetchAll();
?>
<h2>Orders</h2>
<?php if (!$orders): ?>
  <p>No orders yet.</p>
<?php else: ?>
  <div class="grid">
    <?php foreach ($orders as $o): ?>
      <div class="card">
        <div class="content">
          <strong>#<?= (int)$o['id'] ?></strong>
          <p>Customer: <?= e($o['customer_name']) ?></p>
          <p>Status: <?= e(ucfirst($o['status'])) ?></p>
          <p>Total: ₹<?= number_format((float)$o['total_amount'], 2) ?></p>
          <p>Address:<br><?= nl2br(e($o['address'])) ?></p>
          <p>Delivery Slot: <?= e($o['delivery_slot']) ?></p>
          <p>Payment: <?= e($o['payment_method']) ?></p>
          <p><small><?= e($o['created_at']) ?></small></p>
          <div class="actions" style="margin-top:8px;">
            <?php if (strtolower($o['status']) === 'placed'): ?>
              <form method="post" action="<?= e(APP_URL) ?>/actions/order_update_status.php" style="display:inline-block; margin-right:8px;">
                <?= csrf_input() ?>
                <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                <input type="hidden" name="action" value="receive">
                <button type="submit" class="btn">Mark as Received</button>
              </form>
            <?php endif; ?>
            <form method="post" action="<?= e(APP_URL) ?>/actions/order_update_status.php" style="display:inline-flex; gap:8px; align-items:center; margin-top:6px;">
              <?= csrf_input() ?>
              <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
              <input type="hidden" name="action" value="set_delivery_time">
              <label for="dt-<?= (int)$o['id'] ?>"><small>Set Delivery Time:</small></label>
              <input id="dt-<?= (int)$o['id'] ?>" type="datetime-local" name="delivery_time" required>
              <button type="submit" class="btn">Save</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
