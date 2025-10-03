<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';

// Ensure only admins access this page BEFORE any HTML output
require_role('admin');

// Include header after access control
require_once __DIR__ . '/../partials/header.php';

// Prepare data on the server side inside PHP (previously was outside PHP tags)
$pdo = Database::getConnection();
$counts = [
  'users' => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
  'farmers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='farmer' AND (deleted_at IS NULL)")->fetchColumn(),
  'customers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='customer' AND (deleted_at IS NULL)")->fetchColumn(),
  'active_admins' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin' AND (deleted_at IS NULL)")->fetchColumn(),
  'products' => (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
  'orders' => (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
  'events' => (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn(),
  'active_users' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn(),
  'removed_users' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NOT NULL")->fetchColumn(),
  'removed_customers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='customer' AND deleted_at IS NOT NULL")->fetchColumn(),
  'removed_farmers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='farmer' AND deleted_at IS NOT NULL")->fetchColumn(),
  'removed_admins' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin' AND deleted_at IS NOT NULL")->fetchColumn(),
];

$recentRemoved = $pdo->query("SELECT id, name, email, role, deleted_at FROM users WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC LIMIT 10")->fetchAll();
?>
<style>
  .grid.grid-4 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0.75rem;
    margin: 0.75rem 0 1rem;
  }
  .card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #ffffff;
    transition: box-shadow 160ms ease, transform 160ms ease;
  }
  .card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.06); transform: translateY(-1px); }
  .card .content { padding: 0.9rem 1rem; }
  .btn-danger {
    background: #dc2626; color: #fff; border: none;
  }
  .btn-danger:hover { background: #b91c1c; }
  .btn-secondary { background: #374151; color: #fff; }
  .btn-secondary:hover { background: #1f2937; }
  .table thead th {
    background: #f7fafc; color: #111827; border-bottom: 1px solid #e5e7eb;
  }
  .badge {
    display: inline-block; padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 12px;
    background: #eef2ff; color: #3730a3; border: 1px solid #e5e7eb;
  }
</style>
<h2>Admin Dashboard</h2>
<div style="margin: 0 0 1rem 0">
  <a class="btn btn-primary" href="<?= e(APP_URL) ?>/admin/users.php">Manage Users</a>
</div>
<div class="grid grid-4">
  <div class="card"><div class="content"><strong>Users (All)</strong><div>Total: <?= (int)$counts['users'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Users (Active)</strong><div>Total: <?= (int)$counts['active_users'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Users (Removed)</strong><div>Total: <?= (int)$counts['removed_users'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Farmers</strong><div>Active: <?= (int)$counts['farmers'] ?> | Removed: <?= (int)$counts['removed_farmers'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Customers</strong><div>Active: <?= (int)$counts['customers'] ?> | Removed: <?= (int)$counts['removed_customers'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Admins</strong><div>Active: <?= (int)$counts['active_admins'] ?> | Removed: <?= (int)$counts['removed_admins'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Products</strong><div>Total: <?= (int)$counts['products'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Orders</strong><div>Total: <?= (int)$counts['orders'] ?></div></div></div>
  <div class="card"><div class="content"><strong>Events</strong><div>Total: <?= (int)$counts['events'] ?></div></div></div>
</div>
<div class="grid" style="display:grid;grid-template-columns:1fr;gap:1rem;margin:1rem 0">
  <div class="card">
    <div class="content">
      <strong>Quick Add User</strong>
      <form class="form" method="post" action="<?= e(APP_URL) ?>/actions/admin_user_create.php" style="margin-top:0.75rem">
        <?= csrf_input() ?>
        <div class="grid" style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem">
          <div>
            <label class="label">Name</label>
            <input class="input" type="text" name="name" required />
          </div>
          <div>
            <label class="label">Email</label>
            <input class="input" type="email" name="email" required />
          </div>
          <div>
            <label class="label">Role</label>
            <select class="input" name="role" required>
              <option value="customer">Customer</option>
              <option value="farmer">Farmer</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div>
            <label class="label">Password</label>
            <input class="input" type="password" name="password" minlength="8" required />
          </div>
        </div>
        <button class="btn btn-primary" type="submit" style="margin-top:0.75rem">Create User</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="content">
      <strong>Quick Remove User</strong>
      <form class="form" method="post" action="<?= e(APP_URL) ?>/actions/admin_user_delete.php" style="margin-top:0.75rem">
        <?= csrf_input() ?>
        <input type="hidden" name="redirect" value="<?= e(APP_URL) ?>/admin/dashboard.php" />
        <div>
          <label class="label">User ID</label>
          <input class="input" type="number" name="user_id" min="1" required />
        </div>
        <button class="btn btn-secondary" type="submit" style="margin-top:0.75rem">Remove</button>
      </form>
    </div>
  </div>

  <?php if (!empty($recentRemoved)): ?>
    <div class="card">
      <div class="content">
        <strong>Recently Removed Users</strong>
        <div class="table-responsive" style="overflow:auto;margin-top:0.75rem">
          <table class="table">
            <thead>
              <tr>
                <th align="left">ID</th>
                <th align="left">Name</th>
                <th align="left">Email</th>
                <th align="left">Role</th>
                <th align="left">Removed At</th>
                <th align="left">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentRemoved as $u): ?>
                <tr>
                  <td><?= (int)$u['id'] ?></td>
                  <td><?= e($u['name']) ?></td>
                  <td><?= e($u['email']) ?></td>
                  <td><span class="badge"><?= e($u['role']) ?></span></td>
                  <td><?= e($u['deleted_at']) ?></td>
                  <td>
                    <form method="post" action="<?= e(APP_URL) ?>/actions/admin_user_restore.php" style="display:inline-block">
                      <?= csrf_input() ?>
                      <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>" />
                      <input type="hidden" name="redirect" value="<?= e(APP_URL) ?>/admin/dashboard.php" />
                      <button class="btn btn-primary" type="submit">Restore</button>
                    </form>
                    <form method="post" action="<?= e(APP_URL) ?>/actions/admin_user_delete_permanent.php" style="display:inline-block;margin-left:0.5rem" onsubmit="return confirm('Permanently delete this user? This cannot be undone.');">
                      <?= csrf_input() ?>
                      <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>" />
                      <input type="hidden" name="redirect" value="<?= e(APP_URL) ?>/admin/dashboard.php" />
                      <button class="btn btn-danger" type="submit">Delete Permanently</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
