<?php require_once __DIR__ . '/../partials/header.php'; require_role('customer'); require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();
$st = $pdo->prepare("SELECT id, status, total_amount, created_at, address, delivery_slot, payment_method, delivery_time FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
$st->execute([current_user()['id']]);
$orders = $st->fetchAll();
?>
<h2>My Orders</h2>
<table class="table">
  <thead><tr><th>#</th><th>Date</th><th>Status</th><th>Total</th><th>Items</th><th>Address</th><th>Delivery Slot</th><th>Delivery Time</th><th>Payment</th></tr></thead>
  <tbody>
    <?php foreach($orders as $o): ?>
      <tr>
        <td>#<?= (int)$o['id'] ?></td>
        <td><?= e(date('M d, Y H:i', strtotime($o['created_at']))) ?></td>
        <td><?= e(ucfirst($o['status'])) ?></td>
        <td><?= money((float)$o['total_amount']) ?></td>
        <td>
          <?php
            $sti = $pdo->prepare("SELECT p.name, oi.quantity FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?");
            $sti->execute([(int)$o['id']]);
            $its = $sti->fetchAll();
            $summ = [];
            foreach($its as $it){
              $summ[] = e($it['name']).' x '.(int)$it['quantity'];
            }
            echo implode(', ', $summ);
          ?>
        </td>
        <td><?= nl2br(e($o['address'])) ?></td>
        <td><?= e($o['delivery_slot']) ?></td>
        <td>
          <?php if (!empty($o['delivery_time'])): ?>
            <?= e(date('M d, Y H:i', strtotime($o['delivery_time']))) ?>
          <?php else: ?>
            <em>-</em>
          <?php endif; ?>
        </td>
        <td><?= e($o['payment_method']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
