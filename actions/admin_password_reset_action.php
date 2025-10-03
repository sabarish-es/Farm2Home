<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
verify_csrf();

if (!defined('APP_ENV') || APP_ENV !== 'local') {
  http_response_code(403);
  exit('Forbidden: Admin password reset is only available in local environment.');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
  flash('error', 'Email and password are required.');
  header('Location: ../admin/reset-admin.php'); exit;
}
if (strlen($password) < 8) {
  flash('error', 'Password must be at least 8 characters.');
  header('Location: ../admin/reset-admin.php'); exit;
}

try {
  $pdo = Database::getConnection();

  // Ensure the target is an admin user
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
  $stmt->execute([$email]);
  $admin = $stmt->fetch();

  if (!$admin) {
    flash('error', 'No admin found with that email.');
    header('Location: ../admin/reset-admin.php'); exit;
  }

  $hash = password_hash($password, PASSWORD_BCRYPT);
  $upd = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ? LIMIT 1");
  $upd->execute([$hash, $admin['id']]);

  flash('success', 'Admin password updated. You can now log in.');
  header('Location: ../login.php'); exit;

} catch (Throwable $e) {
  // Avoid leaking details; provide a generic error
  flash('error', 'Unexpected error updating password.');
  header('Location: ../admin/reset-admin.php'); exit;
}
