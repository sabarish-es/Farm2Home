<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';

require_login();
$user = current_user();
if (($user['role'] ?? null) === 'admin') {
  header('Location: ' . APP_URL . '/admin/dashboard.php'); exit;
}

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  // Prevent duplicate pendings
  $st = $pdo->prepare('SELECT COUNT(*) AS cnt FROM admin_access_requests WHERE user_id = ? AND status = "pending"');
  $st->execute([(int)$user['id']]);
  $hasPending = (int)$st->fetchColumn() > 0;

  if ($hasPending) {
    flash('error', 'You already have a pending admin access request.');
    header('Location: ' . APP_URL . '/admin/request-access.php'); exit;
  }

  // Allow re-request if last was denied or none exists
  $ins = $pdo->prepare('INSERT INTO admin_access_requests (user_id, status) VALUES (?, "pending")');
  $ins->execute([(int)$user['id']]);
  flash('success', 'Your admin access request has been sent. Please wait for approval by the primary admin.');
  header('Location: ' . APP_URL . '/admin/request-access.php'); exit;
}

// Fetch latest request
$latest = null;
$ls = $pdo->prepare('SELECT id, status, requested_at, decided_at FROM admin_access_requests WHERE user_id = ? ORDER BY requested_at DESC LIMIT 1');
$ls->execute([(int)$user['id']]);
$latest = $ls->fetch();
?>
<div class="hero-card" style="max-width:640px;margin:2rem auto">
  <h2 class="text-pretty">Admin Access Request</h2>

  <?php if (!$latest): ?>
    <div class="card" style="margin:1rem 0">
      <div class="content">
        <p>You haven’t requested admin access yet. Click below to send a request to the primary admin.</p>
        <form method="post" action="<?= e(APP_URL) ?>/admin/request-access.php" style="margin-top:0.75rem">
          <?= csrf_input() ?>
          <button type="submit" class="btn btn-primary">Request Admin Access</button>
        </form>
      </div>
    </div>

  <?php elseif ($latest['status'] === 'pending'): ?>
    <div class="card" role="status" style="border-left:4px solid var(--color-primary);margin:1rem 0">
      <div class="content">
        <strong>Status:</strong> Waiting for approval
        <p style="margin-top:0.5rem">Your request (ID: <?= (int)$latest['id'] ?>) is pending. The primary admin will review it shortly.</p>
      </div>
    </div>

  <?php elseif ($latest['status'] === 'approved'): ?>
    <div class="card" role="status" style="border-left:4px solid var(--color-primary);margin:1rem 0">
      <div class="content">
        <strong>Status:</strong> Approved
        <p style="margin-top:0.5rem">
          Your request has been approved. If you don’t see the Admin link yet, please log out and log back in to refresh your permissions.
        </p>
        <div style="display:flex;gap:0.5rem;margin-top:0.75rem">
          <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/admin/dashboard.php">Go to Admin</a>
          <a class="btn btn-secondary" href="<?= e(APP_URL) ?>/logout.php">Logout</a>
        </div>
      </div>
    </div>

  <?php elseif ($latest['status'] === 'denied'): ?>
    <div class="card" role="alert" style="border-left:4px solid #ff7043;margin:1rem 0">
      <div class="content">
        <strong>Status:</strong> Denied
        <p style="margin-top:0.5rem">Your previous admin access request was denied. You may re-submit a new request if needed.</p>
        <form method="post" action="<?= e(APP_URL) ?>/admin/request-access.php" style="margin-top:0.75rem">
          <?= csrf_input() ?>
          <button type="submit" class="btn btn-primary">Request Again</button>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
