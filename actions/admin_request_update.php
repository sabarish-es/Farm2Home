<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../config/db.php';

verify_csrf();
require_role('admin');

$currentUserId = (int)($_SESSION['user']['id'] ?? 0);
$firstId = first_admin_id();
if (!$firstId || $currentUserId !== (int)$firstId) {
  flash('error', 'Only the primary admin can approve or deny requests.');
  header('Location: ../admin/users.php'); exit;
}

$requestId = (int)($_POST['request_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$requestId || !in_array($action, ['approve','deny'], true)) {
  flash('error', 'Invalid request.');
  header('Location: ../admin/users.php'); exit;
}

$pdo = Database::getConnection();
$stmt = $pdo->prepare('SELECT id, user_id, status FROM admin_access_requests WHERE id = ? LIMIT 1');
$stmt->execute([$requestId]);
$req = $stmt->fetch();

if (!$req || $req['status'] !== 'pending') {
  flash('error', 'Request not found or already processed.');
  header('Location: ../admin/users.php'); exit;
}

if ($action === 'approve') {
  // upgrade user to admin
  $pdo->beginTransaction();
  try {
    $up1 = $pdo->prepare('UPDATE users SET role = "admin" WHERE id = ?');
    $up1->execute([(int)$req['user_id']]);

    $up2 = $pdo->prepare('UPDATE admin_access_requests SET status = "approved", decided_at = NOW(), decided_by = ? WHERE id = ?');
    $up2->execute([$currentUserId, $requestId]);

    $pdo->commit();
    flash('success', 'Admin access approved.');
  } catch (\Throwable $e) {
    $pdo->rollBack();
    flash('error', 'Failed to approve request.');
  }
} else {
  // deny
  $up = $pdo->prepare('UPDATE admin_access_requests SET status = "denied", decided_at = NOW(), decided_by = ? WHERE id = ?');
  $up->execute([$currentUserId, $requestId]);
  flash('success', 'Admin access request denied.');
}

header('Location: ../admin/users.php'); exit;
