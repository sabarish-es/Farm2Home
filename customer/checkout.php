<?php require_once __DIR__ . '/../partials/header.php'; require_role('customer'); ?>
<h2>Checkout</h2>
<form class="form" method="post" action="<?= e(APP_URL) ?>/actions/order_place.php">
  <?= csrf_input() ?>
  <label class="label">Delivery Address</label>
  <textarea class="textarea" name="address" rows="3" required></textarea>
  <label class="label">Delivery Slot</label>
  <select class="select" name="slot" required>
    <option value="Morning (8-11am)">Morning (8-11am)</option>
    <option value="Afternoon (12-3pm)">Afternoon (12-3pm)</option>
    <option value="Evening (5-8pm)">Evening (5-8pm)</option>
  </select>
  <label class="label">Payment Method</label>
  <select class="select" name="payment" required>
    <option value="Online (Simulated)">Online (Simulated)</option>
    <option value="Cash on Delivery">Cash on Delivery</option>
  </select>
  <button class="btn btn-primary" type="submit">Place Order</button>
</form>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
