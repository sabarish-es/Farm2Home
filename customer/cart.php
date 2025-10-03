<?php require_once __DIR__ . '/../partials/header.php'; require_role('customer'); 
$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach($cart as $it){ $total += $it['price'] * $it['qty']; }
?>
<h2>My Cart</h2>
<?php if (!$cart): ?>
  <p>Your cart is empty. <a href="<?= e(APP_URL) ?>/products/catalog.php">Browse products</a></p>
<?php else: ?>
  <table class="table">
    <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
    <tbody>
      <?php foreach($cart as $it): ?>
        <tr>
          <td><?= e($it['name']) ?></td>
          <td><?= money((float)$it['price']) ?></td>
          <td><?= (int)$it['qty'] ?></td>
          <td><?= money($it['price']*$it['qty']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="hero-card">
    <strong>Total: <?= money($total) ?></strong>
    <div style="margin-top:1rem">
      <a class="btn btn-primary" href="<?= e(APP_URL) ?>/customer/checkout.php">Proceed to Checkout</a>
    </div>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
