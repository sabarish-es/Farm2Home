<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/utils.php';

require_role('admin');
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($userId <= 0) {
  http_response_code(400);
  exit('Invalid user id');
}

// prevent deleting yourself to avoid locking out last admin
$currentUser = current_user();
if ($currentUser && (int)$currentUser['id'] === $userId) {
  http_response_code(400);
  exit('You cannot remove yourself.');
}

$pdo = Database::getConnection();

// soft delete
$stmt = $pdo->prepare('UPDATE users SET deleted_at = NOW() WHERE id = ? AND deleted_at IS NULL');
$stmt->execute([$userId]);

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '/admin/dashboard.php';
header('Location: ' . $redirect);
exit;
