<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../config/db.php'; // add DB connection to insert admin request when 'admin' is selected
verify_csrf();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = trim($_POST['role'] ?? '');
$password = $_POST['password'] ?? '';

$allowedRoles = ['customer', 'farmer', 'admin']; // allow 'admin' as a selectable role

if (!$name || !$email || !$password || !in_array($role, $allowedRoles, true)) {
  flash('error', 'Please complete all fields and select a valid role (Customer, Farmer, or Admin).');
  header('Location: ../signup.php'); exit;
}

if (find_user_by_email($email)) {
  flash('error', 'Email already registered.');
  header('Location: ../signup.php'); exit;
}

$selectedRole = $role;
$actualRole = in_array($role, ['customer','farmer'], true) ? $role : 'customer';

$userId = create_user($name, $email, $password, $actualRole);
$user = ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => $actualRole];
login_user($user);

if ($selectedRole === 'admin') {
  $pdo = Database::getConnection();
  $ins = $pdo->prepare('INSERT INTO admin_access_requests (user_id, status) VALUES (?, "pending")');
  $ins->execute([(int)$userId]);

  flash('success', 'We sent your admin access request. You will see status updates on this page.');
  header('Location: ../admin/request-access.php'); exit;
}

header('Location: ../' . $actualRole . '/dashboard.php'); exit;
