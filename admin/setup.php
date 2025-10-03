<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../partials/header.php';

$pdo = Database::getConnection();
$adminCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();

if ($adminCount > 0) {
  // If admin already exists, send to login
  flash('error', 'Admin already exists. Please login.');
  header('Location: ' . APP_URL . '/login.php'); exit;
}
?>
<h2 class="text-pretty">Create Admin Account</h2>
<p style="margin-bottom:1rem">No admin account found. Create the first admin to continue.</p>
<form class="form" method="post" action="<?= e(APP_URL) ?>/actions/admin_setup_action.php" style="max-width:560px">
  <?= csrf_input() ?>
  <label class="label">Name</label>
  <input class="input" type="text" name="name" required />
  <label class="label">Email</label>
  <input class="input" type="email" name="email" required />
  <label class="label">Password</label>
  <input class="input" type="password" name="password" minlength="8" required />
  <button class="btn btn-primary" type="submit">Create Admin</button>
</form>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
