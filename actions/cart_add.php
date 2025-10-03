<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
verify_csrf();

$id = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT id, name, price, image_path FROM products WHERE id = ? AND active=1");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { flash('error','Product not found.'); header('Location: ../products/catalog.php'); exit; }

$_SESSION['cart'] = $_SESSION['cart'] ?? [];
if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['qty'] += $qty;
else $_SESSION['cart'][$id] = ['id'=>$id,'name'=>$p['name'],'price'=>$p['price'],'image'=>$p['image_path'],'qty'=>$qty];

flash('success','Added to cart.');
header('Location: ../customer/cart.php');
