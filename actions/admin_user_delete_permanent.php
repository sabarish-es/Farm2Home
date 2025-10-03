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

// prevent deleting yourself for safety
$currentUser = current_user();
if ($currentUser && (int)$currentUser['id'] === $userId) {
  http_response_code(400);
  exit('You cannot delete yourself.');
}

$pdo = Database::getConnection();

// Only allow hard-delete if the user is already soft-deleted
$stmt = $pdo->prepare('DELETE FROM users WHERE id = ? AND deleted_at IS NOT NULL');
$stmt->execute([$userId]);

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '/admin/dashboard.php';
header('Location: ' . $redirect);
exit;
