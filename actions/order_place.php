<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_role('customer');
verify_csrf();

$cart = $_SESSION['cart'] ?? [];
if (!$cart) { flash('error','Cart is empty.'); header('Location: ../customer/cart.php'); exit; }

$address = trim($_POST['address'] ?? '');
$slot = trim($_POST['slot'] ?? 'Any');
$payment = trim($_POST['payment'] ?? 'Online (Simulated)');

$pdo = Database::getConnection();
$pdo->beginTransaction();
try {
  $total = 0.0;
  // group items by farmer for notifications
  $farmerTotals = [];
  foreach($cart as $it){
    $total += $it['price']*$it['qty'];
    // find farmer_id for product
    $st = $pdo->prepare("SELECT farmer_id FROM products WHERE id=? FOR UPDATE");
    $st->execute([$it['id']]);
    $row = $st->fetch();
    if ($row) {
      $farmerTotals[$row['farmer_id']] = ($farmerTotals[$row['farmer_id']] ?? 0) + ($it['price']*$it['qty']);
    }
  }
  $st = $pdo->prepare("INSERT INTO orders (customer_id, status, total_amount, address, delivery_slot, payment_method) VALUES (?,?,?,?,?,?)");
  $st->execute([current_user()['id'], 'placed', $total, $address, $slot, $payment]);
  $orderId = (int)$pdo->lastInsertId();

  $sti = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?,?,?,?)");
  foreach($cart as $it){
    $sti->execute([$orderId, $it['id'], $it['qty'], $it['price']]);
    // reduce stock
    $pdo->prepare("UPDATE products SET stock = GREATEST(stock - ?,0) WHERE id=?")->execute([$it['qty'], $it['id']]);
  }

  // notifications for farmers
  $stn = $pdo->prepare("INSERT INTO notifications (farmer_id, type, payload) VALUES (?,?,?)");
  foreach($farmerTotals as $fid=>$amount){
    $payload = json_encode(['order_id'=>$orderId,'amount'=>$amount,'customer_id'=>current_user()['id']]);
    $stn->execute([$fid,'new_order',$payload]);
  }

  $pdo->commit();
  unset($_SESSION['cart']);
  flash('success','Order placed successfully!');
  header('Location: ../customer/orders.php');
} catch(Exception $e){
  $pdo->rollBack();
  flash('error','Failed to place order.');
  header('Location: ../customer/checkout.php');
}
