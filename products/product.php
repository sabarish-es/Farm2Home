<?php require_once __DIR__ . '/../partials/header.php'; require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, u.name as farmer_name FROM products p JOIN users u ON u.id = p.farmer_id WHERE p.id = ? AND p.active=1");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); echo "<p>Product not found.</p>"; require_once __DIR__ . '/../partials/footer.php'; exit; }
?>
<div class="grid" style="grid-template-columns:1fr 1fr;gap:2rem">
  <img src="<?= e(APP_URL) ?>/<?= e($product['image_path'] ?: 'assets/img/sample-veg.jpg') ?>" alt="<?= e($product['name']) ?>" style="width:100%;border-radius:var(--radius)" />
  <div class="hero-card">
    <h2><?= e($product['name']) ?></h2>
    <p><strong><?= money((float)$product['price']) ?> / <?= e($product['unit'] ?? 'kg') ?></strong></p>
    <p>Farmer: <?= e($product['farmer_name']) ?></p>
    <p>Category: <?= e($product['category']) ?></p>
    <p>In stock: <?= (int)$product['stock'] ?> <?= e($product['unit'] ?? 'kg') ?></p>
    <p><?= nl2br(e($product['description'])) ?></p>
    <form method="post" action="<?= e(APP_URL) ?>/actions/cart_add.php">
      <?= csrf_input() ?>
      <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>" />
      <label class="label">Quantity</label>
      <input class="input" type="number" name="qty" value="1" min="1" max="<?= (int)$product['stock'] ?>" />
      <button class="btn btn-primary" type="submit">Add to Cart</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
