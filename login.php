<?php require_once __DIR__ . '/partials/header.php'; ?>
<div class="hero-card" style="max-width:520px;margin:2rem auto">
  <h2>Login</h2>
  <form class="form" method="post" action="<?= e(APP_URL) ?>/actions/login_action.php">
    <?= csrf_input() ?>
    <label class="label">Email</label>
    <input class="input" type="email" name="email" required />
    <label class="label">Password</label>
    <input class="input" type="password" name="password" required />
    <button class="btn btn-primary" type="submit">Login</button>
  </form>
  <?php if (defined('APP_ENV') && APP_ENV === 'local'): ?>
    <p style="margin-top:0.75rem">
      <a href="<?= e(APP_URL) ?>/admin/reset-admin.php">Reset admin password (local only)</a>
    </p>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
