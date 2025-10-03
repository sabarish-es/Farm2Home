<?php require_once __DIR__ . '/../partials/header.php'; require_role('farmer'); require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();
$fid = current_user()['id'];
$items = $pdo->prepare("SELECT id,name,price,stock,active,unit FROM products WHERE farmer_id=? ORDER BY created_at DESC");
$items->execute([$fid]);
$items = $items->fetchAll();
?>
<h2>My Products</h2>
<div class="hero-card">
  <form class="form" method="post" enctype="multipart/form-data" action="<?= e(APP_URL) ?>/actions/product_create.php">
    <?= csrf_input() ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
      <div>
        <label class="label">Name</label>
        <input class="input" type="text" name="name" required />
        <label class="label">Price</label>
        <input class="input" type="number" step="0.01" name="price" required />
        <label class="label">Unit</label>
        <select class="select" name="unit" required>
          <option value="kg">Kilogram (kg)</option>
          <option value="g">Gram (g)</option>
          <option value="mg">Milligram (mg)</option>
          <option value="l">Liter (l)</option>
          <option value="ml">Milliliter (ml)</option>
          <option value="t">Tonne (t)</option>
          <option value="bunch">Bunch</option>
          <option value="pack">Pack</option>
        </select>
        <label class="label">Stock</label>
        <input class="input" type="number" name="stock" required />
        <label class="label">Category</label>
        <select class="select" name="category" required>
          <option>Fruits</option><option>Vegetables</option><option>Grains</option><option>Dairy</option>
        </select>
      </div>
      <div>
        <label class="label">Image</label>
        <input class="input" type="file" name="image" accept="image/*" />
        <label class="label">Description</label>
        <textarea class="textarea" name="description" rows="6"></textarea>
        <button class="btn btn-primary" type="submit">Add Product</button>
      </div>
    </div>
  </form>
</div>

<table class="table" style="margin-top:1rem">
  <thead><tr><th>Name</th><th>Price</th><th>Unit</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach($items as $p): ?>
      <tr>
        <td><?= e($p['name']) ?></td>
        <td><?= money((float)$p['price']) ?></td>
        <td><?= e($p['unit'] ?? 'kg') ?></td>
        <td><?= (int)$p['stock'] ?></td>
        <td><?= $p['active'] ? 'Active' : 'Inactive' ?></td>
        <td>
          <form method="post" action="<?= e(APP_URL) ?>/actions/product_delete.php" onsubmit="return confirm('Delete this product? This cannot be undone.');" style="display:inline">
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>" />
            <button class="btn btn-danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
