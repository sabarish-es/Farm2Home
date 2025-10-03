<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../partials/header.php';

if (defined('APP_ENV') && APP_ENV !== 'local') {
  http_response_code(403);
  exit('Forbidden: Admin password reset is only available in local environment.');
}
?>
<h2 class="text-pretty">Reset Admin Password (Local Only)</h2>
<p style="margin-bottom:1rem">Use this tool locally to update the admin password if you cannot log in.</p>

<form class="form" method="post" action="<?= e(APP_URL) ?>/actions/admin_password_reset_action.php" style="max-width:560px">
  <?= csrf_input() ?>
  <label class="label">Admin Email</label>
  <input class="input" type="email" name="email" value="admin@farm2home.local" required />
  <label class="label">New Password</label>
  <input class="input" type="password" name="password" minlength="8" required />
  <button class="btn btn-primary" type="submit">Update Password</button>
</form>

<p style="margin-top:1rem">
  <a href="<?= e(APP_URL) ?>/login.php">Back to Login</a>
</p>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
