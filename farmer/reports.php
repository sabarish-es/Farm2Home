<?php
$pageTitle = 'Sales Reports';
require_once __DIR__ . '/../partials/header.php';
require_role('farmer');

require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();

$farmerId = current_user()['id'];

$daily = $pdo->prepare("
  SELECT DATE(o.created_at) AS day, SUM(oi.unit_price*oi.quantity) AS revenue, SUM(oi.quantity) AS items
  FROM orders o
  JOIN order_items oi ON oi.order_id = o.id
  JOIN products p ON p.id = oi.product_id
  WHERE p.farmer_id = ?
    AND o.status IN ('placed','paid','shipped','delivered')
  GROUP BY DATE(o.created_at)
  ORDER BY day DESC
  LIMIT 30
");
$daily->execute([$farmerId]);
$dailyRows = $daily->fetchAll();

$top = $pdo->prepare("
  SELECT p.name, SUM(oi.quantity) AS qty, SUM(oi.unit_price*oi.quantity) AS revenue
  FROM orders o
  JOIN order_items oi ON oi.order_id = o.id
  JOIN products p ON p.id = oi.product_id
  WHERE p.farmer_id = ?
    AND o.status IN ('placed','paid','shipped','delivered')
  GROUP BY p.id, p.name
  ORDER BY revenue DESC
  LIMIT 5
");
$top->execute([$farmerId]);
$topRows = $top->fetchAll();
?>
<h2>Sales Reports</h2>

<div class="grid" style="grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap: 12px;">
  <div class="card">
    <div class="content">
      <strong>Last 30 Days</strong>
      <?php if (!$dailyRows): ?>
        <p>No sales yet.</p>
      <?php else: ?>
        <ul>
          <?php foreach ($dailyRows as $r): ?>
            <li><strong><?= e($r['day']) ?>:</strong> ₹<?= number_format((float)$r['revenue'], 2) ?> (<?= (int)$r['items'] ?> items)</li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
  <div class="card">
    <div class="content">
      <strong>Top Products</strong>
      <?php if (!$topRows): ?>
        <p>No data yet.</p>
      <?php else: ?>
        <ul>
          <?php foreach ($topRows as $r): ?>
            <li><?= e($r['name']) ?> — <?= (int)$r['qty'] ?> sold, ₹<?= number_format((float)$r['revenue'], 2) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
