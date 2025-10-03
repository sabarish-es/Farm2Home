<?php require_once __DIR__ . '/partials/header.php'; require_once __DIR__ . '/config/db.php';

$pdo = Database::getConnection();
// Sample featured products (latest 8)
$stmt = $pdo->query("SELECT p.id, p.name, p.price, p.unit, p.image_path FROM products p WHERE p.active = 1 ORDER BY p.created_at DESC LIMIT 8");
$featured = $stmt->fetchAll();
?>
<section class="hero">
  <div class="hero-card">
    <h1 class="text-balance" style="margin-top:0">Find Fresh Farm Products Near You</h1>
    <p>Support farmers, eat fresh, and get deliveries to your doorstep.</p>
    <form class="search" action="<?= e(APP_URL) ?>/products/catalog.php" method="get">
      <input name="q" type="text" placeholder="Search vegetables, fruits, grains..." />
      <button class="btn btn-primary" type="submit">Search</button>
    </form>
    <div style="margin-top:1rem">
      <span class="badge">Seasonal</span>
      <span class="badge">Organic</span>
      <span class="badge">Local</span>
    </div>
  </div>
  <div class="hero-card">
    <img src="<?= e(APP_URL) ?>/assets/img/hero.jpg" alt="Fresh farm produce" style="width:100%;height:100%;object-fit:cover;border-radius:10px" />
  </div>
</section>

<h2>Featured Products</h2>
<div class="grid grid-4">
  <?php foreach ($featured as $p): ?>
    <div class="card">
      <img src="<?= e(APP_URL) ?>/<?= e($p['image_path'] ?: 'assets/img/sample-veg.jpg') ?>" alt="<?= e($p['name']) ?>" />
      <div class="content">
        <div class="meta">
          <strong><?= e($p['name']) ?></strong>
          <span><?= money((float)$p['price']) ?> / <?= e($p['unit'] ?? 'kg') ?></span>
        </div>
        <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/products/product.php?id=<?= (int)$p['id'] ?>">View</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
