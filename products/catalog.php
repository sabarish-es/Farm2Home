<?php require_once __DIR__ . '/../partials/header.php'; require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();
$q = trim($_GET['q'] ?? '');
$cat = trim($_GET['category'] ?? '');
$where = 'WHERE p.active = 1';
$params = [];
if ($q) { $where .= ' AND p.name LIKE ?'; $params[] = "%$q%"; }
if ($cat) { $where .= ' AND p.category = ?'; $params[] = $cat; }
$stmt = $pdo->prepare("SELECT p.id,p.name,p.price,p.unit,p.image_path,p.category FROM products p $where ORDER BY p.created_at DESC LIMIT 40");
$stmt->execute($params);
$items = $stmt->fetchAll();
?>
<h2>Browse Products</h2>
<form class="search" method="get">
  <input type="text" name="q" placeholder="Search..." value="<?= e($q) ?>" />
  <select class="select" name="category">
    <option value="">All Categories</option>
    <?php foreach(['Fruits','Vegetables','Grains','Dairy'] as $c): ?>
      <option value="<?= e($c) ?>" <?= $c===$cat?'selected':'' ?>><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-primary" type="submit">Filter</button>
</form>
<div class="grid grid-4" style="margin-top:1rem">
  <?php foreach($items as $p): ?>
    <div class="card">
      <img src="<?= e(APP_URL) ?>/<?= e($p['image_path'] ?: 'assets/img/sample-veg.jpg') ?>" alt="<?= e($p['name']) ?>">
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
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
