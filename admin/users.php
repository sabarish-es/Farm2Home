<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_role('admin');

$pdo = Database::getConnection();
$users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 200")->fetchAll();

$pending = $pdo->query("
  SELECT r.id AS request_id, u.id AS user_id, u.name, u.email, u.role, u.created_at
  FROM admin_access_requests r
  JOIN users u ON r.user_id = u.id
  WHERE r.status = 'pending'
  ORDER BY r.requested_at ASC
")->fetchAll();

$isPrimaryAdmin = false;
if (isset($_SESSION['user']['id'])) {
  $firstId = first_admin_id();
  $isPrimaryAdmin = $firstId !== null && (int)$_SESSION['user']['id'] === (int)$firstId;
}

require_once __DIR__ . '/../partials/header.php';
?>
<h2 class="text-pretty">Manage Users</h2>

<?php if ($pending): ?>
  <div class="card" style="margin-bottom:1rem">
    <div class="content">
      <strong>Pending Admin Access Requests</strong>
      <div class="table-responsive" style="overflow:auto;margin-top:0.75rem">
        <table class="table">
          <thead>
            <tr>
              <th align="left">Request ID</th>
              <th align="left">User</th>
              <th align="left">Email</th>
              <th align="left">Current Role</th>
              <th align="left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pending as $p): ?>
              <tr>
                <td><?= (int)$p['request_id'] ?></td>
                <td><?= e($p['name']) ?> (ID: <?= (int)$p['user_id'] ?>)</td>
                <td><?= e($p['email']) ?></td>
                <td><?= e($p['role']) ?></td>
                <td>
                  <?php if ($isPrimaryAdmin): ?>
                    <form method="post" action="<?= e(APP_URL) ?>/actions/admin_request_update.php" style="display:inline-block;margin-right:0.5rem">
                      <?= csrf_input() ?>
                      <input type="hidden" name="request_id" value="<?= (int)$p['request_id'] ?>" />
                      <input type="hidden" name="action" value="approve" />
                      <button class="btn btn-primary" type="submit">Approve</button>
                    </form>
                    <form method="post" action="<?= e(APP_URL) ?>/actions/admin_request_update.php" style="display:inline-block">
                      <?= csrf_input() ?>
                      <input type="hidden" name="request_id" value="<?= (int)$p['request_id'] ?>" />
                      <input type="hidden" name="action" value="deny" />
                      <button class="btn btn-secondary" type="submit">Deny</button>
                    </form>
                  <?php else: ?>
                    <span>Only the primary admin can decide</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="grid" style="display:grid;grid-template-columns:1fr;gap:1rem;margin-bottom:2rem">
  <div class="card">
    <div class="content">
      <strong>Create New User</strong>
      <form class="form" method="post" action="<?= e(APP_URL) ?>/actions/admin_user_create.php" style="margin-top:1rem">
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
</div>

<div class="card">
  <div class="content">
    <strong>Users</strong>
    <div class="table-responsive" style="overflow:auto;margin-top:0.75rem">
      <table class="table">
        <thead>
          <tr>
            <th align="left">ID</th>
            <th align="left">Name</th>
            <th align="left">Email</th>
            <th align="left">Role</th>
            <th align="left">Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= (int)$u['id'] ?></td>
              <td><?= e($u['name']) ?></td>
              <td><?= e($u['email']) ?></td>
              <td><?= e($u['role']) ?></td>
              <td><?= e($u['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$users): ?>
            <tr><td colspan="5">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
