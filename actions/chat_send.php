<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_login(); verify_csrf();

$user = current_user();
$farmer_id = (int)($_POST['farmer_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if (!$message) { http_response_code(400); exit('empty'); }

$pdo = Database::getConnection();
if ($user['role'] === 'customer') {
  $stmt = $pdo->prepare("INSERT INTO messages (farmer_id, customer_id, sender, content) VALUES (?,?, 'customer', ?)");
  $stmt->execute([$farmer_id, $user['id'], $message]);
} elseif ($user['role'] === 'farmer') {
  $customer_id = (int)($_POST['customer_id'] ?? 0);
  if (!$customer_id) { http_response_code(400); exit('no customer'); }
  $stmt = $pdo->prepare("INSERT INTO messages (farmer_id, customer_id, sender, content) VALUES (?,?,'farmer', ?)");
  $stmt->execute([$user['id'], $customer_id, $message]);
}
echo 'ok';
