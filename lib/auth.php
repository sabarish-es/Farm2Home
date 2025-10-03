<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/utils.php';

function find_user_by_email(string $email) {
  $pdo = Database::getConnection();
  $stmt = $pdo->prepare('SELECT id, name, email, role, password_hash FROM users WHERE email = ? AND (deleted_at IS NULL) LIMIT 1');
  $stmt->execute([$email]);
  return $stmt->fetch();
}

function create_user(string $name, string $email, string $password, string $role): int {
  $pdo = Database::getConnection();
  $hash = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?,?,?,?)');
  $stmt->execute([$name, $email, $hash, $role]);
  return (int)$pdo->lastInsertId();
}

function login_user(array $user): void {
  $_SESSION['user'] = [
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role'],
  ];
}

function logout_user(): void {
  $_SESSION = [];
  session_destroy();
}

function admin_exists(): bool {
  $pdo = Database::getConnection();
  $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'admin' AND (deleted_at IS NULL)");
  $row = $stmt->fetch();
  return ((int)($row['cnt'] ?? 0)) > 0;
}

function first_admin_id(): ?int {
  $pdo = Database::getConnection();
  $stmt = $pdo->query("SELECT id FROM users WHERE role = 'admin' AND (deleted_at IS NULL) ORDER BY id ASC LIMIT 1");
  $row = $stmt->fetch();
  return $row ? (int)$row['id'] : null;
}
