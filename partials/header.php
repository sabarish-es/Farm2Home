<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/utils.php';

$user = current_user();
$role = $user['role'] ?? null;
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($pageTitle) ?></title>
  <link rel="stylesheet" href="<?= e(APP_URL) ?>/assets/css/styles.css" />
</head>
<body>
<header class="header header-sticky">
  <div class="container nav nav-even">
    <div class="nav-slot">
      <a class="logo" href="<?= e(APP_URL) ?>/">
        <img src="<?= e(APP_URL) ?>/assets/img/logo1.jpg" alt="Farm 2 Home logo" class="logo-img" />
        <span class="logo-text">Farm 2 Home</span>
      </a>
    </div>

    <nav class="nav-links" aria-label="Main">
      <a href="<?= e(APP_URL) ?>/" class="nav-link">Home</a>
      <a href="<?= e(APP_URL) ?>/products/catalog.php" class="nav-link">Products</a>
      <a href="<?= e(APP_URL) ?>/events.php" class="nav-link">Events</a>
      <a href="<?= e(APP_URL) ?>/contact.php" class="nav-link">Contact</a>
    </nav>

    <div class="nav-actions nav-slot">
      <?php if ($user): ?>
        <?php if ($role === 'customer'): ?>
          <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/customer/cart.php">Cart</a>
          <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/customer/dashboard.php">Dashboard</a>
          <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/admin/request-access.php">Admin Access</a>
        <?php elseif ($role === 'farmer'): ?>
          <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/farmer/dashboard.php">Farmer Panel</a>
          <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/admin/request-access.php">Admin Access</a>
        <?php elseif ($role === 'admin'): ?>
          <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <span class="hello-text">Hello, <?= e($user['name'] ?? 'User') ?></span>
        <a class="btn btn-primary" href="<?= e(APP_URL) ?>/logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-ghost" href="<?= e(APP_URL) ?>/login.php">Login</a>
        <a class="btn btn-primary" href="<?= e(APP_URL) ?>/signup.php">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container" role="main" aria-label="Content">
  <?php if ($msg = flash('error')): ?>
    <div class="card" role="alert" style="border-left:4px solid #ff7043">
      <div class="content"><strong style="color:#ff7043">Error:</strong> <?= e($msg) ?></div>
    </div>
  <?php endif; ?>
  <?php if ($msg = flash('success')): ?>
    <div class="card" role="status" style="border-left:4px solid var(--color-primary)">
      <div class="content"><strong>Success:</strong> <?= e($msg) ?></div>
    </div>
  <?php endif; ?>
</main>
