<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../config/db.php';
verify_csrf();

$pdo = Database::getConnection();
$adminCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
if ($adminCount > 0) {
  http_response_code(403);
  exit('Forbidden: Admin already exists.');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
  flash('error', 'All fields are required.');
  header('Location: ../admin/setup.php'); exit;
}
if (strlen($password) < 8) {
  flash('error', 'Password must be at least 8 characters.');
  header('Location: ../admin/setup.php'); exit;
}
if (find_user_by_email($email)) {
  flash('error', 'Email already registered.');
  header('Location: ../admin/setup.php'); exit;
}

$userId = create_user($name, $email, $password, 'admin');
$user = ['id'=>$userId, 'name'=>$name, 'email'=>$email, 'role'=>'admin'];
login_user($user);
flash('success','Admin account created successfully.');
header('Location: ../admin/dashboard.php');
