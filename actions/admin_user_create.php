<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/auth.php';
verify_csrf();
require_role('admin');

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password || !in_array($role, ['customer','farmer','admin'], true)) {
  flash('error','All fields are required and role must be valid.');
  header('Location: ../admin/users.php'); exit;
}
if (strlen($password) < 8) {
  flash('error','Password must be at least 8 characters.');
  header('Location: ../admin/users.php'); exit;
}
if (find_user_by_email($email)) {
  flash('error','Email already exists.');
  header('Location: ../admin/users.php'); exit;
}

$userId = create_user($name, $email, $password, $role);
flash('success', 'User created: ' . $email);
header('Location: ../admin/users.php');
