<?php require_once __DIR__ . '/partials/header.php'; ?>
<?php // load auth to check if an admin already exists and toggle the "Admin" role option
require_once __DIR__ . '/lib/auth.php';
$adminExists = admin_exists();
?>
<div class="hero-card" style="max-width:560px;margin:2rem auto">
  <h2>Create Account</h2>
  <form class="form" method="post" action="<?= e(APP_URL) ?>/actions/signup_action.php">
    <?= csrf_input() ?>
    <label class="label">Name</label>
    <input class="input" type="text" name="name" required />
    <label class="label">Email</label>
    <input class="input" type="email" name="email" required />
    <label class="label">Password</label>
    <input class="input" type="password" name="password" required />
    <label class="label">Role</label>
    <select class="select" name="role" required>
      <option value="" disabled selected>Select role</option>
      <option value="customer">Customer</option>
      <option value="farmer">Farmer</option>
      <?php if ($adminExists): ?>
        <option value="admin">Admin (requires approval)</option>
      <?php endif; ?>
    </select>

    <?php if ($adminExists): ?>
      <div class="card" style="margin:0.75rem 0">
        <div class="content">
          <small>Choosing “Admin” will send a request for approval. You won’t have admin access until the primary admin approves.</small>
        </div>
      </div>
    <?php else: ?>
      <div class="card" style="margin:0.75rem 0">
        <div class="content">
          <small>No admin exists yet. To create the first admin, use the setup page.</small>
          <div style="margin-top:0.5rem">
            <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/admin/setup.php">Create first admin</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <button class="btn btn-primary" type="submit">Sign Up</button>
  </form>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
