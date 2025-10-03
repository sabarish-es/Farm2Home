<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function flash(string $key, ?string $value = null) {
  if ($value === null) {
    if (!empty($_SESSION['flash'][$key])) {
      $msg = $_SESSION['flash'][$key];
      unset($_SESSION['flash'][$key]);
      return $msg;
    }
    return null;
  }
  $_SESSION['flash'][$key] = $value;
}

function csrf_token(): string {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function csrf_input(): string {
  return '<input type="hidden" name="csrf" value="'.htmlspecialchars(csrf_token()).'">';
}

function verify_csrf(): void {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
    if (!$ok) {
      http_response_code(400);
      exit('Invalid CSRF token.');
    }
  }
}

function is_logged_in(): bool {
  return !empty($_SESSION['user']);
}

function current_user() {
  return $_SESSION['user'] ?? null;
}

function require_login(): void {
  if (!is_logged_in()) {
    flash('error', 'Please login to continue.');
    header('Location: ' . APP_URL . '/login.php');
    exit;
  }
}

function require_role(string $role): void {
  require_login();
  if (($_SESSION['user']['role'] ?? '') !== $role) {
    http_response_code(403);
    exit('Forbidden.');
  }
}

function e(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function money(float $n): string {
  return '₹' . number_format($n, 2);
}
