<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_role('farmer');
verify_csrf();

$name = trim($_POST['name'] ?? '');
$price = (float)($_POST['price'] ?? 0);
$unit = trim($_POST['unit'] ?? 'kg');
$allowedUnits = ['kg','g','mg','l','ml','t','ton','tonne','litre','bunch','pack'];
if (!in_array($unit, $allowedUnits, true)) { $unit = 'kg'; }
$stock = (int)($_POST['stock'] ?? 0);
$category = trim($_POST['category'] ?? '');
$description = trim($_POST['description'] ?? '');
$imagePath = null;

if (!empty($_FILES['image']['name'])) {
  $dir = __DIR__ . '/../assets/img/uploads';
  if (!is_dir($dir)) mkdir($dir, 0777, true);
  $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
  $fname = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . preg_replace('/[^a-z0-9]+/i','',$ext);
  $dest = $dir . '/' . $fname;
  if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
    $imagePath = 'assets/img/uploads/' . $fname;
  }
}

if (!$name || !$price || !$stock || !$category) {
  flash('error','All fields required.');
  header('Location: ../farmer/products.php'); exit;
}

$pdo = Database::getConnection();
$st = $pdo->prepare("INSERT INTO products (farmer_id, name, price, unit, stock, category, description, image_path, active) VALUES (?,?,?,?,?,?,?, ?,1)");
$st->execute([current_user()['id'], $name, $price, $unit, $stock, $category, $description, $imagePath]);
flash('success','Product added!');
header('Location: ../farmer/products.php');
