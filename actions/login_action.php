<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/auth.php';
verify_csrf();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$user = find_user_by_email($email);
if (!$user || !password_verify($password, $user['password_hash'])) {
  flash('error', 'Invalid credentials.');
  header('Location: ../login.php'); exit;
}

login_user($user);
if ($user['role'] === 'customer') header('Location: ../customer/dashboard.php');
elseif ($user['role'] === 'farmer') header('Location: ../farmer/dashboard.php');
else header('Location: ../admin/dashboard.php');
